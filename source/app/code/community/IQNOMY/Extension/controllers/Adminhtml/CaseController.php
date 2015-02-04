<?php
header("X-XSS-Protection: 0");
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Adminhtml_CaseController extends Mage_Adminhtml_Controller_Action
{
    //The menu item that should be marked as active.
    private $menu = "iqnomy/iqnomy_extension3";
    
    private function init($contentBlock = null)
    {   
        $this->loadLayout();
        $this->_setActiveMenu($this->menu);
        if($contentBlock != null)
            $this->_addContent($this->getLayout()->createBlock($contentBlock));
        
        $this->renderLayout();
    }
    
    public function indexAction()
    {	
//        try
//        {
//            $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('id'));
//            if($case->getIsConfigurable() && $case->getContainer() == null)
//            {
//		$case->createContainer()->save(true);
//            }
//        }
//        catch(Exception $ex)
//        {
//	    //return $this->error($ex->getMessage(), "*/iqnomy/index");
//            return $this->error(Mage::helper("iqnomy_extension")->__("An error occurred while creating the container for this case. Are you sure that your IQNOMY-credentials are correct?"), "*/iqnomy/index");
//        }
        
        $this->init();
    }
    
    public function enableAction()
    {
        $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('id'));
        if($case != null && $case->getBanner() != null)
        {
            $banner = $case->getBanner();
            if($this->getRequest()->getParam('enable') == "true")
            {
                $enable = true;
                $message = Mage::helper('iqnomy_extension')->__("The case has been enabled.");
            }
            else
            {
                $enable = false;
                $message = Mage::helper('iqnomy_extension')->__("The case has been disabled.");
            }

            $banner->setIsEnabled($enable);
            $banner->save();

            return $this->success($message, "*/iqnomy/index");
        }
        return $this->_redirect("*/iqnomy/index");
    }
    
    public function resethtmlAction()
    {
	try
	{
	    $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('id'));
	    $container = $case->getContainer();
	    
	    if($container != null)
	    {
		$template = Mage::getModel("iqnomy_extension/template")->load($this->getRequest()->getParam('id'));
		$template->setId($this->getRequest()->getParam('id'));
		$template->setTemplate($case->getDefaultHtmlTemplate());
		$template->save();

		$liquidContents = $container->getLiquidContents();
		if($liquidContents != null)
		{
		    foreach($liquidContents as $liquidContent)
		    {
			$liquidContent->setHtml($case->getDefaultHtmlTemplate());
			$liquidContent->save();
		    }
		}
	    }
	    
	    $this->success(Mage::helper('iqnomy_extension')->__("The HTML has been reset."), "*/*/index/id/" . $this->getRequest()->getParam('id'));
	}
	catch(Exception $ex)
	{
	    $this->error(Mage::helper('iqnomy_extension')->__("An error occurred while resetting the HTML."), "*/*/index/id/" . $this->getRequest()->getParam('id'));
	}
    }
    
    public function saveAction()
    {
        if($data = $this->getRequest()->getPost())
        {
            $htmlId = $data["html_id"];
            $height = $data["height"];
            if(array_key_exists("is_enabled", $data) && $data["is_enabled"])
            {
                $isEnabled = true;
            }
            else
            {
                $isEnabled = false;
            }
            
            if($htmlId != null && $height > 0)
            {
                try
                {
                    $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('id'));
           
		    if(!$case->getIsNewsletterCase())
		    {
			$containerPlacements = array();
			foreach($data["container_pages_urls"] as $key => $pageUrl)
			{
			    $maxResults = $data["container_pages_max_results"][$key];
			    if($case->getId() == 2)
			    {
				$maxResults = $data["max_results"];
			    }
			    $containerPlacements[] = array("matchUrl" => $pageUrl, "maxResults" => $maxResults, "xpath" => "body", "enabled" => true);
			}
			$container = $case->getContainer();
			if($container == null)
			{
			    $container = $case->createContainer();
			}
			$container->setContainerplacements($containerPlacements);
			$container->save();

			$template = Mage::getModel("iqnomy_extension/template")->load($this->getRequest()->getParam('id'));
			$template->setTemplate($data["html"]);
			$template->save();

			$liquidContents = $container->getLiquidContents();
			if($liquidContents != null)
			{
			    foreach($liquidContents as $liquidContent)
			    {
				if($liquidContent->getHtml() != $data["html"])
				{
				    $liquidContent->setHtml($data["html"]);
				    $liquidContent->save();
				}
			    }
			}
		    }
		    else
		    {
			$template = Mage::getModel("iqnomy_extension/template")->load($this->getRequest()->getParam('id'));
			$template->setTemplate($data["html"]);
			$template->save();
			
			$container = $case->getContainer();
			if($container == null)
			{
			    $container = $case->createContainer();
			    $container->save();
			}
			
			$liquidContent = $case->getNewsletterLiquidContent();
			$liquidContent->setContainerId($case->getContainer()->getId());
			$liquidContent->setName($data["popup_title"]);
			$liquidContent->setPopupTitle($data["popup_title"]);
			$liquidContent->setPopupSubmitText($data["popup_submit_text"]);
			$liquidContent->setPopupAlttext($data["popup_alttext"]);
			$liquidContent->setPopupColoration($data["popup_coloration"]);
			$liquidContent->setPopupCloseIconColor($data["popup_close_icon_color"]);
			$liquidContent->setHtml($data["html"]);
			$liquidContent->setIsNewsletterContent(true);
			$liquidContent->setProductId(null);
			$liquidContent->setIsEnabled(true);
			
			$url = Mage::helper('iqnomy_extension')->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			if(substr($url, -1) == "/")
			{
			    $url = substr($url, 0, strlen($url) - 1);
			}
			$liquidContent->setWebshopUrl($url);
			
			$liquidContent->save();
		    }
		    
		    $banner = $case->getBanner();
                    if($banner == null)
                    {
                        $banner = Mage::getModel("iqnomy_extension/banner");
                        
                        $banner->setName($case->getName());
                        $banner->setContainerId($case->getContainer()->getId());
                        $banner->setDuration($case->getAnimationLength());
                        $banner->setPause($case->getAnimationPause());
			$banner->setIsNewsletter($case->getIsNewsletterCase());
                    }
		    
		    $banner->setAnimationType($case->getAnimationType());
                    $banner->setIsEnabled($isEnabled);
                    $banner->setHtmlId($htmlId);
                    $banner->setHeight($height);
		    
		    if($case->getId() == 2 && array_key_exists('use_magento_grid', $data))
		    {
			$banner->setAnimationType(10);
		    }
                    
                    $banner->save();
                    
                    $this->success(Mage::helper('iqnomy_extension')->__("The case settings have been saved."));
                }
                catch(Exception $ex)
                {
                    $this->error(Mage::helper('iqnomy_extension')->__("An error occurred while saving the case."));
                }
            }
            elseif($htmlId == null)
            {
                if(Mage::helper("iqnomy_extension")->bannersEnabled() && $this->getRequest()->getParam('id') == 1)
                {
                    $this->error(Mage::helper('iqnomy_extension')->__("No iBanner group was selected."));
                }
                else
                {
                    $this->error(Mage::helper('iqnomy_extension')->__("Please enter an HTML-id."));
                }
            }
            else
            {
                $this->error(Mage::helper('iqnomy_extension')->__("The height has to be greater than 0."));
            }
        }
        else
        {
            $this->error(Mage::helper('iqnomy_extension')->__("No data was found to save."));
        }
        $this->indexAction();
    }
    
    private function success($message, $redirectTo = null)
    {
        Mage::getSingleton('adminhtml/session')->addSuccess($message);
        
        if($redirectTo != null)
        {
            session_write_close();
            $this->_redirect($redirectTo);
        }
    }
    
    private function error($message, $redirectTo = null)
    {
        Mage::getSingleton('adminhtml/session')->addError($message);
        
        if($redirectTo != null)
        {
            session_write_close();
            $this->_redirect($redirectTo);
        }
    }
    
    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
	$session = Mage::getSingleton('admin/session');
        return ($session->isAllowed('iqnomy') || $session->isAllowed('system/iqnomy'));
    }
}