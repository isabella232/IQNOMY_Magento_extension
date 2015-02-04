<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Banner_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $data = array();
        // Checking if this is a re-post, if data found load it and empty the session.
        if (Mage::getSingleton('adminhtml/session')->getFormData() != null)
        {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        }
        
        // No data found
        if($data == null)
            $data = array();
         
        //Setting up form.
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
        $this->setForm($form);
 
        //Creating the first fieldset with basic banner information.
        $fieldset = $form->addFieldset('impression_info', array(
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
        
        //The banner name, just to help the user to remember which is which.
        $fieldset->addField('name', 'text', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Name"),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'name'
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
        
        //Loading all existing containers.
        $values = array();
        $containerCollection = Mage::getModel("iqnomy_extension/container")->getCollection();
        foreach($containerCollection as $container)
        {
            $values[$container->getId()] = $container->getName();
        }
        
        $fieldset->addField('container_id', 'select', array(
             'label'     => Mage::helper('iqnomy_extension')->__("Container"),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'container_id',
             'note'      => Mage::helper('iqnomy_extension')->__("Select which IQNOMY-container is linked to this case."),
             'values'    => $values
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
        
        $form->setValues($data);
 
        return parent::_prepareForm();
    }
}