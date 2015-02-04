<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent_Edit_Slider extends Mage_Adminhtml_Block_Abstract
{
    public function getForm($baseBlock)
    {
        $data = Mage::getSingleton("iqnomy_extension/liquidcontent")->getData();

        $action = $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'), 'case' => $this->getRequest()->getParam('case')));
        $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('case'));

        if(!array_key_exists("container_id", $data))
        {
            $data["container_id"] = $case->getContainer()->getId();
        }

        if(!array_key_exists("activate_date", $data))
        {
            $data["activate_date"] = date("d-m-Y");
        }

        if(!array_key_exists("html", $data))
        {
            $data["html"] = $case->getHtmlTemplate();
        }
        
        //Setting up form.
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $action,
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
                
        $fieldset = $form->addFieldset('liquid_content_content', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Banner")
        ));
        
        if(array_key_exists("is_enabled", $data))
            $enabled = $data["is_enabled"];
        else
            $enabled = false;
        
        //Enabled field
        $fieldset->addField("is_enabled", "checkbox", array(
            'label' => Mage::helper('iqnomy_extension')->__("Enabled?"),
            'name' => 'is_enabled',
            'onclick'   => 'this.value = this.checked ? 1 : 0;',
            'checked' => $enabled
        ));
        
        $fieldset->addField('container_id', 'hidden', array(
            'name' => 'container_id'
        ));
            
        $fieldset->addField('activate_date', 'hidden', array(
            'name' => 'activate_date'
        ));
        
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('iqnomy_extension')->__('Title'),
            'name' => 'title',
            'class'     => 'required-entry',
            'required'  => true
        ));
        
        if(!array_key_exists("url", $data))
        {
            $data["url"] = Mage::helper('iqnomy_extension')->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        }
        
        $urlOptions = array(
            'label' => Mage::helper('iqnomy_extension')->__('URL'),
            'name' => 'url',
            'class'     => 'required-entry',
            'required'  => true
        );
        
        $fieldset->addField('url', 'text', $urlOptions);
        
        $values = array();
        $values[""] = Mage::helper('iqnomy_extension')->__('Open in the same window.');
        $values["_blank"] = Mage::helper('iqnomy_extension')->__('Open in a new tab or window.');
        
        $urlTarget = "";
        if(array_key_exists("url_target", $data))
        {
            $urlTarget = $data["url_target"];
        }
        
        $fieldset->addField("url_target", 'select', array(
            'label'     => Mage::helper('iqnomy_extension')->__('URL Target'),
            'name'      => "url_target",
            'values'    => $values,
            'value'     => $urlTarget
       ));
        
        $fieldset->addField('alt_text', 'text', array(
            'label' => Mage::helper('iqnomy_extension')->__('ALT Text'),
            'name' => 'alt_text'
        ));
        
        $imageFieldOptions = array(
            'label'     => Mage::helper('iqnomy_extension')->__('Image'),
            'name' => 'image',
            'value' => 'image',
            'readonly' => true,
            'onchange' => "updateImageLabel();",
            'class' => "hidden"
        );

        if(!array_key_exists("image_path", $data) || !file_exists(Mage::getBaseDir('media') . "/iqnomy" . $data["image_path"]))
        {
            //$imageFieldOptions["class"] = "required-entry";
            
        }
        else
        {
            $fieldset->addField('image_path', 'hidden', array(
                'name' => 'image_path',
                'value' => $data["image_path"]
            ));
        }
	
	$currentImage = null;
	if(array_key_exists("image", $data))
	{
	    $currentImage = $data["image"];
	}
        
        $fieldset->addField('image', 'file', $imageFieldOptions);
	$fieldset->addField('existing-image', 'hidden', array('name' => 'existing_image'));
        
        $openBrowserButton = $fieldset->addField('openfilebrowser', 'label', array(
            'label' => Mage::helper('iqnomy_extension')->__('Image'),
            'onclick'=>'document.getElementById("image").click();',
            'button_text' => Mage::helper('iqnomy_extension')->__('Upload image'),
            'image_title' => Mage::helper('iqnomy_extension')->__('Current image'),
            'required' => true,
            'current_image' => $currentImage
        ));
        
        $openBrowserButton->setRenderer($this->getLayout()->createBlock('iqnomy_extension/adminhtml_liquidcontent_edit_renderer_button'));
        
        $fieldset->addField('html', 'hidden', array(
            'name' => 'html'
        ));
        
        //Creating the fieldset for the data required for the content matching.
        $fieldset = $form->addFieldset('liquid_content_dimensions', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Areas of interest")
        ));
        
        $fieldset->addField('note', 'note', array(
            'text'     => Mage::helper('iqnomy_extension')->__("Select for which interests this banner should be shown to the visitor.")
        ));
        
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();        
        $dimensions = $webservice->getDimensions();
        
        if(!array_key_exists("dimensions", $data))
        {
            $data["dimensions"] = array();
            
            if(array_key_exists("properties", $data) && $data["properties"] != null)
            {
                foreach($data["properties"] as $property)
                {
                    $propertyId = $baseBlock->getDimensionPropertyId($dimensions, $property["dimensionId"], $property["value"]);
                    if($propertyId != null)
                    {
                        $data["dimensions"][$property["dimensionId"]] = $propertyId;
                    }
                }
            }
        }
        
        foreach($dimensions as $dimension)
        {
            $show = true;
            
            $code = $dimension["name"];
            $number = str_replace("level_", "", $code);
            $number = str_replace("_category", "", $number);
            if(strpos($code, "level_") === false || strpos($code, "_category") === false || $number <= 0)
            {
                $show = false;
            }
            
            if($show)
            {
                if(!array_key_exists($dimension["id"], $data["dimensions"]))
                {
                    $data["dimensions"][$dimension["id"]] = array("value" => "empty-option");
                }

                $data["dimension_".$dimension["id"]] = array("value" => $data["dimensions"][$dimension["id"]]);

                $values = array();
                $values[] = array("label" => Mage::helper('iqnomy_extension')->__("None"), "value" => "empty-option");
                foreach($dimension["dimensionProperty"] as $property)
                {
                    $values[] = array("label" => $property["label"], "value" => $property["id"]);
                }

                if(count($values) > 1)
                {
                    $label = $dimension["description"];
                    if($label == null)
                    {
                        $label = $dimension["name"];
                    }
                    $fieldset->addField("dimension_".$dimension["id"], 'select', array(
                        'label'     => $label,
                        'name'      => 'dimensions['.$dimension["id"].']',
                        'values'    => $values,
                        'value'     => $data["dimension_".$dimension["id"]]
                   ));
                }
            }
	    else
	    {
		if(array_key_exists($dimension["id"], $data["dimensions"]))
                {
                    $data["dimension_".$dimension["id"]] = $data["dimensions"][$dimension["id"]];
		    
		    $fieldset->addField("dimension_".$dimension["id"], 'hidden', array(
			'name' => 'dimensions['.$dimension["id"].']',
			'value' => $data["dimension_".$dimension["id"]]
		    ));
                }
	    }
        }

        $form->setValues($data);
	
        return $form;
    }
}