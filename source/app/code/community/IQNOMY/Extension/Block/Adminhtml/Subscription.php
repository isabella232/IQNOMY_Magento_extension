<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Subscription extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_subscription';
        $this->_blockGroup = 'iqnomy_extension';

        $this->_headerText = Mage::helper('iqnomy_extension')->__("Subscribers");
        
        parent::__construct();
        
        $this->_removeButton('add');
    }
}