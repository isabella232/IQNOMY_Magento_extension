<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Liquidcontent_Edit_Renderer_Button extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $required = "";
        if($element->getRequired())
        {
            $required = "<span class='required'>*</span>";
        }
        
        $html = '<tr>';
            $html .= '<td class="label">';
                $html .= '<label>'.$element->getLabel().$required.'</label>';
            $html .= '</td>';
            $html .= '<td class="value">';
                $html .= '<div style="height: 30px;" id="image-buttons">';
                $html .= "<button style='float: left;' type='button' onclick='".$element->getOnclick()."'>".$element->getButtonText().'</button>';
		$html .= "<span style='float: left; margin-left: 8px; margin-right: 8px;'> ".Mage::helper('iqnomy_extension')->__('or')." </span>";
		$html .= "<button style='float: left;' type='button' onclick='openImageChooser();'>".Mage::helper('iqnomy_extension')->__('Search image')."</button>";
                $html .= '<label style="float: left; margin-left: 15px;" id="current-file"></label>';
                if($element->getCurrentImage() != null)
                {
                    $html .= '<a href="'.$element->getCurrentImage().'" target="_blank" title="'.$element->getImageTitle().'"><img style="max-height: 30px; float: left; margin-left: 15px;" id="current-image" src="'.$element->getCurrentImage().'"></a>';
                }
		else
		{
		    $html .= '<a target="_blank" style="display: none;"><img style="max-height: 30px; float: left; margin-left: 15px;" id="current-image"></a>';
		}
                $html .= '</div>';
                if($element->getNote() != null)
                {
                    $html .= '<div><p class="note"><span>'.$element->getNote().'</span></p></div>';
                }
            $html .= '</td>';
        $html .= '</tr>';
        return $html;
    }
}
