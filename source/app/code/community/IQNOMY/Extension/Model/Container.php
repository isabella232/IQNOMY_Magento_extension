<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Container extends Varien_Object
{
    private $_collection;
    
    private function validate($ignoreContainerPlacements = false)
    {        
        $totalPercentage = 0;
        foreach($this->getContentSelectionMethods() as $method)
        {
            if($method["value"] < 0)
            {
                throw new Exception("A factor should be greater or equal to 0.");
            }
            
            $totalPercentage += (int)$method["value"];
        }
        if($totalPercentage != 100)
        {
            throw new Exception("The factors together should be 100%.");
        }
        
        if($this->getContainerplacements() == null && !$ignoreContainerPlacements)
        {
            throw new Exception("At least one page should be defined.");
        }
        
	if($this->getContainerplacements() != null)
	{
	    $existingPages = array();
	    foreach($this->getContainerplacements() as $containerPlacement)
	    {
		if(filter_var($containerPlacement["matchUrl"], FILTER_VALIDATE_URL) === false && $containerPlacement["matchRegEx"] == null)
		{
		    throw new Exception("One of the page URLs is not valid.");
		}

		$maxResults = (int)$containerPlacement["maxResults"];
		if($maxResults <= 0)
		{
		    throw new Exception("The maximum results for an page should be greater than 0.");
		}

		if(in_array($containerPlacement["matchUrl"], $existingPages))
		{
		    throw new Exception("The page URLs should be unique within this container.");
		}
		$existingPages[] = $containerPlacement["matchUrl"];
	    }
	}
    }
    
    public function toApiArray()
    {
        $array = array();
        if($this->getId() != null)
        {
            $array["id"] = $this->getId();
        }
        $array["name"] = $this->getName();
        
        $array["activateDate"] = "2010-01-01T00:00:00+00:00";
        if($this->getIsEnabled())
        {
            $array["expireDate"] = "2020-01-01T00:00:00+00:00";
        }
        else
        {
            $array["expireDate"] = "2010-01-01T00:00:00+00:00";
        }
        
	if($this->getFallbackType() != null)
	{
	    $array["fallbackType"] = $this->getFallbackType();
	}
	if($this->getContainerplacements() != null)
	{
	    $array["containerplacements"] = $this->getContainerplacements();
	}
        $array["contentSelectionMethods"] = $this->getContentSelectionMethods();
        
        return $array;
    }
    
    private function convertTime($unix)
    {
        return date("Y-m-d", Mage::getModel('core/date')->timestamp($unix));
    }
    
    public function save($ignoreContainerPlacements = false)
    {
        $this->validate($ignoreContainerPlacements);
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();
        try
        {
            $response = $webservice->saveContainer($this->toApiArray());
            if($response != null && array_key_exists("id", $response) && $response["id"] > 0)
            {
                $this->setId($response["id"]);
		
                return true;
            }
            return false;
        }
        catch(Exception $ex)
        {
            throw $ex;
            throw new Exception("An error occurred while saving the container. Warning: Make sure that all the page URLs have been tracked at least once.");
        }
    }
    
    public function saveBanner()
    {
	$banner = $this->getBanner();
	$banner->setName($this->getName());
	$banner->setContainerId($this->getId());
	$banner->setHtmlId($this->getHtmlId());
	$banner->setHeight($this->getHeight());
	$banner->setIsEnabled($this->getIsEnabled());
	$banner->setIsIqnomyBanner(false);
	$banner->setAnimationType($this->getAnimationType());
	$banner->setDuration($this->getDuration());
	$banner->setPause($this->getPause());
	$banner->save();
    }
    
    public function getBanner()
    {
	$banner = Mage::getModel("iqnomy_extension/banner")->findByNameAndContainer($this->getName(), $this);
	if($banner == null)
	{
	    return Mage::getModel("iqnomy_extension/banner");
	}
	return $banner;
    }
    
    private $selectionMethods = array(
        "VISITORSEMANTIC" => "Visitor semantic",
        "RESOURCESEMANTIC" => "Semantic",
        "VISITORCLASSIFICATION" => "Visitor classification",
        "DIMENSIONCLASSIFICATION" => "Personalized",
        "FIXED" => "Fixed",
        "RANDOM" => "A/B Test",
        "SELFSERVICE" => "Selfservice",
        "DIMENSIONPROPERTY" => "Dimension rules"
    );
    
    public function getFallbackTypeViewable()
    {
        return $this->selectionMethods[$this->getFallbackType()];
    }
        
    public function getSelectionMethodsViewable()
    {
        $methods = $this->getContentSelectionMethods();
        if($methods == null)
        {
            return "";
        }
        
        $strings = array();
        foreach($methods as $method)
        {
            if($method["value"] > 0)
            {
                $strings[] = $this->selectionMethods[$method["type"]]." ".$method["value"]."%";
            }
        }
        return implode(", ", $strings);
    }
        
    public function fromArray($array)
    {
        if($array == null || !array_key_exists("id", $array) || $array["id"] <= 0)
        {
            return null;
        }
        
        foreach($array as $key => $value)
        {
            $setFunc = "set".ucfirst($key);
            $this->$setFunc($value);
        }
        
        $from = strtotime("2020-01-01 00:00:00");
        $to = strtotime("2020-01-01 00:00:00");
        
        if($this->getActivateDate() != null)
        {
            $from = strtotime($this->getActivateDate());
        }
        
        if($this->getExpireDate() != null)
        {
            $to = strtotime($this->getExpireDate());
        }
        
        if($from <= time() && $to >= time())
        {
            $this->setIsEnabled(true);
        }
        else
        {
            $this->setIsEnabled(false);
        }
        
        return $this;
    }

    public function getCollection()
    {
        if($this->_collection == null)
        {
            /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

            /** @var IQNOMY_Extension_Model_Webservice $webservice */
            $webservice = $_helper->getWebservice();
            $containers = $webservice->getContainers();

            $collection = new Varien_Data_Collection();
            if($containers != null && is_array($containers))
            {
                foreach($containers as $container)
                {
                    $object = Mage::getModel("iqnomy_extension/container")->fromArray($container);
                    if($object != null)
                    {
                        $collection->addItem($object);
                    }
                }
            }
            $this->_collection = $collection;
        }
        return $this->_collection;
    }
    
    public function load($containerId)
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();
        $array = $webservice->getContainer($containerId);
        if($array == null)
        {
            throw new Exception("The container does not exist.");
        }
        
        $this->fromArray($array);
        return $this;
    }
    
    public function exists($containerId)
    {
         /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();
        return $webservice->containerExists($containerId);
    }
    
    public function delete($containerId)
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();
        $result = $webservice->deleteContainer($containerId);
        if($result)
        {
            $bannerCollection = Mage::getModel("iqnomy_extension/banner")->getCollection();
            foreach($bannerCollection as $banner)
            {
                if($banner->getContainerId() == $containerId)
                {
                    $banner->delete();
                }
            }
        }
        return $result;
    }
    
    public function findByName($name)
    {
	if($this->getCollection() != null)
	{
	    foreach($this->getCollection() as $container)
	    {
		if($container->getName() == $name)
		{
		    return $container;
		}
	    }
	}
	return null;
    }
    
    private $liquidContents = null;
    public function getLiquidContents()
    {
	if($this->liquidContents == null)
	{
	    /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

            /** @var IQNOMY_Extension_Model_Webservice $webservice */
            $webservice = $_helper->getWebservice();
            $contents = $webservice->getLiquidContentsByContainer($this->getId());

            $collection = new Varien_Data_Collection();
            if($contents != null && is_array($contents))
            {
                foreach($contents as $content)
                {
		    $object = Mage::getModel("iqnomy_extension/liquidcontent")->fromArray($content, true);
                    if($object != null)
                    {
                        $collection->addItem($object);
                    }
                }
            }
            $this->liquidContents = $collection;
	}
	return $this->liquidContents;
    }
}