<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent_Edit_Product extends Mage_Adminhtml_Block_Abstract
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
        
        $fieldset->addField('container_id', 'hidden', array(
            'name' => 'container_id'
        ));
            
        $fieldset->addField('activate_date', 'hidden', array(
            'name' => 'activate_date'
        ));
        
        $fieldset = $form->addFieldset('liquid_content_content', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Content")
        ));
        
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $values = array();
        $values["none"] = "";
        
        foreach($products as $product)
        {	   
	    if($product->getImage() != "no_selection" || $product->getSmallImage() != "no_selection" || $product->getThumbnail() != "no_selection")
	    {
		$values[$product->getId()] = $product->getName();
	    }
        }
        
        $searchButton = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => Mage::helper('iqnomy_extension')->__("Search"),
            'onclick' => 'openProductChooser();'
        ));
        
        $productOptions = array(
            'label'     => Mage::helper('iqnomy_extension')->__("Product"),
            'name'      => 'product_id',
            'values'    => $values,
	    'onchange'	=> 'productChanged(this);',
            'after_element_html' => $searchButton->toHtml() . '<div style="display: none;" id="product-no-images-message" class="validation-advice">'.Mage::helper('iqnomy_extension')->__("The product you selected has no images and can't be used.").'</div>'
        );
        
        $fieldset->addField("product_id", 'select', $productOptions);
                
        $fieldset->addField('html', 'hidden', array(
            'name' => 'html'
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
            $show = true;
            
            $code = $dimension["name"];
            $number = str_replace("level_", "", $code);
            $number = str_replace("_category", "", $number);
            if(strpos($code, "level_") === false || strpos($code, "_category") === false && $number <= 0)
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