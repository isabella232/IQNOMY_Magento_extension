<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Adminhtml_ContainerController extends Mage_Adminhtml_Controller_Action
{
    //Setting the menu item that should be marked as active.
    private $menu = "system";
        
    private function init()
    {
        $this->loadLayout();        
        $this->_setActiveMenu($this->menu);
        $this->renderLayout();
    }
    
    public function indexAction()
    {
        $this->init();
    }

    public function gridAction()
    {
        $this->init();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function enableAction()
    {
	$container = Mage::getModel("iqnomy_extension/container")->load($this->getRequest()->getParam('id'));
        if($container != null)
        {
            if($this->getRequest()->getParam('enable') == "true")
            {
                $enable = true;
                $message = Mage::helper('iqnomy_extension')->__("The container has been enabled.");
            }
            else
            {
                $enable = false;
                $message = Mage::helper('iqnomy_extension')->__("The container has been disabled.");
            }

            $container->setIsEnabled($enable);
            $container->save();
	    $container->saveBanner();

            return $this->success($message, "*/iqnomy/index");
        }
        return $this->_redirect("*/iqnomy/index");
    }

    public function saveAction()
    {
        $container = Mage::getSingleton("iqnomy_extension/container");
        $id = $this->getRequest()->getParam('id');
        if($id && !$container->exists($id))
        {
            $this->error(Mage::helper('iqnomy_extension')->__("The container does not exist."), "*/*");
            return;
        }

        if ($data = $this->getRequest()->getPost())
        {
            if($id)
            {
                $data["id"] = $id;
            }
            
            $selectionMethods = array();
            if(array_key_exists("selection_methods", $data) && is_array($data["selection_methods"]))
            {
                foreach($data["selection_methods"] as $type => $value)
                {
                    $selectionMethods[] = array("type" => $type, "value" => $value);
                }
            }
            $data["content_selection_methods"] = $selectionMethods;
            
            $placements = array();
            if(array_key_exists("container_pages_urls", $data) && array_key_exists("container_pages_max_results", $data) && is_array($data["container_pages_urls"]) && is_array($data["container_pages_max_results"]) && count($data["container_pages_urls"]) == count($data["container_pages_max_results"]))
            {
                $urls = $data["container_pages_urls"];
                $maxResults = $data["container_pages_max_results"];
                
                foreach($urls as $key => $url)
                {
                    if(array_key_exists($key, $maxResults))
                    {
                        $placements[] = array("matchUrl" => $url, "maxResults" => $maxResults[$key], "xpath" => "body", "enabled" => true);
                    }
                }
            }
            $data["containerplacements"] = $placements;
                        
            $container->setData($data);
            
            try
            {
                $container->save();
		$container->saveBanner();
                $this->success(Mage::helper('iqnomy_extension')->__("The container has been saved."), "*/*");
            }
            catch(Exception $ex)
            {
                $this->error(Mage::helper('iqnomy_extension')->__($ex->getMessage()));
            }
            
            $this->init();
        }
        else
        {
            $this->error(Mage::helper('iqnomy_extension')->__("No data was found to save."), "*/*");
            return;
        }
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $container = Mage::getSingleton("iqnomy_extension/container");
        if($id)
        {
            try
            {
                $container->load($id);
		
		$banner = $container->getBanner();
		if($banner != null)
		{
		    $bannerData = $banner->getData();
		    if(array_key_exists("id", $bannerData))
		    {
			unset($bannerData["id"]);
		    }
		    if(array_key_exists("name", $bannerData))
		    {
			unset($bannerData["name"]);
		    }
		    if(array_key_exists("container_id", $bannerData))
		    {
			unset($bannerData["container_id"]);
		    }
		    $container->setData(array_merge($container->getData(), $bannerData));
		}
            }
            catch(Exception $ex)
            {
                $this->error(Mage::helper('iqnomy_extension')->__($ex->getMessage()), "*/*");
            }
        }
        
        $this->init();
    }

    public function deleteAction()
    {
        $container = Mage::getSingleton("iqnomy_extension/container");
        $id = $this->getRequest()->getParam('id');
        if(!$id || !$container->exists($id))
        {
            $this->error(Mage::helper('iqnomy_extension')->__("The container does not exist."), "*/*");
            return;
        }
        
        try
        {
            $container->delete($id);
	    $banner = Mage::getModel("iqnomy_extension/banner")->findByNameAndContainer($container->getName(), $container);
	    if($banner != null)
	    {
		$banner->delete();
	    }
            $this->success(Mage::helper('iqnomy_extension')->__("The container has been deleted."), "*/*");
        }
        catch(Exception $ex)
        {
            $this->error(Mage::helper('iqnomy_extension')->__("The container could not be removed."), "*/*");
        }
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
