<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Container_Grid extends Mage_Adminhtml_Block_Widget
{
    private $_collection;
    
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate("iqnomy_extension/container/grid.phtml");
                
        try
        {
            $this->_collection = Mage::getSingleton("iqnomy_extension/container")->getCollection();
        }
        catch(Exception $ex)
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iqnomy_extension')->__("An error occurred while loading the containers."));
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
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}