<?php

/**
 * IQNOMY Version Info
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_System_Config_Form_Fieldset_Versioninfo extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $version = Mage::getConfig()->getNode('modules/IQNOMY_Extension/version');

        $html = '<fieldset class="config">'
              . Mage::helper('iqnomy_extension')->__('IQNOMY Extension version %s', $version)
              . '<span style="float:right"><a href="http://www.iqnomy.com/" target="_blank">'
              . Mage::helper('iqnomy_extension')->__('www.iqnomy.com') . '</a></span>'
              . '</fieldset>';

        return $html;
    }
}
