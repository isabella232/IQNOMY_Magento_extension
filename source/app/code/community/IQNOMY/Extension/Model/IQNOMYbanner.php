<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_IQNOMYbanner
{
    public function iqnomyBannerAfterSave($observer)
    {
	try
	{
	    $banner = $observer->getDataObject()->load($observer->getDataObject()->getId())->getData();
	    
	    $case = Mage::getModel("iqnomy_extension/case")->load(1);
	    if($case != null)
	    {
		$container = $this->getContainerByBanner($banner["banner_id"], $banner["name"], true, $banner["is_active"]);

		$liquidContents = $container->getLiquidContents();

		$images = array();
		if(array_key_exists("image", $banner))
		{
		    $images = $banner["image"];
		}

		$data = array();
		foreach($images as $bannerImage)
		{
		    $data[$bannerImage["image_id"]] = $bannerImage;
		}

		foreach($liquidContents as $liquidContent)
		{
		    if(!array_key_exists($liquidContent->getIqnomyBannerValueId(), $data) || $data[$liquidContent->getIqnomyBannerValueId()]["disabled"])
		    {                    
			//The image has been removed or disabled.
			$liquidContent->delete($liquidContent->getId());
		    }
		    else
		    {
			//Update the data.                    
			$this->updateLiquidContent($container, $liquidContent, $liquidContent->getIqnomyBannerValueId(), $data[$liquidContent->getIqnomyBannerValueId()], !$data[$liquidContent->getIqnomyBannerValueId()]["disabled"]);

			unset($data[$liquidContent->getIqnomyBannerValueId()]);
		    }
		}

		//Create liquid contents for the left overs.
		foreach($data as $valueId => $bannerImage)
		{
		    $this->updateLiquidContent($container, Mage::getModel("iqnomy_extension/liquidcontent"), $valueId, $bannerImage, !$bannerImage["disabled"]);
		}
		
		$this->getPlacementByBanner($banner["banner_id"], $banner["name"], $container);
	    }
	}
	catch(Exception $ex)
	{
	    $error = Mage::helper('iqnomy_extension')->__("An error occurred while saving the banner to the IQNOMY-platform.");
	    Mage::getSingleton('adminhtml/session')->addError($error);
	}
    }
    
    public function iqnomyBannerAfterDelete($observer)
    {	
	try
	{
	    $banner = $observer->getModel()->toArray();
	    
	    $container = $this->getContainerByBanner($banner["banner_id"], $banner["name"], false);
	    
	    if($container != null)
	    {
		$container->delete($container->getId());
		
		$placement = $this->getPlacementByBanner($banner["banner_id"], $banner["name"], $container, false);
		if($placement != null)
		{
		    $placement->delete();
		}
	    }
	    
	}
	catch(Exception $ex)
	{
	    $error = Mage::helper('iqnomy_extension')->__("An error occurred while removing the banner from the IQNOMY-platform.");
	    Mage::getSingleton('adminhtml/session')->addError($error);
	}
    }
    
    private function updateLiquidContent($container, $liquidContent, $valueId, $bannerImage, $active)
    {	
	$liquidContent->setName($container->getName()."-".$valueId);
	$liquidContent->setIqnomyBannerValueId($valueId);
	$liquidContent->setContainerId($container->getId());
	$liquidContent->setIsEnabled($active);
	$liquidContent->setUrl($bannerImage["label"]);
	$liquidContent->setTitle($bannerImage["label"]);
	$liquidContent->setImagePath($bannerImage["file"]);
	$liquidContent->setHtml("");
	$liquidContent->setUrlTarget("");
	$liquidContent->setAltText($bannerImage["label"]);
	
	if(array_key_exists("extra_data", $bannerImage))
	{
	    $data = json_decode($bannerImage["extra_data"], true);
	    
	    $dimensions = array();
	    foreach($data as $dimensionIdentifier => $propertyId)
	    {
		if(strpos($dimensionIdentifier, "dimension") !== false && $propertyId != "")
		{
		    $dimensionId = str_replace("dimension", "", $dimensionIdentifier);
		    if($dimensionId > 0)
		    {
			$dimensions[$dimensionId] = $propertyId;
		    }
		}
	    }
	    $liquidContent->setDimensions($dimensions);
	}
	
	$liquidContent->save();
    }
    
    private function getContainerByBanner($bannerId, $title, $createIfNotExists = true, $isActive = null)
    {
	if($isActive !== null)
	{
	    if($isActive)
	    {
		$isActive = true;
	    }
	    else
	    {
		$isActive = false;
	    }
	}
	
	$bannerName = "IQNOMY Banner ".$bannerId.": ".$title;
	
        $case = Mage::getModel("iqnomy_extension/case")->load(1);
        $container = Mage::getModel("iqnomy_extension/container")->findByName($bannerName);
        if($container == null && $createIfNotExists)
        {
            $container = $case->createContainer();
            $container->setName($bannerName);
	    $container->setIsEnabled($isActive);
            $container->save();
        }
	
	if($container != null && $container->getIsEnabled() && $isActive === false)
	{
	    $container->setIsEnabled(false);
	    $container->save();
	}
	
	if($container != null && !$container->getIsEnabled() && $isActive === true)
	{
	    $container->setIsEnabled(true);
	    $container->save();
	}
	
        return $container;
    }
    
    private function getPlacementByBanner($bannerId, $title, $container, $createIfNotExists = true)
    {	
	$placementName = "IQNOMY Banner ".$bannerId.": ".$title;
	
	$placement = Mage::getModel("iqnomy_extension/banner")->findByNameAndContainer($placementName, $container);
	if($placement == null && $createIfNotExists)
	{
	    $placement = Mage::getModel("iqnomy_extension/banner");
	    $placement->setIsIqnomyBanner(true);
	    $placement->setIsEnabled(true);
	    $placement->setName($placementName);
	    $placement->setHtmlId("iqnomy-banner-".$bannerId);
	    $placement->setContainerId($container->getId());
	    $placement->save();
	}
	return $placement;
    }
    
    public function editTabImageColumnAdd(Varien_Event_Observer $observer)
    {
	try
	{
	    /** @var IQNOMY_Banner_Block_Adminhtml_Banner_Edit_Tab_Image_Column $renderer */
	    $renderer = $observer->getRenderer();

	    /** @var IQNOMY_Extension_Helper_Data $_helper */
	    $_helper = Mage::helper('iqnomy_extension');

	    /** @var IQNOMY_Extension_Model_Webservice $webservice */
	    $webservice = $_helper->getWebservice();        
	    $dimensions = $webservice->getDimensions();

	    foreach($dimensions as $dimension)
	    {
		$show = true;

		$code = $dimension["name"];
		$number = str_replace("level_", "", $code);
		$number = str_replace("_category", "", $number);
		if(strpos($code, "level_") === false || strpos($code, "_category") === false || $number <= 0)
		{
		    $show = false;
		}

		if($show)
		{
		    $label = $dimension["description"];
		    if($label == null)
		    {
			$label = $dimension["name"];
		    }

		    $label = Mage::helper("iqnomy_extension")->__("Dimension")."<br>(".$label.")";

		    $html = "<select>";

		    $html .= "<option value=''>".Mage::helper("iqnomy_extension")->__("None")."</option>";

		    foreach($dimension["dimensionProperty"] as $property)
		    {
			$html .= "<option value='".$property["id"]."'>".$property["label"]."</option>";
		    }

		    $html .= "</select>";

		    $renderer->addColumn('dimension'.$dimension["id"], $label, $html);
		}
	    }
	}
	catch(Exception $ex)
	{
	    $error = Mage::helper('iqnomy_extension')->__("An error occurred while retreiving the dimensions from the IQNOMY-platform.");
	    Mage::getSingleton('adminhtml/session')->addError($error);
	}
    }
}