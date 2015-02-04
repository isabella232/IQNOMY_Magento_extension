<?php
/**
 * IQNOMY Helper
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var array
     */
    protected $_webservice = array();

    /**
     * @var array
     */
    protected $_dimensions = array();
    
    /**
     *
     * @var int|null
     */
    protected $_categoryDepth = null;

    /**
     * Return IQNOMY webservice object with the API configuration for the given storeview.
     *
     * @param mixed $store
     * @return IQNOMY_Extension_Model_Webservice
     */
    public function getWebservice($store = null)
    {
        $storeId = (int)Mage::app()->getStore($store)->getId();

        if (!isset($this->_webservice[$storeId])) {
            /** @var IQNOMY_Extension_Model_Webservice $webservice */
            $webservice = Mage::getModel('iqnomy_extension/webservice');
            
            $webservice->setAccountInfo(
                'live',
                // Mage::getStoreConfig('iqnomy_extension/account/environment', $store),
                Mage::getStoreConfig('iqnomy_extension/account/username',    $store),
                Mage::getStoreConfig('iqnomy_extension/account/tenant_id',   $store),
                Mage::getStoreConfig('iqnomy_extension/account/api_key',     $store)
            );

            $this->_webservice[$storeId] = $webservice;
        }

        return $this->_webservice[$storeId];
    }
    
    public function validateConfig()
    {
        try
        {
            $webservice = $this->getWebservice();
            try 
            {
                $webservice->getContainers();
            }
            catch(Exception $ex)
            {
                throw new Exception($ex->getMessage());//"Your IQNOMY configuration is not valid.");
            }
            
            return null;
        }
        catch(Exception $ex)
        {
            return Mage::helper('iqnomy_extension')->__($ex->getMessage());
        }
    }

    /**
     * Get the depth-level of categories that should be tracked and synchronized.
     * 
     * @param mixed $store
     * @return int
     */
    public function getCategoryDepth($store = null)
    {
        return 100;
//        if($this->_categoryDepth == null)
//        {
//            $this->_categoryDepth = Mage::getStoreConfig('iqnomy_extension/dimensions/category_depth', $store);
//        }
//        
//        return $this->_categoryDepth;
    }

    public function encode($array)
    {
        if(is_array($array))
        {
            foreach($array as $key => $value)
            {
                $array[$key] = $this->encode($value);
            }
        }
        else
        {
            return htmlentities($array);
        }
    }
    
    /**
     * Return the configured product dimensions.
     *
     * @param mixed $store
     * @return array
     */
    public function getConfiguredProductDimensions($store = null)
    {
        $storeId = (int)Mage::app()->getStore($store)->getId();

        if (!isset($this->_dimensions[$storeId])) {
            if (!Mage::getStoreConfigFlag('iqnomy_extension/dimensions/product', $store)) {
                // default
                $dimensions = $this->getDefaultProductDimensions($store);
            }
            else {
                // custom
                $dimensions = array();
                foreach (explode(',', Mage::getStoreConfig('iqnomy_extension/dimensions/product_custom', $store)) as $_attributeCode) {
                    $dimensions[] = $_attributeCode;
                }
            }

            $this->_dimensions[$storeId] = $dimensions;
        }

        return $this->_dimensions[$storeId];
    }

    /**
     * Return the default product dimensions. These are all visible and comparable attributes, except textareas.
     *
     * @param mixed $store
     * @return array
     */
    public function getDefaultProductDimensions($store = null)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributeCollection */
        $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection');

        $attributeCollection->getSelect()
            ->where('is_visible = 1')
            ->where('is_visible_on_front = 1 OR is_comparable = 1')
            ->where('frontend_input != ?', 'textarea')
            ->order(array('position', 'attribute_code'));

        $result = array();
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $_attribute */
        foreach ($attributeCollection as $_attribute) {
            $result[] = $_attribute->getAttributeCode();
        }

        return $result;
    }

    /**
     * Get the dimension values for the given product, if loaded.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductDimensions($product)
    {
        $result = array();

        $result['category_id'] = implode(',', $product->getCategoryIds());

        foreach ($this->getConfiguredProductDimensions($product->getStore()) as $_dimension) {
            $value = $product->getData($_dimension);
            if (!is_null($value)) {
                $result[$_dimension] = $value;
            }
        }

        return $result;
    }

    /**
     * Track an event using the REST API.
     *
     * @param array $iqEventData
     * @param null|string $externalVisitorId
     * @param mixed $store
     */
    public function trackEvent($iqEventData, $externalVisitorId = null, $store = null)
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = Mage::app()->getRequest();

        // IQNOMY: removed Do-Not-Track check at the request of IQNOMY
        /*
        // check for Do-Not-Track header
        if ($request->getHeader('DNT') && !Mage::getStoreConfig('iqnomy_extension/tracking/ignore_dnt', $store)) {
            return;
        }
        */

        // retrieve IQNOMY cookies from request
        $visitorId = $request->getCookie('_iqnomyvid');
        $followId  = $request->getCookie('_iqnomyfid');
        if (is_null($visitorId) || is_null($followId)) {
            return;
        }

        // if no external visitor id is specified, use email address for logged in customer
        if (is_null($externalVisitorId)) {
            if ($customer = Mage::helper('customer')->getCustomer()) {
                $externalVisitorId = $customer->getEmail();
            }
        }

        // on which url did the event occur?
        $eventUrl = Mage::helper('core/url')->getCurrentUrl();
        if ($url = $request->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_REFERER_URL)) {
            $eventUrl = $url;
        }
        if ($url = $request->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_BASE64_URL)) {
            $eventUrl = Mage::helper('core')->urlDecode($url);
        }
        if ($url = $request->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_URL_ENCODED)) {
            $eventUrl = Mage::helper('core')->urlDecode($url);
        }

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $this->getWebservice($store);

        $webservice->trackEvent(
            $visitorId,
            $followId,
            $eventUrl,
            $iqEventData,
            $externalVisitorId
        );
    }
    
//    public function getContainerObject($data)
//    {
//        $object = new Varien_Object();
//        
//        $object->setId($data["id"]);
//        $object->setName($data["name"]);
//        $object->setActiveFrom($data["activateDate"]);
//        $object->setEscalationType($data["escalationType"]);
//        
//        return $object;
//    }
    
    public function bannersEnabled()
    {
        return ($this->moduleEnabled("Fishpig_iBanners") || $this->moduleEnabled("IQNOMY_Banner"));
    }
    
    public function moduleEnabled($module)
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        if($modules != null && array_key_exists($module, $modules))
        {
            $module = (array)$modules[$module];
            if($module != null && array_key_exists("active", $module) && $module["active"])
            {
                return true;
            }
        }
        return false;
    }
    
    public function getBaseUrl($type = null)
    {
	if(Mage::getSingleton('adminhtml/session')->getIqnomyStoreId() != null)
	{
	    $storeId = Mage::getSingleton('adminhtml/session')->getIqnomyStoreId();
	    if($type == null)
	    {
		return Mage::app()->getStore($storeId)->getBaseUrl();
	    }
	    else
	    {
		return Mage::app()->getStore($storeId)->getBaseUrl($type);
	    }
	}
	else
	{
	    if($type == null)
	    {
		return Mage::getBaseUrl();
	    }
	    else
	    {
		return Mage::getBaseUrl($type);
	    }
	}
    }
}
