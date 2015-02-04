<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Dashboard_Iqnomy extends Mage_Adminhtml_Block_Widget
{
    /**
     * @var array
     */
    protected $_dashboardUrl = array(
        'live' => '//management.iqnomy.com/myliquidsuite-ws/web/plugin/info.xhtml',
        'test' => '//management.test.iqnomy.com/myliquidsuite-ws/web/plugin/info.xhtml'
    );

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('iqnomy_extension/dashboard.phtml');
        
        $message = Mage::helper("iqnomy_extension")->validateConfig();
        if($message == null)
        {
            $this->setConfigValid(true);
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addError($message);
            $this->setConfigValid(false);
        }
    }

    /**
     * Get the URL to be loaded in the iframe.
     *
     * @return string
     */
    public function getIframeUrl()
    {
        $storeId = $this->getStoreId();

        $environment = Mage::getStoreConfig('iqnomy_extension/account/environment', $storeId);
        $username    = Mage::getStoreConfig('iqnomy_extension/account/username',    $storeId);
        $tenantId    = Mage::getStoreConfig('iqnomy_extension/account/tenant_id',   $storeId);
        $apiKey      = Mage::getStoreConfig('iqnomy_extension/account/api_key',     $storeId);

        if (!array_key_exists($environment, $this->_dashboardUrl)) {
            Mage::throwException('Invalid environment configured.');
        }
        if ($username == '') {
            Mage::throwException('Empty username configured.');
        }
        if ($tenantId == '') {
            Mage::throwException('Empty tenant ID configured.');
        }
        if ($apiKey == '') {
            Mage::throwException('Empty API key configured.');
        }

        return $this->_dashboardUrl[$environment] . '?' . http_build_query(array(
            'key'  => sprintf('%s*%s', $username, $tenantId),
            'user' => $apiKey
        ));
    }

    /**
     * Get selected store id.
     *
     * @return false|int
     */
    public function getStoreId()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        if (empty($storeId)) {
            $storeId = null;
        }

        return $storeId;
    }
}
