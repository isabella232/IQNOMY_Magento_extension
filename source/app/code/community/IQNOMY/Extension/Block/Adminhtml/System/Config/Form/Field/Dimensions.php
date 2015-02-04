<?php

/**
 * IQNOMY Dimensions settings intro.
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_System_Config_Form_Field_Dimensions extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $text1 = 'Dimensions are used to recognize your visitors interests. You can use these to personalize your '
               . 'content. Make sure to select the dimensions that are revelant for you. For more information go to '
               . '<a href="%s" target="_blank">support.iqnomy.com</a>';

        $text2 = 'The attributes configured here are treated as IQNOMY dimensions. After changing these, you should '
               . 'synchronize via <a href="%s">Customers, IQNOMY Extension</a>.';

        $link = $this->getUrl('adminhtml/iqnomy');

        $html = '<tr id="row_' . $element->getHtmlId() . '">'
              . '<td colspan="3" class="value2">'
              . '<p>' . Mage::helper('iqnomy_extension')->__($text1, 'http://support.iqnomy.com/') . "</p>\n"
              . '<p>' . Mage::helper('iqnomy_extension')->__($text2, $link) . '</p>'
              . '</td>'
              . '</tr>';

        return $html;
    }
}
