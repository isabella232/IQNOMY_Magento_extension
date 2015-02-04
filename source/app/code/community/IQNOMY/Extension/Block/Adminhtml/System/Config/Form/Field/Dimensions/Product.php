<?php
/**
 * IQNOMY Default product dimensions
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_System_Config_Form_Field_Dimensions_Product extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        $dimensions = $_helper->getDefaultProductDimensions();

        $html = '<div id="' . $element->getHtmlId() . '">';
        if (count($dimensions) > 0) {
            $html .= nl2br(implode("\n", $this->escapeHtml($dimensions)));
        }
        else {
            $html .= Mage::helper('iqnomy_extension')->__('(None)');
        }
        $html .= '</div>';

        return $html;
    }
}
