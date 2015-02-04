<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Banner extends Mage_Core_Model_Abstract
{    
    const animationTypeSlide = 1;
    const animationTypeFade = 2;
    const animationTypeNone = 3;
    
    protected function _construct()
    {
        $this->_init("iqnomy_extension/banner");
    }
    
    /**
     * 
     * @return IQNOMY_Extension_Model_Container|null
     */
    public function getContainer()
    {
        $collection = Mage::getSingleton("iqnomy_extension/container")->getCollection();
        if($this->getContainerId() != null && $collection != null)
        {
            foreach($collection as $container)
            {
                if($container->getId() == $this->getContainerId())
                {
                    return $container;
                }
            }
        }
        return null;
    }
    
    public function findByNameAndContainer($name, $container)
    {
        $collection = $this->getCollection(false);
        if($collection != null)
        {
            foreach($collection as $banner)
            {
                if($banner->getName() == $name && $banner->getContainerId() == $container->getId())
                {
                    return $banner;
                }
            }
        }
        return null;
    }
    
    public function validate()
    {
        return null;
    }
    
    public function getCollection($filterIqnomyBanners = true)
    {
        $collection = parent::getCollection();
        if($filterIqnomyBanners)
        {
            foreach($collection as $key => $banner)
            {
                if($banner->getIsIqnomyBanner())
                {
                    $collection->removeItemByKey($key);
                }
            }
        }
        return $collection;
    }
    
    public function controllerActionLayoutRenderBefore($observer)
    {
        /** @var Mage_Page_Block_Html_Head $headBlock */
        $layout = Mage::app()->getLayout();
        if ($beforeBodyEndBlock = $layout->getBlock('before_body_end')) {
            $bannerBlock = $layout->createBlock('iqnomy_extension/banner');
            $beforeBodyEndBlock->append($bannerBlock);
        }
    }
    
    public function newsletterSubscriberSaveAfter($observer)
    {
        try
        {
            if(Mage::app()->getRequest()->getParam('by') == "iqnomy")
            {
                //checking if allready subscribed.
                $subscription = $observer->getEvent()->getSubscriber();
                if ($subscription != null && $subscription->subscriber_email)
                {
                    $model = Mage::getModel("iqnomy_extension/subscription")->getCollection()->addFilter("email", $subscription->subscriber_email)->getFirstItem();
                    if($model == null || $subscription->subscriber_email != $model->getEmail())
                    {
                        //New subscriber that should be saved.
                        $model = Mage::getModel("iqnomy_extension/subscription");
                        $model->setEmail($subscription->subscriber_email);
                        $model->setHash(sha1($model->getEmail()));
                        $model->save();
                        
                        //Setting a flag that we need to track it.
                        Mage::getSingleton('customer/session')->setSubscriptionHash($model->getHash());
                    }
                }
            }
        }
        catch(Exception $ex)
        {
            //Woops an error, well... we can't do anything about it from here.
        }
    }
}