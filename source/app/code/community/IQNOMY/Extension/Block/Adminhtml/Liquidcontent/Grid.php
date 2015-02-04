<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent_Grid extends Mage_Adminhtml_Block_Widget
{
    private $_collection;
    
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate("iqnomy_extension/liquidcontent/grid.phtml");
                	
        try
        {
	    if($this->getRequest()->getParam('container') != null)
	    {
		$container = Mage::getSingleton("iqnomy_extension/container")->load($this->getRequest()->getParam('container'));
		if($container == null)
		{
		    throw new Exception();
		}
		
		$this->_collection = $container->getLiquidContents();
	    }
	    else
	    {
		$this->_collection = Mage::getSingleton("iqnomy_extension/liquidcontent")->getCollection();
	    }
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iqnomy_extension')->__("An error occurred while loading the liquid contents."));
            $this->_collection = new Varien_Data_Collection();
        }
    }
    
    public function _prepareLayout()
    {        
        parent::_prepareLayout();
    }
    
    public function getCollection()
    {
        return $this->_collection;
    }
    
    public function getDuplicateUrl($row)
    {
	if($this->getRequest()->getParam('container'))
	{
	    return $this->getUrl('*/*/duplicate', array('id' => $row->getId(), 'container' => $this->getRequest()->getParam('container')));
	}
        return $this->getUrl('*/*/duplicate', array('id' => $row->getId()));
    }
    
    public function getRowUrl($row)
    {
	if($this->getRequest()->getParam('container'))
	{
	    return $this->getUrl('*/*/edit', array('id' => $row->getId(), 'container' => $this->getRequest()->getParam('container')));
	}
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}