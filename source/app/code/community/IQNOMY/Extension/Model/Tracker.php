<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Tracker
{
    /**
     * Get HTML JavaScript snippet which tracks event data on frontend. Can be used in
     * AJAX response.
     *
     * @param array $eventData
     * @return string
     */
    public function getTrackEventScript($eventData)
    {
        return "<script type=\"text/javascript\">\n"
             . "\tif (typeof _iqsHelper != 'undefined') {\n"
             . "\t\t_iqsHelper.trackEvent(" . Zend_Json::encode(Mage::helper('iqnomy_extension')->encode($eventData)) . ");\n"
             . "\t}\n"
             . "</script>\n";
    }

    /**
     * Collect event data for the current request. First finds out the page_type by looking at the
     * current layout update handles and comparing them to the iqnomy_extension/page_types config node.
     * When the page_type is determined, an event is dispatched to collect additional event data,
     * for example: iqnomy_extension_collect_event_data_compare
     *
     * @param Mage_Core_Controller_Varien_Action $action
     * @return array
     */
    public function collectEventData($action)
    {	
        $result = array();

        // find out the current page type
        $layoutHandles = $action->getLayout()->getUpdate()->getHandles();
        $pageTypeConfig = Mage::getConfig()->getNode('iqnomy_extension/page_types')->asArray();
        foreach ($pageTypeConfig as $_pageType => $_pageTypeConfig) {
            if (isset($_pageTypeConfig['layout_handles']) && is_array($_pageTypeConfig['layout_handles'])) {
                if (array_intersect(array_keys($_pageTypeConfig['layout_handles']), $layoutHandles)) {
                    $result['page_type'] = $_pageType;
                    break;
                }
            }
        }

        // dispatch events
        $transportObject = new Varien_Object(array(
            'result' => $result
        ));
        Mage::dispatchEvent('iqnomy_extension_collect_event_data', array(
            'action'    => $action,
            'transport' => $transportObject
        ));
        $result = $transportObject->getResult();

        if (isset($result['page_type'])) {
            Mage::dispatchEvent("iqnomy_extension_collect_event_data_{$result['page_type']}", array(
                'action'    => $action,
                'transport' => $transportObject
            ));
        }
        $result = $transportObject->getResult();

        if(isset($result['category_id']))
        {
            $categories = array();
            $category = Mage::getModel("catalog/category")->load($result['category_id']);
            $level = $category->getLevel();
            
            $result["level_".$level."_category"] = $category->getId();
            
            while($level > 1)
            {
                $category = $category->getParentCategory();
                $level = $category->getLevel();
                if($level > 1)
                {
                    $result["level_".$level."_category"] = $category->getId();
                }
            }
        }
        
        $subscriptionHash = Mage::getSingleton('customer/session')->getSubscriptionHash();
        if($subscriptionHash != null)
        {
            $result["newsletter"] = true;
            $result["emailhash"] = $subscriptionHash;
            
            Mage::getSingleton('customer/session')->setSubscriptionHash(null);
        }
        
        return $result;
    }

    /**
     * Observes controller_action_layout_render_before event. Called before layout is rendered.
     * Inserts the IQImpressor tracker script on each page.
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerActionLayoutRenderBefore($observer)
    {	
        if (Mage::app()->getRequest()->isAjax()) {
            // never include the IQImpressor tracker script on ajax responses
            return;
        }

        /** @var Mage_Core_Model_Layout $layout */
        $layout = Mage::app()->getLayout();

        /** @var Mage_Core_Block_Text_List $beforeBodyEndBlock */
        if ($beforeBodyEndBlock = $layout->getBlock('before_body_end')) {

            /** @var IQNOMY_Extension_Block_Tracker $trackerBlock */
            $trackerBlock = $layout->createBlock('iqnomy_extension/tracker', 'iqnomy.tracker');

            $beforeBodyEndBlock->append($trackerBlock);
        }
    }

    /**
     * Observes core_block_abstract_to_html_after event. Called after a block is rendered.
     * Adds event data to layered navigation block, based on selected filters.
     * Adds event data to toolbar block, based on selected order and direction.
     * This is also included in ajax response.
     *
     * @param Varien_Event_Observer $observer
     */
    public function coreBlockAbstractToHtmlAfter($observer)
    {
        /** @var Mage_Core_Block_Abstract $block */
        $block = $observer->getBlock();

        $eventData = array();

        if ($block instanceof Mage_Catalog_Block_Layer_View) {
            /** @var Mage_Catalog_Block_Layer_View $block */
            $dimensions = Mage::helper('iqnomy_extension')->getConfiguredProductDimensions();

            /** @var Mage_Catalog_Block_Layer_Filter_Category $categoryFilter */
            if ($categoryFilter = $block->getChild('category_filter')) {
                // request parameter 'cat' is defined in Mage_Catalog_Model_Layer_Filter_Category
                if ($categoryId = $block->getRequest()->getParam('cat')) {
                    $eventData['filter'] = 'true';
                    $eventData['category_id'] = $categoryId;
                }
            }

            try {
                /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $filterableAttributes */
                $filterableAttributes = $block->getData('_filterable_attributes');
                if (is_null($filterableAttributes)) {
                    $filterableAttributes = $block->getLayer()->getFilterableAttributes();
                }

                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $_attribute */
                foreach ($filterableAttributes as $_attribute) {
                    $_attributeCode = $_attribute->getAttributeCode();
                    if (in_array($_attributeCode, $dimensions)) {
                        $_filterValue = $block->getRequest()->getParam($_attributeCode);
                        if (!is_null($_filterValue)) {
                            $eventData['filter'] = 'true';
                            $eventData[$_attributeCode] = $_filterValue;
                        }
                    }
                }
            }
            catch (Exception $e) {
                $forceLog = Mage::getStoreConfigFlag('iqnomy_extension/account/enable_logging');
                Mage::log("\n" . $e->__toString(), Zend_Log::ERR, 'iqnomy.log', $forceLog);
            }
        }
        elseif ($block instanceof Mage_Catalog_Block_Product_List) {
            /** @var Mage_Catalog_Block_Product_List $block */

            if ($toolbarBlock = $block->getChild($block->getToolbarBlockName())) {
                $eventData['order']     = $toolbarBlock->getCurrentOrder();
                $eventData['direction'] = $toolbarBlock->getCurrentDirection();
            }
        }

        if ($eventData) {
            $transport = $observer->getTransport();
            $transport->setHtml($transport->getHtml() . $this->getTrackEventScript($eventData));
        }
    }

    /**
     * Observes sales_quote_config_get_product_attributes event. Called when product attributes for
     * cart items are loaded. Loads configured dimensions as additional product attributes for cart
     * items.
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesQuoteConfigGetProductAttributes($observer)
    {
        $transfer = $observer->getAttributes();
        foreach (Mage::helper('iqnomy_extension')->getConfiguredProductDimensions() as $_dimension) {
            $transfer->setData($_dimension, '');
        }
    }

    /**
     * Observes customer_register_success event. Called when a user creates a customer account.
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerRegisterSuccess($observer)
    {
        try {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = $observer->getCustomer();

            /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

            $_helper->trackEvent(array(
                'account' => 'register'
            ), $customer->getEmail());
        }
        catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Observes customer_login event. Called when a customer logs in.
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerLogin($observer)
    {
        try {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = $observer->getCustomer();

            /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

            $_helper->trackEvent(array(
                'account' => 'login'
            ), $customer->getEmail());
        }
        catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Observes newsletter_subscriber_save_after event. Called when newsletter subscriber model is
     * saved.
     *
     * @param Varien_Event_Observer $observer
     */
    public function newsletterSubscriberSaveAfter($observer)
    {
        try {
            /** @var Mage_Newsletter_Model_Subscriber $subscriber */
            $subscriber = $observer->getDataObject();

            $successStatuses = array(
                Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE,
                Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
            );

            $oldStatus = $subscriber->getOrigData('subscriber_status');
            $newStatus = $subscriber->getSubscriberStatus();

            if (!in_array($oldStatus, $successStatuses) && in_array($newStatus, $successStatuses)) {
                // user subscribed to newsletter, or has the intention to

                /** @var IQNOMY_Extension_Helper_Data $_helper */
                $_helper = Mage::helper('iqnomy_extension');

                $_helper->trackEvent(array(
                    'newsletter' => 'true'
                ), $subscriber->getSubscriberEmail());
            }
        }
        catch (Exception $exception) {
            Mage::logException($exception);
        }
    }
    
    /**
     * Observes controller_action_postdispatch_contacts_index_post event. Called when contact form is
     * submitted.
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerActionPostdispatchContactsIndexPost($observer)
    {
        try {
            /** @var Mage_Contacts_IndexController $controllerAction */
            $controllerAction = $observer->getControllerAction();

            /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

            $_helper->trackEvent(array(
                'contactform' => 'true'
            ), $controllerAction->getRequest()->getPost('email'));
        }
        catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Called after the cart contents have changed.
     */
    protected function _trackCartChanged()
    {
        try {
            /** @var Mage_Checkout_Model_Cart $cart */
            $cart = Mage::getSingleton('checkout/cart');

            $orderrows = array();
            $subtotal = 0.0;
            /** @var Mage_Sales_Model_Quote_Item $_item */
            foreach ($cart->getItems() as $_item) {
                if (!$_item->isDeleted() && !$_item->getParentItemId()) {
                    $orderrows[] = array(
                        'product_id' => (int) $_item->getProductId(),
                        'quantity'   => $_item->getQty(),
                        'price'      => (float) $_item->getBasePriceInclTax()
                    );
                    $subtotal += $_item->getBasePriceInclTax() * $_item->getQty();
                }
            }

            $eventData = array(
                'cart_changed' => 'true',
                'orderrows'    => Zend_Json::encode($orderrows),
                'subtotal'     => $subtotal
            );

            /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

            $_helper->trackEvent($eventData);
        }
        catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Observes checkout_cart_add_product_complete event. Called after the visitor has added an item
     * to the cart.
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkoutCartAddProductComplete($observer)
    {
        $this->_trackCartChanged();
    }

    /**
     * Observes checkout_cart_update_items_after event. Called after the visitor has updated cart
     * items.
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkoutCartUpdateItemsAfter($observer)
    {
        $this->_trackCartChanged();
    }

    /**
     * Observes sales_quote_remove_item event. Called after the visitor has removed an item from
     * the cart.
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesQuoteRemoveItem($observer)
    {
        $this->_trackCartChanged();
    }

    /**
     * Observes checkout_type_onepage_save_order_after event. Called after customer has placed an
     * order.
     *
     * @param Varien_Event_Observer $observer
     */
    public function checkoutTypeOnepageSaveOrderAfter($observer)
    {
        try {
            /** @var Mage_Sales_Model_Order $order */
            $order = $observer->getOrder();

            /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

	    $trackData = array(
                'checkout' => 'true'
            );
	    
	    $trackData['used_coupon_code'] = ($order->getCouponCode() == null ? 'false' : 'true');
	    $trackData['coupon_code'] = ($order->getCouponCode() == null ? '' : $order->getCouponCode());
	    
            $_helper->trackEvent($trackData, $order->getCustomerEmail());
        }
        catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Observes iqnomy_extension_collect_event_data_overview event. Called when additional event data
     * for the category is collected.
     *
     * @param Varien_Event_Observer $observer
     */
    public function collectEventDataOverview($observer)
    {
        /** @var array $result */
        $result = $observer->getTransport()->getResult();

        /** @var Mage_Core_Model_Category $category */
        if ($category = Mage::registry('current_category')) {
            $result['category_id'] = $category->getId();
        }

        $observer->getTransport()->setResult($result);
    }

    /**
     * Observes iqnomy_extension_collect_event_data_detail event. Called when additional event data for
     * the product detail page is collected.
     *
     * @param Varien_Event_Observer $observer
     */
    public function collectEventDataDetail($observer)
    {
        /** @var array $result */
        $result = $observer->getTransport()->getResult();
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var Mage_Core_Model_Product $product */
        if ($product = Mage::registry('current_product')) {
            $result = array_merge(
                $result,
                array('product_id' => $product->getId()),
                $_helper->getProductDimensions($product)
            );
        }
        /** @var Mage_Core_Model_Category $category */
        if ($category = Mage::registry('current_category')) {
            $result['category_id'] = $category->getId();
        }

        $observer->getTransport()->setResult($result);
    }

    /**
     * Observes iqnomy_extension_collect_event_data_compare event. Called when additional event data for
     * the compare page is collected.
     *
     * @param Varien_Event_Observer $observer
     */
    public function collectEventDataCompare($observer)
    {
        /** @var array $result */
        $result = $observer->getTransport()->getResult();
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        $products = array();
        foreach (Mage::helper('catalog/product_compare')->getItemCollection() as $_product) {
            $products[] = array_merge(
                array('product_id' => $_product->getId()),
                $_helper->getProductDimensions($_product)
            );
        }
        $result['products'] = Zend_Json::encode($products);

        $observer->getTransport()->setResult($result);
    }

    /**
     * Observes iqnomy_extension_collect_event_data_wishlist event. Called when additional event data for
     * the wishlist page is collected.
     *
     * @param Varien_Event_Observer $observer
     */
    public function collectEventDataWishlist($observer)
    {
        /** @var Mage_Core_Controller_Varien_Action $action */
        $action = $observer->getAction();
        /** @var array $result */
        $result = $observer->getTransport()->getResult();

        /** @var Mage_Wishlist_Block_Customer_Wishlist $block */
        if ($block = $action->getLayout()->getBlock('customer.wishlist')) {

            /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

            $products = array();
            /** @var  Mage_Wishlist_Model_Item $_item */
            foreach ($block->getWishlistItems() as $_item) {
                $products[] = array_merge(
                    array('product_id' => $_item->getProductId()),
                    $_helper->getProductDimensions($_item->getProduct())
                );
            }
            $result['products'] = Zend_Json::encode($products);
        }

        $observer->getTransport()->setResult($result);
    }

    /**
     * Observes iqnomy_extension_collect_event_data_search event. Called when additional event data for
     * the search result page is collected.
     *
     * @param Varien_Event_Observer $observer
     */
    public function collectEventDataSearch($observer)
    {
        /** @var Mage_Core_Controller_Varien_Action $action */
        $action = $observer->getAction();
        /** @var array $result */
        $result = $observer->getTransport()->getResult();

        if ($action->getRequest()->getControllerName() != 'advanced') {
            $result['search'] = $action->getRequest()->getParam('q');
        }
        else {
            $result['search'] = $action->getRequest()->getParam('name');
            // TODO: include additional search fields (dimensions)
        }

        $observer->getTransport()->setResult($result);
    }
}
