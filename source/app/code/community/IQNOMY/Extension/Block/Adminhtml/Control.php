<?php
/**
 * IQNOMY Admin Control
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Control extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * @var array
     */
    protected $_dashboardUrl = array(
        'live' => '//management.iqnomy.com/myliquidsuite-ws/web/plugin/info.xhtml',
        'test' => '//management.test.iqnomy.com/myliquidsuite-ws/web/plugin/info.xhtml'
    );
    
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_headerText = Mage::helper('iqnomy_extension')->__('IQNOMY Extension');
        parent::_construct();
    }
    
    /**
     * Prepare layout
     *
     * @return IQNOMY_Extension_Block_Adminhtml_Control
     */
    protected function _prepareLayout()
    {   	
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
        
        if(!Mage::helper("iqnomy_extension")->bannersEnabled())
        {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('iqnomy_extension')->__("We strongly advice to install the <a href='http://www.magentocommerce.com/magento-connect/ibanners.html' target='_blank'>Fishpig_iBanners</a> module."));
        }
        
        $this->removeButton('add');

        if($this->getConfigValid())
        {
            $this->_addButton('add_new', array(
                'label'   => Mage::helper('iqnomy_extension')->__('Synchronize'),
                'onclick' => "setLocation('{$this->getUrl('*/*/synchronize')}'); varienLoaderHandler.handler.onCreate({options:{loadArea:''}}); this.onclick='';",
            ));
        }

        $this->_addButton('configure', array(
            'label'   => Mage::helper('iqnomy_extension')->__('Configure'),
            'onclick' => "setLocation('{$this->getUrl('adminhtml/system_config/edit', array('section' => 'iqnomy_extension'))}')",
        ));
            
        $this->_addButton('advanced_containers', array(
            'label'     => Mage::helper('iqnomy_extension')->__("Advanced: Containers"),
            'onclick' => "setLocation('{$this->getUrl('*/container/index')}');"
        ));
            
        $this->_addButton('advanced_liquid_contents', array(
            'label'     => Mage::helper('iqnomy_extension')->__("Advanced: Created contents"),
            'onclick' => "setLocation('{$this->getUrl('*/liquidcontent/index')}');"
        ));

        return parent::_prepareLayout();
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
    
    public function getRowUrl($row)
    {
        if($row->getId() == 1 && Mage::helper("iqnomy_extension")->moduleEnabled("IQNOMY_Banner"))
        {
            return $this->getUrl('iqnomy_banner/adminhtml_banner/index');
        }
        return $this->getUrl('*/case/index', array('id' => $row->getId()));
    }
    
    public function getEnableDisableUrlContainer($row)
    {
	$helper = Mage::helper("iqnomy_extension");
	$enable = !$row->getIsEnabled();
        if($enable === true)
        {
            return "<a href='".$this->getUrl('*/container/enable', array('id' => $row->getId(), 'enable' => "true"))."'>".$helper->__("Enable")."</a>";
        }
        else
        {
            return "<a href='".$this->getUrl('*/container/enable', array('id' => $row->getId(), 'enable' => "false"))."'>".$helper->__("Disable")."</a>";
        }
    }
    
    public function getEnableDisableUrl($row)
    {
        $helper = Mage::helper("iqnomy_extension");
        $enable = $row->getEnableDisable();
        if($enable === null)
        {
            return "";
        }
        elseif($enable === true)
        {
            return "<a href='".$this->getUrl('*/case/enable', array('id' => $row->getId(), 'enable' => "true"))."'>".$helper->__("Enable")."</a>";
        }
        else
        {
            return "<a href='".$this->getUrl('*/case/enable', array('id' => $row->getId(), 'enable' => "false"))."'>".$helper->__("Disable")."</a>";
        }
    }
    
    public function getCollection()
    {
        return Mage::getModel("iqnomy_extension/case")->getCollection();
    }
    
    private $otherContainers = null;
    
    public function getOtherContainers()
    {
        if($this->otherContainers == null)
        {
            $containers = array();
            foreach(Mage::getModel("iqnomy_extension/container")->getCollection() as $container)
            {
                $containers[$container->getId()] = $container;
            }

            foreach($this->getCollection() as $case)
            {
                if($case->getContainer() != null && array_key_exists($case->getContainer()->getId(), $containers))
                {
                    unset($containers[$case->getContainer()->getId()]);
                }
            }
            
            $this->otherContainers = new Varien_Data_Collection();
            foreach($containers as $container)
            {
                $this->otherContainers->addItem($container);
            }
        }
        return $this->otherContainers;
    }
    
    public function getContainerEditUrl($container)
    {
        return $this->getUrl('*/container/edit', array('id' => $container->getId()));
    }
    
    public function getContainerLiquidContentOverviewUrl($container)
    {
	return $this->getUrl('*/liquidcontent/index', array('container' => $container->getId()));
    }
}
