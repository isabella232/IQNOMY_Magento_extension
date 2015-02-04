<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();   
			   
        $this->_objectId = 'id';
        $this->_blockGroup = 'iqnomy_extension';
        $this->_controller = 'adminhtml_liquidcontent';
 
        $this->_mode = 'edit';
 
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }
 
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
 
    public function getHeaderText()
    {
        $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('case'));
        $data = Mage::getSingleton('iqnomy_extension/liquidcontent')->getData();
        if ($data && array_key_exists("id", $data) && $data["id"])
        {
            $title = "Edit liquid content";
            
            if($case != null && $case->getLiquidContentEdit() != null)
            {
                $title = $case->getLiquidContentEdit();
            }
            
            $title = Mage::helper('iqnomy_extension')->__($title);
            
            if(array_key_exists("name", $data))
            {
                $title .= ": ".$data["name"];
            }
            return $title;
        }
        else
        {            
            $title = "New liquid content";
            if($case != null && $case->getLiquidContentNew() != null)
            {
                $title = $case->getLiquidContentNew();
            }
            
            return Mage::helper('iqnomy_extension')->__($title);
        }
    }
    
    public function getDeleteUrl()
    {
        if($this->getRequest()->getParam('case'))
        {
            return $this->getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id'), 'case' => $this->getRequest()->getParam('case')));
        }
	elseif($this->getRequest()->getParam('container_id'))
	{
	    return $this->getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id'), 'container_id' => $this->getRequest()->getParam('container_id')));
	}
        return parent::getDeleteUrl();
    }
    
    public function getBackUrl()
    {
        if($this->getRequest()->getParam('case'))
        {
            return $this->getUrl('*/case/index', array('id' => $this->getRequest()->getParam('case')));
        }
	elseif($this->getRequest()->getParam('container_id'))
	{
	    return $this->getUrl('*/*/index', array('container_id' => $this->getRequest()->getParam('container_id')));
	}
        return parent::getBackUrl();
    }
}