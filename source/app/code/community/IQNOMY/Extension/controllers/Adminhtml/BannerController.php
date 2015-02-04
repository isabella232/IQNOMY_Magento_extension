<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
    //The menu item that should be marked as active.
    private $menu = "system";
    
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
        //Initialize default: grid.
        $this->init();
    }

    public function gridAction()
    {
        //Initialize default: grid.
        $this->init();
    }

    public function newAction()
    {
        //Forward to the editAction without any data.
        $this->_forward('edit');
    }

    public function saveAction()
    {
        //Save a new banner or update a existing banner.
        $data = null;
        if ($data = $this->getRequest()->getPost())
        {
            if(!array_key_exists("is_enabled", $data))
                $data["is_enabled"] = false;
        }
        
        if ($data != null || $data = $this->getRequest()->getPost())
        {       
            //Load the model.
            $model = Mage::getModel("iqnomy_extension/banner");
            $id = $this->getRequest()->getParam('id');
            if ($id)
            {
                //It's a existing banner. Load the data of the banner.
                $model = $model->load($id);
                $model->setData("id", $id);
                $data["id"] = $id;
            }
            $model->setData($data);

            //Setting the data into the session in case an error occurred.
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            try
            {
                //Advanced validation.
                $validation = $model->validate();
                //The validation failed, the message contains the error.
                if($validation != null)
                    Mage::throwException($validation);
                
                //Save to database.
                $model->save();

                //Check if saving was successfull. When the save fails, usally an exception is thrown, just to be sure.
                if (!$model->getId())
                {
                    Mage::throwException(Mage::helper('iqnomy_extension')->__("The liquid internet case could not be saved."));
                }

                //Adding success message and clearing the session.
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('iqnomy_extension')->__("The liquid internet case has been saved."));
                Mage::getSingleton('adminhtml/session')->setFormData(null);

                //Redirection to indexAction
                $this->_redirect('*/*/');
            }
            catch (Exception $e)
            {
                //An exception was thrown. Setting the error and reloading the form.
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iqnomy_extension')->__($e->getMessage()));
                $this->_redirect('*/*/edit', array('id' => $id));
            }
            return;
        }
        
        //No data was found, returning to the indexAction
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iqnomy_extension')->__("No data was found to save."));
        $this->_redirect('*/*/');
    }

    public function editAction()
    {
        $containerCount = Mage::getModel("iqnomy_extension/container")->getCollection()->count();
        if($containerCount > 0)
        {
            $id = $this->getRequest()->getParam('id', null);
            $model = Mage::getModel("iqnomy_extension/banner");
            if ($id)
            {
                //Loading the data.
                $model->load((int) $id);
                if ($model->getId() != $id)
                {
                    //Banner was not found, returning to the indexAction.
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iqnomy_extension')->__("The liquid internet case does not exist."));
                    $this->_redirect('*/*/');
                    return;
                }

                //Checking if this is a re-post.
                $sessionData = Mage::getSingleton('adminhtml/session')->getFormData();
                if($sessionData == null)
                {
                    //This is not a re-post, setting the data from the model in the session.
                    Mage::getSingleton('adminhtml/session')->setFormData($model->getData());
                }
            }
            //Initialize the form.
            $this->init('iqnomy_extension/adminhtml_banner_edit');
        }
        else
        {
            //No containers were found, a banner requires as container. Redirecting to indexAction.
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iqnomy_extension')->__("Liquid internet cases cannot be added while there are no containers."));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0)
        {
            try
            {
                $model = Mage::getModel("iqnomy_extension/banner");
                //Deleting the banner.
                $model->setId($this->getRequest()->getParam('id'))->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('iqnomy_extension')->__("The liquid internet case has been removed."));
                $this->_redirect('*/*/');
            }
            catch (Exception $e)
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
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
