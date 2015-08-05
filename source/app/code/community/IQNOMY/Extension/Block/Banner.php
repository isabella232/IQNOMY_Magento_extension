<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Banner extends Mage_Core_Block_Abstract
{   
    protected function _toHtml()
    {
        $html = "";
        $tenantId = Mage::getStoreConfig('iqnomy_extension/account/tenant_id');
        if($tenantId != null && $tenantId > 0)
        {
            //Loading the banner collection.
            $bannerCollection = Mage::getModel('iqnomy_extension/banner')->getCollection(false);
            if($bannerCollection->count() > 0)
            {
                $preview = false;
                if(array_key_exists("preview", $_GET) && $_GET["preview"] == "true")
                {
                    $preview = true;
                }
                
                $placementId = null;
                
                $html .= "<div id='iqNewsLetterContent'></div>";
                $html .= "<script type='text/javascript'>";
		
		if(array_key_exists("placement_id", $_GET))
                {
                    $placementId = $_GET["placement_id"];
		    $html .= "iqIsPreview = true;";
                }
		
                //IQJquery defined in iqnomy/IQJquery.js
                $html .= "IQJquery(document).ready(function() {";
                foreach($bannerCollection as $banner)
                {
                    $enabled = "false";
                    if($banner->getIsEnabled())
                    {
			$enabled = "true";
                    }
		    elseif($preview && $placementId == $banner->getId() && !array_key_exists("liquid_content_id", $_GET))
		    {
			$enabled = "true";
		    }
		    
                    $htmlId = $banner->getHtmlId();
                    $containerId = (int)$banner->getContainerId();
                    $height = (int)$banner->getHeight();
                    if($banner->getAnimationType() == IQNOMY_Extension_Model_Banner::animationTypeFade)
                    {
                        $animationType = "fading";
                        $duration = (int)$banner->getDuration();
                        $pause = (int)$banner->getPause();
                    }
                    elseif($banner->getAnimationType() == IQNOMY_Extension_Model_Banner::animationTypeSlide)
                    {
                        $animationType = "sliding";
                        $duration = (int)$banner->getDuration();
                        $pause = (int)$banner->getPause();
                    }
                    else
                    {
                        $animationType = "none";
                        $duration = 0;
                        $pause = 0;
                    }

                    if($banner->getIsIqnomyBanner())
                    {
                        $isIqnomyBanner = "true";
                    }
                    else
                    {
                        $isIqnomyBanner = "false";
                    }
		    
		    if($banner->getIsNewsletter())
		    {
			$isNewsletter = "true";
		    }
		    else
		    {
			$isNewsletter = "false";
		    }

		    if($banner->getAnimationType() == 10)
		    {
			$html .= "iqAddProductSelection('".$htmlId."', ".$containerId.", ".$enabled.", '".Mage::getUrl('iqnomy/product/test')."');";
		    }
		    else
		    {
			//Javascript function found in iqnomy/IQBanner.js
			$html .= "iqAddBanner('".$htmlId."', ".$containerId.", ".$height.", '".$animationType."', '".$duration."', '".$pause."', ".$isIqnomyBanner.", ".$enabled.", ".$isNewsletter.");";
		    }
                }
                
		if(array_key_exists("preview", $_GET) && $_GET["preview"] == "true" && array_key_exists("liquid_content_id", $_GET))
		{
		    $liquidContent = Mage::getModel("iqnomy_extension/liquidcontent")->load($_GET["liquid_content_id"]);
		    if($liquidContent != null)
		    {
			$container = $liquidContent->getContainer();
			if($container != null)
			{
			    $html .= "//<![CDATA[\n" . "previewLiquidContent(".$container->getId().", ".$liquidContent->getRawHtmlTemplate().");\n" . "//]]>\n";
			}
		    }
		}
		
                $html .= "});";
                
                $html .= "</script>";
            }            
        }
        
        return $html;
    }
}