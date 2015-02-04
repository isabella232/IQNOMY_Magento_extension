<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Product
{
    public function onProductSaved($observer)
    {
	try
	{	    
	    $coreHelper = Mage::helper('core');
	    $product = $observer->getProduct();
	    
	    $liquidContentCollection = Mage::getModel("iqnomy_extension/liquidcontent")->getCollection();
	    if($liquidContentCollection != null)
	    {
		foreach($liquidContentCollection as $liquidContent)
		{
		    if($liquidContent->getProductId() != null && $liquidContent->getProductId() == $product->getId())
		    {
			$price = $coreHelper->formatPrice($product->getPrice(), true);
			$url = $product->getProductUrl();
			$title = $product->getName();
			
			$specialPrice = $coreHelper->formatPrice($product->getPrice(), true);
			$specialPriceFrom = 0;
			$specialPriceTo = 0;
			if($product->getSpecialFromDate() != null)
			{
			    $specialPriceFrom = strtotime($product->getSpecialFromDate());
			}
			
			if($product->getSpecialToDate() != null)
			{
			    $specialPriceTo = strtotime($product->getSpecialToDate());
			}
			
			$image = $product->getMediaGalleryImages()->getItemByColumnValue('label','iqnomy');
			if($image == null)
			{
			    $image = $product->getMediaGalleryImages()->getFirstItem();
			}
			
			$imageUrl = "";
			if($image != null)
			{
			    $imageUrl = $image->getUrl();
			}
			
			$liquidContent->loadProductAttributes($product);
			$liquidContent->setSpecialPrice($specialPrice);
			$liquidContent->setSpecialPriceFromDate($specialPriceFrom);
			$liquidContent->setSpecialPriceToDate($specialPriceTo);
			$liquidContent->setPrice($price);
			$liquidContent->setUrl($url);
			$liquidContent->setTitle($title);
			$liquidContent->setImage($imageUrl);
			$liquidContent->save(true);
		    }
		}
	    }
	}
	catch(Exception $ex)
	{
	    $error = Mage::helper('iqnomy_extension')->__("This product is linked with one or more liquid contents, updating these liquid contents failed.");
	    Mage::getSingleton('adminhtml/session')->addError($error);
	}
    }
}
