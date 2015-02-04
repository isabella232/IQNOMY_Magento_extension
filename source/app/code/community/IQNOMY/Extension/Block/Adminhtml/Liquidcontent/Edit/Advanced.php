<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent_Edit_Advanced extends Mage_Adminhtml_Block_Abstract
{
    public function getForm($baseBlock)
    {
        $data = Mage::getSingleton("iqnomy_extension/liquidcontent")->getData();
         
	if($this->getRequest()->getParam('container'))
	{
	    if(!array_key_exists("container_id", $data) || $data["container_id"] == null)
	    {
		$data["container_id"] = $this->getRequest()->getParam('container');
	    }
	    $action = $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'), 'container' => $this->getRequest()->getParam('container')));
	}
	else
	{
	    $action = $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id')));
	}
        
        //Setting up form.
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $action,
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
         
        //Creating the first fieldset with basic information.
        $fieldset = $form->addFieldset('liquid_content_general', array(
             'legend' => Mage::helper('iqnomy_extension')->__("General")
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

        //The content name, just to help the user to remember which is which.
        $fieldset->addField('name', 'text', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Name"),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'name'
        ));
        
        //Loading all existing banners.
        $values = array();
        $containerCollection = Mage::getSingleton("iqnomy_extension/container")->getCollection();
        foreach($containerCollection as $container)
        {
            $values[$container->getId()] = $container->getName();
        }

        $fieldset->addField('container_id', 'select', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Container"),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'container_id',
             'values'    => $values
        ));
        
        $fieldset = $form->addFieldset('liquid_content_content', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Content")
        ));
        
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $values = array();
        $values[""] = Mage::helper('iqnomy_extension')->__("None");
        
        foreach($products as $product)
        {
            $values[$product->getId()] = $product->getName();
        }
        
        $searchButton = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => Mage::helper('iqnomy_extension')->__("Search"),
            'onclick' => 'openProductChooser();'
        ));
        
        $productOptions = array(
            'label'     => Mage::helper('iqnomy_extension')->__("Product"),
            'name'      => 'product_id',
            'values'    => $values,
            'onchange' => 'productChanged(this);',
            'after_element_html' => $searchButton->toHtml()
        );
        
        $productOptions["note"] = Mage::helper('iqnomy_extension')->__('You can use the following items in your html template: ${imageUrl} for the product image, ${title} for the product name, ${url} for the url to the product page & ${price} for the product price.');
        
        $fieldset->addField("product_id", 'select', $productOptions);
        
        $imageFieldOptions = array(
            'label'     => Mage::helper('iqnomy_extension')->__('Content image (.jpg, .jpeg, .gif, .png)'),
            'name' => 'image',
            'value' => 'image',
	    'onchange' => 'updateImageLabel();',
            'readonly' => true
        );
        
        $imageFieldOptions["note"] = Mage::helper('iqnomy_extension')->__('You can use ${imageUrl} in your html code to use this field.');
        
        if(!array_key_exists("image_path", $data) || !file_exists(Mage::getBaseDir('media') . "/iqnomy" . $data["image_path"]))
        {
            //$imageFieldOptions["class"] = "required-entry";
            
            $buttonTitle = Mage::helper('iqnomy_extension')->__('Select image');
        }
        else
        {
            $fieldset->addField('image_path', 'hidden', array(
                'name' => 'image_path',
                'value' => $data["image_path"]
            ));
            
            $buttonTitle = Mage::helper('iqnomy_extension')->__('Change image');
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
            'button_text' => $buttonTitle,
            'image_title' => Mage::helper('iqnomy_extension')->__('Current image'),
            'required' => false,
            'current_image' => $currentImage,
            'note' => Mage::helper('iqnomy_extension')->__('You can use ${imageUrl} in your html code to use this field.')
        ));
        
        $openBrowserButton->setRenderer($this->getLayout()->createBlock('iqnomy_extension/adminhtml_liquidcontent_edit_renderer_button'));
        
        $titleOptions = array(
            'label' => Mage::helper('iqnomy_extension')->__('Title'),
            'name' => 'title',
            'class'     => 'required-entry',
            'required'  => true
        );
        
        $titleOptions['note'] = Mage::helper('iqnomy_extension')->__('You can use ${title} in your html code to view the title.');
        
        $fieldset->addField('title', 'text', $titleOptions);
        
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

        $urlOptions["note"] = Mage::helper('iqnomy_extension')->__('You can use ${url} in your html code to use this field.');
        
        $fieldset->addField('url', 'text', $urlOptions);
        
        if(!array_key_exists("html", $data))
        {
            $data["html"] = '<a href="${url}" title="${title}">'.PHP_EOL.'<img src="${imageUrl}" alt="${title}">'.PHP_EOL.'</a>';
        }

        $fieldset->addField('html', 'textarea', array(
            'name' => 'html',
            'label' => Mage::helper('iqnomy_extension')->__("HTML template"), 
            'class' => 'required-entry',
            'style' => 'width:700px; height:250px;',
            'required' => true, 
        ));
        
        //Creating the fieldset for the data required for the content matching.
        $fieldset = $form->addFieldset('liquid_content_dimensions', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Areas of interest")
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

        $form->setValues($data);
        
        return $form;
    }
}
