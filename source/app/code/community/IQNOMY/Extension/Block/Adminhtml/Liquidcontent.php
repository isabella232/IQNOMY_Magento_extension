<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_LiquidContent extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
	parent::__construct();
        $this->_controller = 'adminhtml_liquidcontent';
        $this->_blockGroup = 'iqnomy_extension';

	$this->_addButtonLabel = Mage::helper('iqnomy_extension')->__("Add liquid content");
	
	if($this->getRequest()->getParam('container') != null)
	{
	    $container = Mage::getSingleton("iqnomy_extension/container")->load($this->getRequest()->getParam('container'));
	    $containerName = "";
	    if($container != null)
	    {
		$containerName = $container->getName();
	    }
	    $this->_headerText = Mage::helper('iqnomy_extension')->__("Liquid contents for container: ") . $containerName;
	    
	    $this->_removeButton('add');
	
	    $this->_addButton('addBtn', array(
		'label' => $this->_addButtonLabel,
		'onclick' => "setLocation('" . $this->getUrl('*/*/new', array('container' => $this->getRequest()->getParam('container'))) . "');",
		'class' => 'add'
	    ));
	}
	else
	{
	    $this->_headerText = Mage::helper('iqnomy_extension')->__("Liquid contents");
	}

    }
}