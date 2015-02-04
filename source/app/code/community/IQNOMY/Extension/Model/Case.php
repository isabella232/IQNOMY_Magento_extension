<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Case extends Varien_Object
{
    private $collection;
    
    public function getCollection($storeId = null)
    {
	if($storeId == null)
	{
	    $storeId = Mage::getSingleton('adminhtml/session')->getIqnomyStoreId();
	}
	
	if($this->collection == null)
	{            
	    $this->collection = new Varien_Data_Collection();
	    
            $helper = Mage::helper("iqnomy_extension");
            
            if(!$helper->moduleEnabled("IQNOMY_Banner"))
            {
                $case1 = Mage::getModel("iqnomy_extension/case");
                $case1->setId(1);
		$case1->setLabel("Slider personaliseren");
		$case1->setName("Slider personaliseren");
		if($storeId != null && $storeId > 0)
		{
		    $case1->setName($case1->getName().' - store '.$storeId);
		}
                $case1->setDescription("Als u de iBanner module heeft geinstalleerd dan kunt u deze direct personaliseren.");
                $case1->setConfigureText("Via deze case kunt u uw sliders en/of banners personaliseren. De door u ingestelde sliders en/of banners worden gebruikt als standaard content. Zodra het IQNOMY-platform een match heeft gevonden wordt deze standaard content vervangen door de content die het beste bij de bezoeker past.");
                $case1->setLiquidContentOverviewText("De liquid contents die u ingesteld heeft vervangen de standaard content indien er een match is tussen de interesses van de bezoeker en de liquid contents.");
                $case1->setMaxResults(3);
                $case1->setSelectionMethods(array("DIMENSIONCLASSIFICATION" => 100));
                $case1->setFallbackType("FIXED");
                $case1->setAnimationType(IQNOMY_Extension_Model_Banner::animationTypeSlide);
                $case1->setAnimationLength(1000);
                $case1->setAnimationPause(6000);
                $case1->setUseProduct(false);
                $case1->setDefaultHeight(200);
                $case1->setHtmlTemplate('<a href="${url}" title="${title}" target="${target}">'.PHP_EOL.'<img src="${imageUrl}" alt="${alt}">'.PHP_EOL.'</a>');
                $case1->setExampleUrl("http://www.slideshare.net/iqnomy/case3colours");
                $case1->setMoreInfoUrl("http://www.slideshare.net/iqnomy/coloursnl-verhoogt-click-through-rate-door-gepersonaliseerde-content-30379965");
                $case1->setFormType("slider");
                $case1->setIsConfigurable(true);
                $case1->setUseHtmlTemplate(true);
                $case1->setTemplateVariables('${url}, ${title}, ${imageUrl}, ${target} & ${alt}');
                $case1->setLiquidContentOverviewTitle("Selected banners");
                $case1->setLiquidContentAddTitle("Add banner");
                $case1->setLiquidContentEdit("Edit banner");
                $case1->setLiquidContentNew("Add banner");
		$case1->setLiquidContentDeleteMessageSuccess("The banner has been deleted.");
		$case1->setLiquidContentDeleteMessageError("The banner could not be deleted.");
		$case1->setLiquidContentSavedMessageSuccess("The banner has been added to the case.");
		$case1->setHtmlIdNote('Warning: Enter the ID of the HTML-element in which the personalized banner should be placed. For example: &lt;div id=&quot;<b>this-is-an-id</b>&quot;&gt; or &lt;ul id=&quot;<b>this-is-also-an-id</b>&quot;&gt; or &lt;span id=&quot;<b>this-is-also-an-id</b>&quot;&gt;, etc.');
		$case1->setIsNewsletterCase(false);
		$case1->setLiquidContentAddEnabled(true);
		$case1->setAdvancedEnabled(true);
		$case1->setStartWithUrl("http://www.slideshare.net/iqnomy/starten-met-banner-personalisatie");
            }
            else
            {
                $case1 = Mage::getModel("iqnomy_extension/case");
                $case1->setId(1);
		$case1->setLabel("IQNOMY slider personaliseren");
                $case1->setName("IQNOMY slider personaliseren");
		if($storeId != null && $storeId > 0)
		{
		    $case1->setName($case1->getName().' - store '.$storeId);
		}
                $case1->setDescription("Via deze case kunt u uw iqnomy sliders personaliseren.");
                $case1->setMaxResults(3);
		//$case1->setSelectionMethods(array("RANDOM" => 100));
                $case1->setSelectionMethods(array("DIMENSIONCLASSIFICATION" => 100));
                $case1->setFallbackType("FIXED");
//                $case1->setAnimationType(IQNOMY_Extension_Model_Banner::animationTypeSlide);
//                $case1->setAnimationLength(500);
//                $case1->setAnimationPause(3000);
//                $case1->setUseProduct(false);
//                $case1->setDefaultHeight(200);
//                $case1->setHtmlTemplate('<a href="${url}" title="${title}" target="${target}">'.PHP_EOL.'<img src="${imageUrl}" alt="${alt}">'.PHP_EOL.'</a>');
                $case1->setExampleUrl("http://www.slideshare.net/iqnomy/case3colours");
                $case1->setMoreInfoUrl("http://www.slideshare.net/iqnomy/coloursnl-verhoogt-click-through-rate-door-gepersonaliseerde-content-30379965");
//                $case1->setFormType("slider");
                $case1->setIsConfigurable(true);
                $case1->setConfigureLink();
                $case1->setDisableStatus(true);
		$case1->setIsNewsletterCase(false);
		$case1->setLiquidContentAddEnabled(true);
		$case1->setIsIqnomyCase(true);
            }
            
	    $this->collection->addItem($case1);
	    
	    $case2 = Mage::getModel("iqnomy_extension/case");
            $case2->setId(2);
	    $case2->setLabel("Voor u geselecteerd");
	    $case2->setName("Voor u geselecteerd");
	    if($storeId != null && $storeId > 0)
	    {
		$case2->setName($case2->getName().' - store '.$storeId);
	    }
	    $case2->setDescription("Direct de bezoeker van uw homepage op basis van zijn of haar interesse de juiste producten bieden.");
	    $case2->setMaxResults(4);
	    $case2->setSelectionMethods(array("DIMENSIONCLASSIFICATION" => 100));
	    $case2->setFallbackType("FIXED");
	    $case2->setAnimationType(IQNOMY_Extension_Model_Banner::animationTypeNone);
            $case2->setUseProduct(true);
            $case2->setDefaultHeight(200);
	    $case2->setHtmlTemplate('<li class="item" style="display: block; text-align: center; float: left; margin: 0px 18px;">'.PHP_EOL.'<a href="${url}" title="${title}" class="product-image"><img src="${imageUrl}" alt="${title}"></a>'.PHP_EOL.'<h2 class="product-name"><a href="${url}" title="${title}">${title}</a></h2>'.PHP_EOL.'<div class="price-box">'.PHP_EOL.'<span class="regular-price">'.PHP_EOL.'<span class="price">${price}</span>'.PHP_EOL.'</span>'.PHP_EOL.'</div>'.PHP_EOL.'</li>');
            $case2->setExampleUrl("http://www.slideshare.net/iqnomy/ikbenzomooinl-iqnomy-case");
            $case2->setMoreInfoUrl("http://www.slideshare.net/iqnomy/ikbenzomooinl-verhoogt-clickthrough-rate-door-gepersonaliseerde-content");
            $case2->setFormType("product");
	    $case2->setIsConfigurable(true);
            $case2->setUseHtmlTemplate(true);
            $case2->setTemplateVariables('${url}, ${title}, ${imageUrl}, ${target}, ${price} & ${specialPrice}');
            $case2->setLiquidContentOverviewTitle("Selected products");
            $case2->setLiquidContentAddTitle("Add product");
            $case2->setLiquidContentEdit("Edit product");
            $case2->setLiquidContentNew("Add product");
	    $case2->setLiquidContentDeleteMessageSuccess("The product has been deleted from the case.");
	    $case2->setLiquidContentDeleteMessageError("The product could not be deleted from the case.");
	    $case2->setLiquidContentSavedMessageSuccess("The product has been added to the case.");
            $case2->setHtmlIdNote('Warning: Enter the ID of the HTML-element in which the personalized products should be placed. For example: &lt;div id=&quot;<b>this-is-an-id</b>&quot;&gt; or &lt;ul id=&quot;<b>this-is-also-an-id</b>&quot;&gt; or &lt;span id=&quot;<b>this-is-also-an-id</b>&quot;&gt;, etc.');
	    $case2->setIsNewsletterCase(false);
	    $case2->setLiquidContentAddEnabled(true);
	    $case2->setAdvancedEnabled(true);
	    $case2->setStartWithUrl("http://www.slideshare.net/iqnomy/starten-met-voor-jou-geselecteerd-35003281");
	    
	    $this->collection->addItem($case2);
            
            $case3 = Mage::getModel("iqnomy_extension/case");
            $case3->setId(3);
	    $case3->setLabel("Lead generation");
	    $case3->setName("Lead generation");
	    if($storeId != null && $storeId > 0)
	    {
		$case3->setName($case3->getName().' - store '.$storeId);
	    }
	    $case3->setDescription("Zorg ervoor dat bezoekers die uw website verlaten wel hun e-mailadres achter laten.");
	    $case3->setIsConfigurable(true);
	    $case3->setIsNewsletterCase(true);
            $case3->setConfigureText("");
	    $case3->setFallbackType("FIXED");
	    $case3->setSelectionMethods(array("DIMENSIONCLASSIFICATION" => 100));
	    $case3->setAnimationType(IQNOMY_Extension_Model_Banner::animationTypeNone);
	    $case3->setAnimationLength(0);
	    $case3->setAnimationPause(0);
	    $case3->setLiquidContentAddEnabled(false);
	    $case3->setUseHtmlTemplate(true);
            $case3->setHtmlTemplate('<style>'.PHP_EOL.'#iq-newspopup-lightbox {'.PHP_EOL.'position: fixed;'.PHP_EOL.'top: 0;'.PHP_EOL.'left: 0;'.PHP_EOL.'width: 100%;'.PHP_EOL.'height: 100%;'.PHP_EOL.'z-index: 2000000000;'.PHP_EOL.'background: rgba(255, 255, 255, 0.5);'.PHP_EOL.'}'.PHP_EOL.''.PHP_EOL.'#iq-newpopup{'.PHP_EOL.'position: absolute;'.PHP_EOL.'top: 50%;'.PHP_EOL.'left: 50%;'.PHP_EOL.'width: ${popup_width}px;'.PHP_EOL.'margin-left: -${popup_width_half}px;'.PHP_EOL.'margin-top: -${popup_height_half}px;'.PHP_EOL.'-webkit-box-shadow: 0px 2px 4px 1px #aaa;'.PHP_EOL.'box-shadow: 0px 2px 4px 1px #aaa;'.PHP_EOL.'-webkit-border-top-left-radius: 4px;'.PHP_EOL.'-webkit-border-top-right-radius: 4px;'.PHP_EOL.'-moz-border-radius-topleft: 4px;'.PHP_EOL.'-moz-border-radius-topright: 4px;'.PHP_EOL.'border-top-left-radius: 4px;'.PHP_EOL.'border-top-right-radius: 4px;'.PHP_EOL.'border: 1px solid ${popup_coloration};'.PHP_EOL.'background: #FFFFFF;'.PHP_EOL.'overflow: hidden;'.PHP_EOL.'}'.PHP_EOL.''.PHP_EOL.'#iq-newpopup-close{'.PHP_EOL.'position: absolute;'.PHP_EOL.'right: 10px;'.PHP_EOL.'top: 12px;'.PHP_EOL.'width: 16px;'.PHP_EOL.'height: 16px;'.PHP_EOL.'z-index: 99;'.PHP_EOL.'cursor: pointer;'.PHP_EOL.'}'.PHP_EOL.''.PHP_EOL.'#iq-newpopup-close-icon {'.PHP_EOL.'background-image: url(http://www.iqnomy.com/iq-demo/nieuwsbrief/close-button/close-icon-${popup_close_icon_color}.png);'.PHP_EOL.'background-repeat: no-repea'.PHP_EOL.'t;width: 16px;'.PHP_EOL.'height: 16px;'.PHP_EOL.'}'.PHP_EOL.''.PHP_EOL.'#iq-newpopup-title {'.PHP_EOL.'font-family: Verdana, Geneva, sans-serif;'.PHP_EOL.'font-weight: normal;'.PHP_EOL.'font-size: 16px;'.PHP_EOL.'color: #eee;'.PHP_EOL.'background: ${popup_coloration};'.PHP_EOL.'width: 100%;'.PHP_EOL.'padding: 10px;'.PHP_EOL.'}'.PHP_EOL.''.PHP_EOL.'#iq-newpopup-image-big {'.PHP_EOL.'margin:10px 10px 0px 10px;'.PHP_EOL.'}'.PHP_EOL.'.iq-newpopup-input-row {'.PHP_EOL.'height: 39px;'.PHP_EOL.'margin:10px 10px 0px 10px;'.PHP_EOL.'overflow: hidden;'.PHP_EOL.'}'.PHP_EOL . '.iq-newpopup-input-row .validation-advice'.PHP_EOL . '{'.PHP_EOL . 'float: left;'.PHP_EOL . 'width: 30%;'.PHP_EOL . 'background: none;'.PHP_EOL . 'clear: initial;'.PHP_EOL. '}'.PHP_EOL .PHP_EOL.''.PHP_EOL.'.iq-newpopup-input {'.PHP_EOL.'font-family: Verdana, Geneva, sans-serif;'.PHP_EOL.'font-size: 12px;'.PHP_EOL.'color: #aaa;'.PHP_EOL.'background: #fff;'.PHP_EOL.'border: 1px solid ${popup_coloration};'.PHP_EOL.'width: 35%;'.PHP_EOL.'position: relative;'.PHP_EOL.'float: left;'.PHP_EOL.'padding: 7px;'.PHP_EOL.'-webkit-border-radius: 4px;'.PHP_EOL.'-moz-border-radius: 4px;'.PHP_EOL.'border-radius: 4px;'.PHP_EOL.'outline: none;'.PHP_EOL.'margin:0px 0px 10px 0px'.PHP_EOL.'}'.PHP_EOL.''.PHP_EOL.'.iq-newpopup-input.iqmailonly{'.PHP_EOL.'width: 40%; '.PHP_EOL.'}'.PHP_EOL.''.PHP_EOL.'.iq-newpopup-input.iqsubmit{'.PHP_EOL.'width: 90px;'.PHP_EOL.'color: #fff;'.PHP_EOL.'background: ${popup_coloration};'.PHP_EOL.'cursor: pointer;'.PHP_EOL.'float: right;'.PHP_EOL.'text-align:center;'.PHP_EOL.'}'.PHP_EOL.''.PHP_EOL.'</style>'.PHP_EOL.'<div id="iq-newspopup-lightbox">'.PHP_EOL.'<div id="iq-newpopup">'.PHP_EOL.'<div id="iq-newpopup-close" onclick="document.getElementById(\'iq-newspopup-lightbox\').style.display = \'none\';">'.PHP_EOL.'<div id="iq-newpopup-close-icon"></div>'.PHP_EOL.'</div>'.PHP_EOL.'<div id="iq-newpopup-title">${popup_title}</div>'.PHP_EOL.'<div id="iq-newpopup-image-big">'.PHP_EOL.'<img src="${popup_imageurl}" alt="${popup_alttext}">'.PHP_EOL.'</div>'.PHP_EOL.'<div class="iq-newpopup-input-row">'.PHP_EOL.'<form action="${webshop_url}/index.php/newsletter/subscriber/new/by/iqnomy/" method="post" id="newsletter-validate-detail-iqnomy">'.PHP_EOL.'<input type="text" value="Jouw e-mail" name="email" id="iq-mail-from" class="iq-newpopup-input iqmailonly required-entry validate-email" onblur="if (this.value==\'\') this.value=this.defaultValue" onclick="if (this.defaultValue==this.value) this.value=\'\'" />'.PHP_EOL.'<input type="submit" value="${popup_submit_text}" name="subscribe" id="iq-newpopup-submit" class="iq-newpopup-input iqsubmit" />'.PHP_EOL.'</form>'.PHP_EOL.'</div>'.PHP_EOL.'<div id="_iqLqcid" style="display:none;">${lqcid}</div>'.PHP_EOL.'</div>'.PHP_EOL.'</div>');
	    $case3->setAdvancedEnabled(false);
	    $case3->setLiquidContentOverviewTitle("Selected pop-up");
	    $case3->setExampleUrl("http://www.slideshare.net/iqnomy/case22viadierenwinkel");
	    $case3->setStartWithUrl("http://www.slideshare.net/iqnomy/starten-met-e-mail-popup");
	    
	    $this->collection->addItem($case3);
	}
        
        foreach($this->collection as $case)
        {
            if($case->getUseHtmlTemplate())
            {
		$case->setDefaultHtmlTemplate($case->getHtmlTemplate());
                $template = Mage::getModel("iqnomy_extension/template")->load($case->getId());
                if($template->getId() == $case->getId())
                {
                    $case->setHtmlTemplate($template->getTemplate());
                }
                else
                {
                    $template->setId($case->getId());
                    $template->setTemplate($case->getHtmlTemplate());
                    $template->save();
                }
            }
        }
        
	return $this->collection;
    }
    
    private $container = null;
    public function getContainer()
    {
	if($this->container == null)
	{
	    $this->container = Mage::getModel("iqnomy_extension/container")->findByName($this->getName());
	}
	
        return $this->container;
    }
    
    public function getNewsletterLiquidContent()
    {
	if($this->getContainer() == null || $this->getContainer()->getLiquidContents()->count() == 0)
	{
	    $liquidContent = Mage::getModel("iqnomy_extension/liquidcontent");
	    $liquidContent->setPopupTitle("Nieuwsbrief");
	    $liquidContent->setPopupSubmitText("Aanmelden");
	    
	    return $liquidContent;
	}
	/** @var IQNOMY_Extension_Helper_Data $_helper */
        $_helper = Mage::helper('iqnomy_extension');

        /** @var IQNOMY_Extension_Model_Webservice $webservice */
        $webservice = $_helper->getWebservice();        
	
	$liquidContent = $this->getContainer()->getLiquidContents()->getFirstItem();
	$dimensionValues = array();
	if($liquidContent->getProperties() != null)
	{
	    $dimensions = $webservice->getDimensions();
	    foreach($liquidContent->getProperties() as $property)
	    {
		$propertyId = $this->getDimensionPropertyId($dimensions, $property["dimensionId"], $property["value"]);
		if($propertyId != null)
		{
		    $dimensionValues[$property['dimensionId']] = $propertyId;
		}
	    }
	}
	if(count($dimensionValues) > 0)
	{
	    $liquidContent->setDimensions($dimensionValues);
	}
	return $liquidContent;
    }
    
    public function getDimensionPropertyId($dimensions, $dimensionId, $value)
    {
        if($dimensions != null)
        {
            foreach($dimensions as $dimension)
            {
                if($dimension["id"] == $dimensionId)
                {
                    foreach($dimension["dimensionProperty"] as $property)
                    {
                        if($property["value"] == $value)
                        {
                            return $property["id"];
                        }
                    }
                }
            }
        }
        return null;
    }
    
    public function getStatus()
    {
        $helper = Mage::helper("iqnomy_extension");
        
        if($this->getDisableStatus())
        {
            return "<b style='color: #0D9E00;'>".$helper->__("Case ok")."</b>";
        }
                
        if(!$this->getIsConfigurable())
        {
            return "<b>".$helper->__("N/A")."</b>";
        }
        
	$container = $this->getContainer();
	if($container == null || $container->getId() == null)
        {
            return "<b>".$helper->__("Not used")."</b>";
        }
        
        if($container->getLiquidContents() == null || $container->getLiquidContents()->count() <= 0)
        {
            return "<b style='color: #CA0000;'>".$helper->__("No liquid contents")."</b>";
        }
        
        $banner = $this->getBanner();
        if($banner == null || $banner->getContainer() == null || $banner->getContainer()->getId() != $container->getId())
        {
            return "<b style='color: #CA0000;'>".$helper->__("Not placed")."</b>";
        }
        
        if(!$banner->getIsEnabled())
        {
            return "<b style='color: #CA0000;'>".$helper->__("Disabled")."</b>";
        }
        
        return "<b style='color: #0D9E00;'>".$helper->__("Case ok")."</b>";
    }
    
    public function getEnableDisable()
    {
        $helper = Mage::helper("iqnomy_extension");
        
        if($this->getDisableStatus() || !$this->getIsConfigurable())
        {
            return null;
        }
        
	$container = $this->getContainer();
	if($container == null || $container->getLiquidContents() == null || $container->getLiquidContents()->count() <= 0)
        {
            return null;
        }
        
        $banner = $this->getBanner();
        if($banner == null || $banner->getContainer() == null || $banner->getContainer()->getId() != $container->getId())
        {
            return null;
        }
        
        if($banner->getIsEnabled())
        {
            return false;
        }
        
        return true;
    }
    
    public function getBanner()
    {
        $container = $this->getContainer();
        if($container == null)
        {
            return null;
        }
        
        return Mage::getModel("iqnomy_extension/banner")->findByNameAndContainer($this->getName(), $container);
    }
    
    public function countLiquidContents()
    {
        $container = Mage::getModel("iqnomy_extension/container")->findByName($this->getName());
	if($container != null)
        {
            if($container->getLiquidContents() != null)
            {
                return $container->getLiquidContents()->count();
            }
        }
        return 0;
    }
    
    public function load($id)
    {
        foreach($this->getCollection() as $case)
        {
            if($case->getId() == $id)
            {
                return $case;
            }
        }
        return null;
    }
    
    public function createContainer()
    {
	$container = Mage::getModel("iqnomy_extension/container");
	$container->setName($this->getName());
	$container->setIsEnabled(true);
	
	$selectionMethods = array();
	foreach($this->getSelectionMethods() as $type => $value)
	{
	    $selectionMethods[] = array("type" => $type, "value" => $value);
	}
	$container->setContentSelectionMethods($selectionMethods);
	$container->setFallbackType($this->getFallbackType());
	    
	if(!$this->getIsNewsletterCase())
	{
	    $placements = array();
	    
	    if($this->getIsIqnomyCase())
	    {
		$url = Mage::helper('iqnomy_extension')->getBaseUrl();
		$url = str_replace("index.php", "", $url);
		$url = str_replace("http://", "", $url);
		$url = str_replace("https://", "", $url);
		$url = explode("/", $url);
		$url = $url[0];
		
		$placements[] = array("matchRegEx" => $url, "maxResults" => $this->getMaxResults(), "xpath" => "body", "enabled" => true);
	    }
	    else
	    {
		$url = Mage::helper('iqnomy_extension')->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		if(substr($url, -1) != "/")
		{
		    $url .= "/";
		}

		$placements[] = array("matchUrl" => $url, "maxResults" => $this->getMaxResults(), "xpath" => "body", "enabled" => true);
	    }
	}
	else
	{
	    $nr = "IQ" . str_replace(" ", "", $container->getName());
	    $nr = str_replace("-", "", $nr);
	    
	    $url = Mage::helper('iqnomy_extension')->getBaseUrl();
	    $url = str_replace("index.php", "", $url);
	    $url = str_replace("http://", "", $url);
	    $url = str_replace("https://", "", $url);
	    $url = explode("/", $url);
	    $url = $url[0];
	    
	    $placements = array();
	    $placements[] = array(
		"enabled" => true,
		"xpath" => 'body',
		"maxResults" => 1,
		"preInjectionScript" => "var _iqContentBox".$nr." = document.createElement('div'); _iqContentBox".$nr.".id = '_iqContentBox".$nr."'; _iqContentBox".$nr.".style.display = 'none'; document.getElementsByTagName('body')[0].appendChild(_iqContentBox".$nr.");",
		"postInjectionScript" => 'window.ieVersion = function () { var regular; if (navigator.appName == "Microsoft Internet Explorer") { regular = new RegExp("MSIE ([0-9]+)"); return regular.exec(navigator.userAgent) ? RegExp.$1 : false; } else if (navigator.appName == "Netscape") { regular = new RegExp(".NET CLR ([0-9.]*); I63rv:([0-9]+)"); return regular.exec(navigator.userAgent) ? RegExp.$2 : false; } return false; }; window.lostfocus = function (event) { event = event ? event : window.event; var from = event.relatedTarget || event.toElement; if (!from || from.nodeName == "HTML") { var margin = 20; if (event.clientY <= 0 + margin && !iqIsShown) { iqIsShown = true; IQImpressor.trackEvent(_iqsTenant, "WEBSHOP", {contentShowed_${containerid}:"true"}); document.getElementById("_iqContentBox'.$nr.'").style.display = "block"; } } }; (function () { var _iqContentBox'.$nr.' = document.getElementById("_iqContentBox'.$nr.'"); if (typeof _iqContentBox'.$nr.' === undefined || _iqContentBox'.$nr.'.innerHTML.length === 0) { return false; } var newsletterSubscriberFormDetailIqnomy = new VarienForm("newsletter-validate-detail-iqnomy"); window.iqIsShown = false; var iqIeVersion = ieVersion(); if (iqIeVersion && iqIeVersion <= 8) { document.attachEvent("onmouseout", lostfocus); } else { document.addEventListener("mouseout", lostfocus, false); } var iqCloseImageBox = document.getElementById("iq-newpopup-close"); iqCloseImageBox.onclick = function() {  IQImpressor.trackEvent(_iqsTenant, "WEBSHOP", {contentClosed_${containerid}:"true"}); document.getElementById("_iqContentBox'.$nr.'").style.display = "none"; }; var iqSubmitForm = document.getElementById("newsletter-validate-detail-iqnomy"); iqSubmitForm.onsubmit= function(){ if(newsletterSubscriberFormDetailIqnomy.validator.validate()) { IQImpressor.trackEvent(_iqsTenant, "WEBSHOP", {contentSentOrClicked_${containerid}:"true"}); var iqLqcid = document.getElementById("_iqLqcid").innerHTML; IQImpressor.trackContainerClick(iqLqcid); document.getElementById("_iqContentBox'.$nr.'").style.display = "none"; } return true; }; })();',
		"matchRegEx" => $url
	    );
	}
	
	if(count($placements) > 0)
	{
	    $container->setContainerplacements($placements);        
	}
	
        return $container;
    }
    
    public function getIBanners()
    {
        if(Mage::helper("iqnomy_extension")->bannersEnabled())
        {
            $banner = $this->getBanner();
            if($banner != null)
            {
                $htmlId = $banner->getHtmlId();
                
                $ibannerGroups = Mage::getModel("ibanners/group")->getCollection();
                foreach($ibannerGroups as $ibannerGroup)
                {
                    if("ibanners-".$ibannerGroup->getCode() == $htmlId)
                    {
                        return $ibannerGroup->getBannerCollection();
                    }
                }
            }
        }
        return null;
    }
}