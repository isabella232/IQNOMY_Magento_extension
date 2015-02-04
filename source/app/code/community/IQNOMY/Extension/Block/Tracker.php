<?php

/**
 * IQNOMY Tracker Script Block
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Tracker extends Mage_Core_Block_Abstract
{
    /**
     * @var array
     */
    protected $_hostAndPort = array(
        'live' => 'liquifier.iqnomy.com',
        'test' => 'liquifier.test.iqnomy.com'
    );

    /**
     * @var array
     */
    protected $_mediaHostAndPort = array(
        'live' => 'static.iqnomy.com',
        'test' => 'static.test.iqnomy.com'
    );

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Head $headBlock */
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->addJs('iqnomy/iqnomy.js');
        }
        return $this;
    }

    /**
     * Return the IQImpressor script HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $environment = Mage::getStoreConfig('iqnomy_extension/account/environment');
        $tenantId    = Mage::getStoreConfig('iqnomy_extension/account/tenant_id');
        $enableTenantScript = Mage::getStoreConfig('iqnomy_extension/account/tenant_script') ? 'true' : 'false';
        if (!array_key_exists($environment, $this->_hostAndPort) || empty($tenantId)) {
            return '';
        }

        /** @var array $eventData */
        $eventData = Mage::getSingleton('iqnomy_extension/tracker')->collectEventData($this->getAction());

        $html = "<script type=\"text/javascript\">\n"
              . "//<![CDATA[\n"
              . "if (_iqsHelper.init(". Zend_Json::encode($this->getTrackerConfig()) . ")) {\n"
	      . "\tvar IQUnixTime = ".time().";\n"
              . "\tvar _iqsTenant = {$tenantId};\n"
              . "\tvar _iqsImpress = { hostAndPort: '{$this->_hostAndPort[$environment]}', timeout: 5000, impressCallback: iqImpressCallback, backupCallback: iqImpressBackupCallback,extra:{$enableTenantScript}};\n"
              . "\tvar _iqsEventData = _iqsHelper.getEventData(". Zend_Json::encode(Mage::helper('iqnomy_extension')->encode($eventData)) . ");\n"
              . "\t(function() {\n"
              . "\t\tvar _iqs = document.createElement('script'); _iqs.type = 'text/javascript'; _iqs.async = true;\n"
              . "\t\t_iqs.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + "
              . "'{$this->_mediaHostAndPort[$environment]}/myliquidsuite/js/IQImpressor.js';\n"
              . "\t\tvar s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(_iqs, s);\n"
              . "\t})();\n"
              . "}\n"
	      . "//]]>\n"
              . "</script>\n";
        return $html;
    }

    /**
     * Return the configuration parameters needed for the tracker.
     *
     * @param mixed $store
     * @return array
     */
    public function getTrackerConfig($store = null)
    {
        $forceLog = Mage::getStoreConfigFlag('iqnomy_extension/account/enable_logging');
        return array(
            'logging'   => Mage::getIsDeveloperMode() || Mage::getStoreConfig('dev/log/active', $store) || $forceLog,
            'ignoreDnt' => Mage::getStoreConfigFlag('iqnomy_extension/tracking/ignore_dnt', $store)
        );
    }
}
