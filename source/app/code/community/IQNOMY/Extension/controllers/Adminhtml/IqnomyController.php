<?php
/**
 * IQNOMY Admin Controller
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Adminhtml_IqnomyController extends Mage_Adminhtml_Controller_Action
{
    private $numberalNames = array(1 => "first", 2 => "second", 3 => "third", 4 => "fourth", 5 => "fifth", 6 => "sixth", 7 => "seventh", 8 => "eighth", 9 => "ninth", 10 =>  "tenth");
    
    public function _construct()
    {
	parent::_construct();
    }
    
    /**
     * Index action.
     */
    public function indexAction()
    {		
	if (!Mage::app()->isSingleStoreMode())
	{
	    $storeId = $this->getRequest()->getParam('store');
	    if($storeId != null && Mage::getSingleton('adminhtml/session')->getIqnomyStoreId() != $storeId)
	    {
		Mage::getSingleton('adminhtml/session')->setIqnomyStoreId($storeId);
		session_write_close();
		return $this->_redirect('*/*/*/store/'.$storeId);
	    }
	    else
	    {
		$store = null;
		if($storeId != null)
		{
		    $store = Mage::getModel('core/store')->load($storeId);
		    if($store == null || $store->getId() != $storeId)
		    {
			$store = null;
		    }
		}
		
		if($store == null)
		{
		    if(Mage::getSingleton('adminhtml/session')->getIqnomyStoreId() != null)
		    {
			$storeId = Mage::getSingleton('adminhtml/session')->getIqnomyStoreId();
			$store = Mage::getModel('core/store')->load($storeId);
			if($store == null || $store->getId() != $storeId)
			{
			    $storeId = null;
			}
		    }
		
		    if($storeId == null)
		    {
			$stores = array_values(Mage::app()->getStores());
			$storeId = $stores[0]->getId();
		    }
		    return $this->_redirect('*/*/*/store/'.$storeId);
		}
	    }
	}
	
        $this->_title(Mage::helper('iqnomy_extension')->__('IQNOMY Extension'));

        $this->loadLayout();

        $this->_setActiveMenu('iqnomy/iqnomy_extension1');

        $this->renderLayout();
    }

    /**
     * Synchronize action.
     */
    public function synchronizeAction()
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        try {
            // Note: If different IQNOMY accounts can be configured for each storeview, a loop is
            //       required here so attributes for each account are synced.

            /** @var IQNOMY_Extension_Model_Webservice $webservice */
            $webservice = $_helper->getWebservice();

            $success = array();
            $failed = array();
            
            $categoryDepth = $_helper->getCategoryDepth();
            $currentLevel = 2;
            while($currentLevel <= $categoryDepth)
            {
                $attribute = $this->getCategoryAttribute($currentLevel);
                $options = $this->getCategoryLevelOptions($currentLevel);
                
                if($options != null)
                {
                    try {
                        $result = $webservice->updateDimension(
                            $attribute["code"],
                            $attribute["label"],
                            true,
                            $options
                        );
                    }
                    catch (Mage_Core_Exception $exception) {
                        $result = false;
                    }

                    if ($result){
                        $success[] = $attribute["label"];
                    }
                    else {
                        $failed[] = $attribute["label"];
                    }
                }
                $currentLevel++;
            }
            
            $attributeCodes = $_helper->getConfiguredProductDimensions();

            foreach ($attributeCodes as $_attributeCode) {
                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                $attribute = Mage::getSingleton('eav/config')
                    ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $_attributeCode);

                if ($attribute->getEntityType()->getEntityTypeCode() != Mage_Catalog_Model_Product::ENTITY) {
                    continue;
                }
                if (!$attribute->usesSource()) {
                    continue;
                }

                $options = $attribute->getSource()->getAllOptions(false);
                if($options != null && count($options) > 0)
                {
                    // try to synchronize the dimension
                    $attributeCode = $attribute->getAttributeCode();

                    try {
                        $result = $webservice->updateDimension(
                            $attributeCode,
                            $attribute->getFrontendLabel(),
                            true,
                            $options
                        );
                    }
                    catch (Mage_Core_Exception $exception) {
                        $result = false;
                    }

                    if ($result) {
                        $success[] = $attributeCode;
                    }
                    else {
                        $failed[] = $attributeCode;
                    }
                }
            }

            if (count($success)) {
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess($_helper->__('Successfully synchronized dimensions: %s', implode(', ', $success)));
            }
            if (count($failed)) {
                Mage::getSingleton('adminhtml/session')
                    ->addError($_helper->__('Failed synchronizing dimensions: %s', implode(', ', $failed)));
            }
            
            if(count($success) == 0 && count($failed) == 0)
            {
                Mage::getSingleton('adminhtml/session')->addError($_helper->__("Nothing was found to syncronize."));
            }
        }
        catch (Mage_Core_Exception $exception) {
            Mage::logException($exception);
            Mage::getSingleton('adminhtml/session')
                ->addError($_helper->__('Error during synchronization: %s', $exception->getMessage()));
        }
        $this->_redirect('*/*');
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
	$session = Mage::getSingleton('admin/session');
        return ($session->isAllowed('iqnomy') || $session->isAllowed('system/iqnomy'));
    }
    
    private function getCategoryLevelOptions($level)
    {
        $categories = Mage::getSingleton('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addIsActiveFilter()
            ->addFieldToFilter('level', array('eq' => $level));
        
        if($categories == null || $categories->count() == 0)
        {
            return null;
        }
        
        $options = array();
        foreach($categories as $category)
        {
            $options[] = array("value" => $category->getId(), "label" => $category->getName());
        }
        return $options;
    }
    
    private function getCategoryAttribute($depth)
    {
        $numberalName = $depth;
        if(array_key_exists($depth, $this->numberalNames))
        {
            $numberalName = Mage::helper('iqnomy_extension')->__($this->numberalNames[$depth]);
        }
        
        return array("code" => "level_".$depth."_category", "label" => ucfirst($numberalName.Mage::helper('iqnomy_extension')->__("-level category")));
    }
}