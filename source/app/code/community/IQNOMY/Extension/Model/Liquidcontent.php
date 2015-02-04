<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_LiquidContent extends Varien_Object
{
    private $_collection;
    private $_dimensions;
    
    private function getDimensionProperty($dimensionId, $propertyId)
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();        
        $this->_dimensions = $webservice->getDimensions();
        
        $dimension = $this->getDimension($dimensionId);
        if($dimension != null)
        {
            foreach($dimension["dimensionProperty"] as $property)
            {
                if($property["id"] == $propertyId)
                {
                    return $property;
                }
            }
        }
        return null;
    }
    
    private function getDimensionPropertyByValue($dimensionId, $value)
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();        
        $this->_dimensions = $webservice->getDimensions();
        
        $dimension = $this->getDimension($dimensionId);
        if($dimension != null)
        {
            foreach($dimension["dimensionProperty"] as $property)
            {
                if($property["value"] == $value)
                {
                    return $property;
                }
            }
        }
        return null;
    }
    
    private function getDimension($dimensionId)
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();        
        $this->_dimensions = $webservice->getDimensions();
        
        if($this->_dimensions != null)
        {
            foreach($this->_dimensions as $dimension)
            {
                if($dimension["id"] == $dimensionId)
                {
                    return $dimension;
                }
            }
        }
        return null;
    }
    
    public function getPropertyList()
    {
        $_helper = Mage::helper('iqnomy_extension');
        
        $properties = $this->getProperties();
        
        $list = array();
        if($properties != null)
        {
            foreach($properties as $property)
            {
                $dimension = $this->getDimension($property["dimensionId"]);
                if($dimension != null)
                {
                    $property = $this->getDimensionPropertyByValue($property["dimensionId"], $property["value"]);
                    if($property != null)
                    {
                        $string = $dimension["name"];
                        if($dimension["description"] != null)
                        {
                            $string = $dimension["description"];
                        }
                        
                        $string .= ": ".$property["label"];
                        $list[] = $string;
                    }
                }
            }
        }
        
        if($list == null)
        {
            return $_helper->__("None");
        }
        return implode(", ", $list);
    }
    
    private function validate()
    {
        if($this->getContainer() == null)
        {
            throw new Exception("Select an valid container.");
        }
        
        if($this->getActivateDate() != null && $this->getExpireDate() != null && strtotime($this->getActivateDate()) > strtotime($this->getExpireDate()))
        {
            throw new Exception("The till date should be later than the from date.");
        }
        
        if($this->getIqnomyBannerValueId() == null)
        {
	    if($this->getIsNewsletterContent() || $this->getProductId() == null)
	    {
		if(array_key_exists("image", $_FILES) && array_key_exists("name", $_FILES["image"]) && $_FILES["image"]["name"] != null)
                {
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);

                    $path = Mage::getBaseDir('media') . "/iqnomy";

                    $filename = sha1($this->getName().date("YmdHis")).".".pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $result = $uploader->save($path, $filename);
		    
		    $imageDimensions = getimagesize($result["path"] . $result["file"]);
		    $this->setImageWidth($imageDimensions[0]);
		    $this->setImageHeight($imageDimensions[1]);
		    
                    $this->setImagePath($result["file"]);
                }

		if($this->getImagePath() == null || $this->getUrlToImage() == null)
		{
		    throw new Exception("Select a valid image.");
		}
	    }
	    else
	    {
		$this->setImageWidth(null);
		$this->setImageHeight(null);
	    }
	    
            if($this->getProductId() == null && !$this->getIsNewsletterContent())
            {
                if(filter_var($this->getUrl(), FILTER_VALIDATE_URL) === false)
                {
                    throw new Exception("The content URL is not valid.");
                }
            }
	    
            if($this->getProductId() != null)
            {
                $product = Mage::getModel("catalog/product")->load($this->getProductId());
                if($product == null || $this->getProductId() != $product->getId())
                {
                    throw new Exception("The selected product does not exist.");
                }

                $price = Mage::helper('core')->formatPrice($product->getPrice(), true);
		
		$specialPrice = Mage::helper('core')->formatPrice($product->getSpecialPrice(), true);
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
		
                $url = $product->getProductUrl();
                $title = $product->getName();

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
		
                $this->setPrice($price);
		$this->setSpecialPrice($specialPrice);
		$this->setSpecialPriceFrom($specialPriceFrom);
		$this->setSpecialPriceTo($specialPriceTo);
                $this->setUrl($url);
                $this->setTitle($title);
                $this->setImage($imageUrl);
		$this->setImagePath("");
		$this->loadProductAttributes($product);
            }
        }
    }
    
    public function loadProductAttributes($product)
    {
	$excludedAttributes = array("attribute_set_id", "entity_id", "entity_type_id", "old_id", "category_ids", "msrp_enabled", "msrp_display_actual_price_type", "msrp");
	    
	$attributes = $product->getAttributes();
	$attributeValues = array();
	foreach ($attributes as $attribute) {
	    $code = $attribute->getAttributeCode();
	    if(!in_array($code, $excludedAttributes))
	    {
		$value = $attribute->getFrontend()->getValue($product);
		if(is_array($value))
		{
		    foreach($value as $key => $subvalue)
		    {
			if(!is_array($subvalue) && !is_object($subvalue))
			{
			    $attributeValues["attr_".$code."_".$key] = $subvalue;
			}
		    }
		}
		elseif(!is_object($value))
		{
		    $attributeValues["attr_".$code] = $attribute->getFrontend()->getValue($product);
		}
	    }
	}

	if(array_key_exists("attr_image", $attributeValues) && $attributeValues["attr_image"] != null)
	{
	    $attributeValues["attr_image"] = Mage::getModel('catalog/product_media_config')->getMediaUrl( $product->getImage() );
	}
	if(array_key_exists("attr_small_image", $attributeValues) && $attributeValues["attr_small_image"] != null)
	{
	    $attributeValues["attr_small_image"] = Mage::getModel('catalog/product_media_config')->getMediaUrl( $product->getSmallImage() );
	}
	if(array_key_exists("attr_thumbnail", $attributeValues) && $attributeValues["attr_thumbnail"] != null)
	{
	    $attributeValues["attr_thumbnail"] = Mage::getModel('catalog/product_media_config')->getMediaUrl( $product->getThumbnail() );
	}
	
	$formattedPrice = Mage::helper('core')->currency($product->getPrice());
	$formattedSpecialPrice = Mage::helper('core')->currency($product->getSpecialPrice());
	$attributeValues["attr_formatted_price"] = $formattedPrice;
	$attributeValues["attr_formatted_special_price"] = $formattedSpecialPrice;
	
	$this->setData("additional_variables", $attributeValues);
    }
    
    public function getVariables()
    {
	$hidden = array("name", "raw_html_template", "html", "activate_date", "expire_date", "properties", "container_id", "id", "image_path", "iqnomy_banner_value_id", "is_enabled", "additional_variables");
	$transForm = array("image" => "image_url", "url_target" => "target", "alt_text" => "alt");
	
	$variables = $this->getData();
	foreach($hidden as $hide)
	{
	    if(array_key_exists($hide, $variables))
	    {
		unset($variables[$hide]);
	    }
	}
	foreach($transForm as $from => $to)
	{
	    if(array_key_exists($from, $variables))
	    {
		$variables[$to] = $variables[$from];
		unset($variables[$from]);
	    }
	}
	$additional = $this->getAdditionalVariables();
	if($additional != null)
	{
	    $variables = array_merge($variables, $this->getAdditionalVariables());
	}
	$result = array();
	foreach($variables as $key => $value)
	{
	    $templateCode = '${'.lcfirst(str_replace(" ", "", ucwords(str_replace("_", " ", $key)))).'}';
	    $result[] = array("code" => $key, "value" => $value, "template_code" => $templateCode);
	}
	return $result;
    }
    
    /**
     * 
     * @return IQNOMY_Extension_Model_Container|null
     */
    private $_container = null;
    public function getContainer()
    {
	if($this->_container == null)
	{
	    $collection = Mage::getSingleton("iqnomy_extension/container")->getCollection();
	    if($this->getContainerId() != null && $collection != null)
	    {
		foreach($collection as $container)
		{
		    if($container->getId() == $this->getContainerId())
		    {
			$this->_container = $container;
		    }
		}
	    }
	}
        return $this->_container;
    }
    
    public function toApiArray()
    {
        $array = array();
        if($this->getId() != null)
        {
            $array["id"] = $this->getId();
        }
        
        $array["containerId"] = $this->getContainer()->getId();
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
        
        $htmlTemplate = array(
            "product_id" => $this->getProductId(),
            "url" => $this->getUrl(), 
            "image_path" => $this->getImagePath(), 
            "title" => $this->getTitle(),
            "price" => $this->getPrice(),
	    "special_price" => $this->getSpecialPrice(),
	    "special_price_from_date" => $this->setSpecialPriceFrom(),
	    "special_price_to_date" => $this->getSpecialPriceTo(),
            "html" => $this->getHtml(),
            "url_target" => $this->getUrlTarget(),
            "alt_text" => $this->getAltText(),
            "iqnomy_banner_value_id" => $this->getIqnomyBannerValueId(),
	    "additional_variables" => $this->getAdditionalVariables(),
	    "popup_title" => $this->getPopupTitle(),
	    "popup_submit_text" => $this->getPopupSubmitText(),
	    "popup_alttext" => $this->getPopupAlttext(),
	    "popup_coloration" => $this->getPopupColoration(),
	    "popup_close_icon_color" => $this->getPopupCloseIconColor(),
	    "webshop_url" => $this->getWebshopUrl(),
	    "is_newsletter_content" => $this->getIsNewsletterContent(),
	    "image_width" => $this->getImageWidth(),
	    "image_height" => $this->getImageHeight()
        );
        
	$htmlTemplate["image"] = $this->getUrlToImage();
//        if($this->getIqnomyBannerValueId() == null)
//        {
//            $htmlTemplate["image"] = ($this->getProductId() != null ? $this->getImage() : Mage::getBaseUrl('media') . "iqnomy" . $this->getImagePath());
//        }
//        else
//        {
//            $htmlTemplate["image"] = Mage::getBaseUrl('media') . "iqnomy_banner" . $this->getImagePath();
//        }
        
        $array["htmlTemplate"] = json_encode($htmlTemplate);
        
        $properties = array();
        if($this->getDimensions() != null)
        {
            foreach($this->getDimensions() as $dimensionId => $propertyId)
            {
                $property = $this->getDimensionProperty($dimensionId, $propertyId);
                if($property != null)
                {
                    $prop = array("dimensionId" => $dimensionId, "value" => $property["value"]);
                    if(array_key_exists("label", $property))
                    {
                        $prop["label"] = $property["label"];
                    }
                    $properties[] = $prop;
                }
            }
        }
        if(count($properties) > 0)
        {
            $array["properties"] = $properties;
        }        
        
        return $array;
    }
    
    public function getUrlToImage()
    {
	if($this->getIqnomyBannerValueId() == null)
        {
	    if($this->getProductId() != null)
	    {
		return $this->getImage();
	    }
	    else
	    {
		$pathOne = Mage::getBaseDir('media') . "/iqnomy" . $this->getImagePath();
		if(file_exists($pathOne))
		{
		    return Mage::helper('iqnomy_extension')->getBaseUrl('media') . "iqnomy" . $this->getImagePath();
		}
		
		$pathTwo = Mage::getBaseDir('media') . $this->getImagePath();
		if(file_exists($pathTwo))
		{
		    return Mage::helper('iqnomy_extension')->getBaseUrl('media') . $this->getImagePath();
		}
	    }
        }
        else
        {
            return Mage::helper('iqnomy_extension')->getBaseUrl('media') . "iqnomy_banner" . $this->getImagePath();
        }
	return "";
    }
    
    private function convertTime($unix)
    {
        return date("Y-m-d", Mage::getModel('core/date')->timestamp($unix));
    }
    
    public function save($skipValidation = false)
    {
	if(!$skipValidation)
	{
	    $this->validate();
	}
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();
        try
        {
            $response = $webservice->saveLiquidContent($this->toApiArray());
            if($response != null && array_key_exists("id", $response) && $response["id"] > 0)
            {
                return true;
            }
            return false;
        }
        catch(Exception $ex)
        {
            throw new Exception("An error occurred while saving the liquid content.");
        }
    }
          /**
           * 
           * @param type $array
           * @return null|\IQNOMY_Extension_Model_LiquidContent
           * 
           * 
           * <li class="item">
<a href="" title="Test" class="product-image"><img src="${imageUrl}" width="135" height="135" alt="${title}"></a>
<h2 class="product-name"><a href="${imageUrl}" title="${title}">${title}</a></h2>                        
<div class="price-box">
<span class="regular-price">
<span class="price">&euro;&nbsp;1,00</span>
</span>
</div>
</li>
           */
    public function fromArray($array)
    {        
        if($array == null || !array_key_exists("id", $array) || $array["id"] <= 0)
        {
            return null;
        }
        
        foreach($array as $key => $value)
        {
            if($key != "htmlTemplate")
            {
                $setFunc = "set".ucfirst($key);
                $this->$setFunc($value);
            }
            else
            {
		$this->setRawHtmlTemplate($value);
                $template = json_decode($value, true);
		if($template == null || !is_array($template))
		{
		    $template = array();
		}
		
                foreach($template as $key => $value)
                {
                    $setFunc = "set".ucfirst($key);
                    $this->$setFunc($value);
                }
            }
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
            $contents = $webservice->getLiquidContents();

            $collection = new Varien_Data_Collection();
            if($contents != null && is_array($contents))
            {
                foreach($contents as $content)
                {
                    $object = Mage::getModel("iqnomy_extension/liquidcontent")->fromArray($content, true);
                    if($object != null)
                    {
			if($object->getContainer() != null)
			{
			    $collection->addItem($object);
			}
                    }
                }
            }
            $this->_collection = $collection;
        }
        return $this->_collection;
    }
    
    public function load($id)
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();
        $array = $webservice->getLiquidContent($id);
        if($array == null)
        {
            throw new Exception("The liquid content does not exist.");
        }
        
        $this->fromArray($array, true);
        return $this;
    }
    
    public function exists($id)
    {
         /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();
        return $webservice->liquidContentExists($id);
    }
    
    public function delete($id)
    {
        /** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();
        return $webservice->deleteLiquidContent($id);
    }
    
    public function getProductName()
    {
        if($this->getProductId() != null)
        {
            $product = Mage::getModel("catalog/product")->load($this->getProductId());
            if($product != null && $product->getId() == $this->getProductId())
            {
                return $product->getName();
            }
        }
        return Mage::helper("iqnomy_extension")->__("None");
    }
}