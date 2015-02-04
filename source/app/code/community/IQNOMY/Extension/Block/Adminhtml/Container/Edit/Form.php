<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Container_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{    
    protected function _prepareForm()
    {        
        $data = Mage::getSingleton("iqnomy_extension/container")->getData();
        
        //Setting up the edit form.
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
 
        $this->setForm($form);
 
        //Creating the fieldset for the container data.
        $fieldset = $form->addFieldset('container_form_general', array(
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
		
        //Name field, just for recognition by the user.
        $fieldset->addField('name', 'text', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Name"),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'name',
        ));
	
	$fieldset = $form->addFieldset('impression_placement', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Placement")
        ));
        
        //The HTML-id of the banner html-element in which 
        $fieldset->addField('html_id', 'text', array(
             'label'     => Mage::helper('iqnomy_extension')->__("HTML-id"),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'html_id',
             'note'      => Mage::helper('iqnomy_extension')->__("Enter the HTML-id of the HTML-element in which the liquid content should be placed.")
        ));
        
        $fieldset->addField('height', 'text', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Height"),
             'class'     => 'required-entry validate-number',
             'required'  => true,
             'note'      => Mage::helper('iqnomy_extension')->__("Enter the height in pixels. This height is required to avoid screen flashes."),
             'name'      => 'height',
        ));
        
        $fieldset = $form->addFieldset('impression_animation', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Animation")
        ));
        
        $values = array();
        $values[IQNOMY_Extension_Model_Banner::animationTypeNone] = Mage::helper('iqnomy_extension')->__("Show the liquid contents next to eachother.");
        $values[IQNOMY_Extension_Model_Banner::animationTypeSlide] = Mage::helper('iqnomy_extension')->__("Slide through the liquid contents.");
        $values[IQNOMY_Extension_Model_Banner::animationTypeFade] = Mage::helper('iqnomy_extension')->__("Fade through the liquid contents.");
        
        $fieldset->addField('animation_type', 'select', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Animation type"),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'animation_type',
             'values'    => $values,
             'note'      => Mage::helper('iqnomy_extension')->__("Select the way of showing the matching liquid content."),
             'onchange'  => 'animationTypeChanged(this);'
        ));
        
        $fieldset->addField('duration', 'text', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Animation duration"),
             'class'     => 'required-entry validate-number',
             'required'  => true,
             'note'      => Mage::helper('iqnomy_extension')->__("Enter the animation duration in milliseconds."),
             'name'      => 'duration',
        ));
        
        $fieldset->addField('pause', 'text', array(
            'label'      => Mage::helper('iqnomy_extension')->__("Pause time"),
            'class'      => 'required-entry validate-number',
            'required'   => true,
            'note'       => Mage::helper('iqnomy_extension')->__("Enter the time, in milliseconds, for how long the liquid content should be shown until it animates to the next liquid content."),
            'name'       => 'pause'
        ));
        
        $fieldset = $form->addFieldset('container_form_escalation', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Escalation type")
        ));
        
        $values = array();
        $values["FIXED"] = "Fixed";
        $values["RANDOM"] = "A/B Test";
        
        $fieldset->addField('fallback_type', 'select', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Escalation type"),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'fallback_type',
             'values'    => $values,
             'note'      => Mage::helper('iqnomy_extension')->__("If there is no profile of the visitor, this type will be used to select content.")
        ));
        
        $fieldset = $form->addFieldset('container_form_selection', array(
             'legend' => Mage::helper('iqnomy_extension')->__("Selection methods"),
             'comment' => Mage::helper('iqnomy_extension')->__("Enter the factor for each selection method in %. The total should be 100%")
        ));
             
        $values = array();
        $values["VISITORSEMANTIC"] = "Visitor semantic";
        $values["RESOURCESEMANTIC"] = "Semantic";
        $values["VISITORCLASSIFICATION"] = "Visitor classification";
        $values["DIMENSIONCLASSIFICATION"] = "Personalized";
        $values["FIXED"] = "Fixed";
        $values["RANDOM"] = "A/B Test";
        $values["SELFSERVICE"] = "Selfservice";
        $values["DIMENSIONPROPERTY"] = "Dimension rules";
        
        foreach($values as $key => $value)
        {
            $data["selection_methods_".$key] = 0;
            $fieldset->addField('selection_methods_'.$key, 'text', array(
                 'label'     => Mage::helper('iqnomy_extension')->__($value),
                 'class'     => 'required-entry validate-number validate-zero-or-greater',
                 'required'  => true,
                 'name'      => 'selection_methods['.$key.']'
            ));
        }
        
        if(array_key_exists("content_selection_methods", $data))
        {
            foreach($data["content_selection_methods"] as $method)
            {
                $data["selection_methods_".$method["type"]] = $method["value"];
            }
        }
	
        $fieldset = $form->addFieldset('container_placements', array(
            'legend' => Mage::helper('iqnomy_extension')->__("Pages"),
        ));
                
        if(!array_key_exists("containerplacements", $data))
        {
            $data["container_placements"] = array();
        }
        
        $data["is_advanced"] = true;
        
        $fieldset->addField('', 'text', array())->setRenderer($this->getLayout()->createBlock('iqnomy_extension/adminhtml_container_edit_containerpages')->setData($data));
        
        $form->setValues($data);
 
        return parent::_prepareForm();
    }
}