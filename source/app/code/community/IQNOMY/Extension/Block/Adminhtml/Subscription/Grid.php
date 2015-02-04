<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Subscription_Grid extends Mage_Adminhtml_Block_Widget
{
    private $_collection;
    
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate("iqnomy_extension/subscription/grid.phtml");
                
        $this->_collection = new Varien_Data_Collection();
        $collection = Mage::getSingleton("iqnomy_extension/subscription")->getCollection();

        foreach($collection as $subscriber)
        {
            $realSubscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($subscriber->getEmail());
            if($realSubscriber->getEmail() == $subscriber->getEmail())
            {
                if($realSubscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
                {
                    $subscriber->setStatus("Subscribed");
                }
                else
                {
                    $subscriber->setStatus("Not subscribed");
                }
                $this->_collection->addItem($subscriber);
            }
        }
    }
    
    public function _prepareLayout()
    {
        parent::_prepareLayout();
    }
    
    public function getCollection()
    {
        return $this->_collection;
    }
}