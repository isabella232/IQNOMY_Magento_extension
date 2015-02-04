<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Adminhtml_SubscriptionController extends Mage_Adminhtml_Controller_Action
{
    //The menu item that should be marked as active.
    private $menu = "iqnomy/iqnomy_extension4";
    
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