<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{    
    protected function _prepareForm()
    {       
        $data = Mage::getSingleton("iqnomy_extension/liquidcontent")->getData();
         
        if($this->getRequest()->getParam('case') != null)
        {
            $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('case'));
            $formBlock = $this->getLayout()->createBlock("iqnomy_extension/adminhtml_liquidcontent_edit_".$case->getFormType());
        }
        else
        {
            $formBlock = $this->getLayout()->createBlock("iqnomy_extension/adminhtml_liquidcontent_edit_advanced");
        }
        
        $form = $formBlock->getForm($this);
        
        $form->setUseContainer(true);
        $this->setForm($form);
       
        return parent::_prepareForm();
    }
    
    protected function _toHtml()
    {
        $html = parent::_toHtml();
	$html .= $this->getLayout()->createBlock("iqnomy_extension/adminhtml_liquidcontent_chooser")->toHtml();
	$html .= $this->getLayout()->createBlock("iqnomy_extension/adminhtml_liquidcontent_image_chooser")->toHtml();
	
	return $html;
    }
    
    public function getDimensionPropertyId($dimensions, $dimensionId, $value)
    {
        if($dimensions != null)
        {
            foreach($dimensions as $dimension)
            {
                if($dimension["id"] == $dimensionId)
                {
                    foreach($dimension["dimensionProperty"] as $property)
                    {
                        if($property["value"] == $value)
                        {
                            return $property["id"];
                        }
                    }
                }
            }
        }
        return null;
    }
}


    