<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Container_Edit_Containerpages extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{    
    public function _construct()
    {
        $this->setTemplate("iqnomy_extension/container/pages.phtml");
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    
    public function getPreviewUrl($url)
    {
        if(strpos($url, "?") !== false)
        {
            return $url."&preview=true&placement_id=".$this->getPlacementId();
        }
        else
        {
            return $url."?preview=true&placement_id=".$this->getPlacementId();
        }
    }
}