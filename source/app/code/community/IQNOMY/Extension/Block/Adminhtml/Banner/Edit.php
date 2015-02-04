<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();   
			   
        $this->_objectId = 'id';
        $this->_blockGroup = 'iqnomy_extension';
        $this->_controller = 'adminhtml_banner';
 
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
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        if ($data && array_key_exists("id", $data) && $data["id"])
        {
            if(array_key_exists("name", $data))
            {
                return Mage::helper('iqnomy_extension')->__("Edit liquid internet case: ".$data["name"]);
            }
            return Mage::helper('iqnomy_extension')->__("Edit liquid internet case");
        }
        else
        {
            return Mage::helper('iqnomy_extension')->__("New liquid internet case");
        }
    }
}