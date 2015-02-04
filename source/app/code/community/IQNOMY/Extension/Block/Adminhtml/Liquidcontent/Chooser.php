<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent_Chooser extends Mage_Core_Block_Abstract 
{
    public function __construct($data)
    {
        parent::__construct($data);
    }
    
    public function _toHtml()
    {
        $block = $this->getLayout()->createBlock("adminhtml/catalog_product_widget_chooser");
        $block->setId("productChooser");
        
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label' => Mage::helper('iqnomy_extension')->__("Close"),
            'onclick' => 'closeProductChooser();',
            'class' => 'delete',
            'style' => 'float: right; padding-top: 2px;'
        ));
        
        
        return '<div id="product-chooser" class="entry-edit" style="display: none; position: fixed; left: 0px; right: 0px; top: 0px; bottom: 0px; z-index: 500; background-color: rgba(0,0,0,0.75); padding: 10%;overflow: auto;">
                    <div class="entry-edit-head">
                        <h4 class="icon-head head-edit-form fieldset-legend" style="margin-top: 2px;">'.Mage::helper('iqnomy_extension')->__("Search product").'</h4>
                        '.$button->toHtml().'
                    </div>
                    <div class="fieldset">
                        '.$block->toHtml().'
                    </div>
                </div>';
        
        
    }
}