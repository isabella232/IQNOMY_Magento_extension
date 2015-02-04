<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_ProductController extends Mage_Core_Controller_Front_Action
{
    public function testAction()
    {	
	$productIds = $this->getRequest()->getPost('product_ids');
	if(!is_array($productIds))
	{
	    $result = array('success' => false);
	}
	else
	{
	    $grid = Mage::app()->getLayout()->createBlock('catalog/product_list', 'product_list');
	   
	    $collection = Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
	    $coll = new Varien_Data_Collection();
	    foreach($collection as $product)
	    {
		$coll->addItem($product->load($product->getId()));
	    }
	    $grid->setCollection($coll);
	    $grid->setColumnCount($coll->count());
	    $grid->setProductCount($coll->count());
	    $grid->setShowItems($coll->count());
	    $grid->setMode('grid');
	    $grid->setTemplate('catalog/product/list.phtml');
	    
	    $result = array('success' => true, 'grid' => $grid->toHtml());
	}
	echo json_encode($result);
    }
}