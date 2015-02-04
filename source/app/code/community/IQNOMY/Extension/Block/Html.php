<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Html extends Mage_Core_Block_Abstract
{
    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    
    public function _toHtml()
    {
        $tag = $this->getTag();
        $class = $this->getClass();
        $style = $this->getStyle();
        $htmlId = $this->getHtmlId();
        
        return '<'.$tag.' id="'.$htmlId.'" class="'.$class.'" style="'.$style.'"></'.$tag.'>';
    }
}
