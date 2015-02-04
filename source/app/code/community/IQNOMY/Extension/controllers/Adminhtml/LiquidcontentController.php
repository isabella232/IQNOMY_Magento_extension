<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Adminhtml_LiquidContentController extends Mage_Adminhtml_Controller_Action
{
    //Setting the menu item that should be marked as active.
    private $menu = "system";
    
    public function _construct()
    {
        parent::_construct();
    }
    
    private function init()
    {
        if($this->getRequest()->getParam('case'))
        {
            $this->menu = "iqnomy";
        }
        
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
    
    public function duplicateAction()
    {
        $this->editAction(true);
    }

    public function saveAction()
    {
        $content = Mage::getSingleton("iqnomy_extension/liquidcontent");
        $id = $this->getRequest()->getParam('id');
        if($id && !$content->exists($id))
        {
            if($this->getRequest()->getParam('case'))
            {
                $this->error(Mage::helper('iqnomy_extension')->__("The liquid content does not exist."), '*/case/index/id/'.$this->getRequest()->getParam('case'));
            }
            else
            {
                $this->error(Mage::helper('iqnomy_extension')->__("The liquid content does not exist."), "*/*");
            }
            return;
        }

        if ($data = $this->getRequest()->getPost())
        {
            $data["iqnomy_banner_value_id"] = null;
            
            if($id)
            {
                $data["id"] = $id;
            }
	    
	    if(array_key_exists("existing_image", $data) && $data["existing_image"] != null)
	    {
		$data["image_path"] = $data["existing_image"];
		unset($data["existing_image"]);
	    }
            
            if($this->getRequest()->getParam('case'))
            {
                $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('case'));
                if($case->getFormType() == "slider")
                {
                    $data["name"] = $data["title"];
                }
            }
                        
            $content->setData($data);
            
            try
            {
                $content->save();
                if($this->getRequest()->getParam('case'))
                {
		    $case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('case'));
                    $this->success(Mage::helper('iqnomy_extension')->__($case->getLiquidContentSavedMessageSuccess()), '*/case/index/id/'.$this->getRequest()->getParam('case'));
                }
		elseif($this->getRequest()->getParam('container'))
                {
                    $this->success(Mage::helper('iqnomy_extension')->__("The liquid content has been saved."), '*/*/index/container/'.$this->getRequest()->getParam('container'));
                }
                else
                {
                    $this->success(Mage::helper('iqnomy_extension')->__("The liquid content has been saved."), "*/*");
                }
            }
            catch(Exception $ex)
            {
                $this->error(Mage::helper('iqnomy_extension')->__($ex->getMessage()));
            }
            
            $this->init();
        }
        else
        {
            if($this->getRequest()->getParam('case'))
            {
                $this->error(Mage::helper('iqnomy_extension')->__("No data was found to save."), '*/case/index/id/'.$this->getRequest()->getParam('case'));
            }
	    elseif($this->getRequest()->getParam('container'))
	    {
		$this->error(Mage::helper('iqnomy_extension')->__("No data was found to save."), '*/*/index/container/'.$this->getRequest()->getParam('container'));
	    }
            else
            {
                $this->error(Mage::helper('iqnomy_extension')->__("No data was found to save."), "*/*");
            }
            return;
        }
    }

    public function editAction($isCopy = false)
    {
        $id = $this->getRequest()->getParam('id', null);
        
        if($isCopy)
        {
            $this->getRequest()->setParam("id", null);
        }
        
        $content = Mage::getSingleton("iqnomy_extension/liquidcontent");
        if($id)
        {
            try
            {
                $content->load($id);
            }
            catch(Exception $ex)
            {
                if($this->getRequest()->getParam('case'))
                {
                    $this->error(Mage::helper('iqnomy_extension')->__($ex->getMessage()), '*/case/index/id/'.$this->getRequest()->getParam('case'));
                }
		elseif($this->getRequest()->getParam('container'))
		{
		    $this->error(Mage::helper('iqnomy_extension')->__($ex->getMessage()), '*/*/index/container/'.$this->getRequest()->getParam('container'));
		}
                else
                {
                    $this->error(Mage::helper('iqnomy_extension')->__($ex->getMessage()), "*/*");
                }
            }
        }
        
        $this->init();
    }

    public function deleteAction()
    {
        $content = Mage::getSingleton("iqnomy_extension/liquidcontent");
        $id = $this->getRequest()->getParam('id');
        if(!$id || !$content->exists($id))
        {
            if($this->getRequest()->getParam('case'))
            {
                $this->error(Mage::helper('iqnomy_extension')->__("The liquid content does not exist."), '*/case/index/id/'.$this->getRequest()->getParam('case'));
            }
	    elseif($this->getRequest()->getParam('container'))
	    {
		$this->error(Mage::helper('iqnomy_extension')->__("The liquid content does not exist."), '*/*/index/container/'.$this->getRequest()->getParam('container'));
	    }
            else
            {
                $this->error(Mage::helper('iqnomy_extension')->__("The liquid content does not exist."), "*/*");
            }
            return;
        }
        
        try
        {
            $content->delete($id);
            
            if($this->getRequest()->getParam('case'))
            {
		$case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('case'));
                $this->success(Mage::helper('iqnomy_extension')->__($case->getLiquidContentDeleteMessageSuccess()), '*/case/index/id/'.$this->getRequest()->getParam('case'));
            }
	    elseif($this->getRequest()->getParam('container'))
	    {
		$this->success(Mage::helper('iqnomy_extension')->__("The liquid content has been deleted."), '*/*/index/container/'.$this->getRequest()->getParam('container'));
	    }
            else
            {
                $this->success(Mage::helper('iqnomy_extension')->__("The liquid content has been deleted."), "*/*");
            }
        }
        catch(Exception $ex)
        {
            if($this->getRequest()->getParam('case'))
            {
		$case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('case'));
                $this->error(Mage::helper('iqnomy_extension')->__($case->getLiquidContentDeleteMessageError()), '*/case/index/id/'.$this->getRequest()->getParam('case'));
            }
	    elseif($this->getRequest()->getParam('container'))
	    {
		$this->error(Mage::helper('iqnomy_extension')->__("The liquid content could not be removed."), '*/*/index/container/'.$this->getRequest()->getParam('container'));
	    }
            else
            {
                $this->error(Mage::helper('iqnomy_extension')->__("The liquid content could not be removed."), "*/*");
            }
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