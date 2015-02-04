<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Container extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
            $this->_controller = 'adminhtml_container';
            $this->_blockGroup = 'iqnomy_extension';

            $this->_headerText = Mage::helper('iqnomy_extension')->__("Containers");
            $this->_addButtonLabel = Mage::helper('iqnomy_extension')->__("Add container");

            parent::__construct();
	}
	
	
}