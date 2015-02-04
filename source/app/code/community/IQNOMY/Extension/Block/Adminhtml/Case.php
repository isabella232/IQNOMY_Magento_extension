<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Case extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_mode = 'edit';
        
        $this->_controller = 'adminhtml_case';
        $this->_blockGroup = 'iqnomy_extension';
        
        $this->_headerText = Mage::helper('iqnomy_extension')->__('Case: ' . $this->getCase()->getName());
        parent::_construct();
        $this->setTemplate("iqnomy_extension/case/overview.phtml");
    }

    /**
     * Prepare layout
     *
     * @return IQNOMY_Extension_Block_Adminhtml_Case
     */
    protected function _prepareLayout()
    {
        $this->_addButton('back_button', array(
            'label'   => Mage::helper('iqnomy_extension')->__('Back'),
            'onclick' => "setLocation('{$this->getUrl('*/iqnomy/index')}')",
            'class' => 'back'
        ));
            
        if($this->getCase()->getIsConfigurable())
        {
            $addLiquidContentTitle = "Add liquid content";
            if($this->getCase()->getLiquidContentAddTitle() != null)
            {
                $addLiquidContentTitle = $this->getCase()->getLiquidContentAddTitle();
            }
            
	    if($this->getCase()->getContainer() != null && $this->getCase()->getAdvancedEnabled())
	    {
		$this->_addButton('advanced_case', array(
		    'label'   => Mage::helper('iqnomy_extension')->__("Advanced"),
		    'onclick' => "setLocation('{$this->getUrl('*/container/edit', array('id' => $this->getCase()->getContainer()->getId()))}')",
		));
	    }
	    
	    if($this->getCase()->getContainer() != null && $this->getCase()->getLiquidContentAddEnabled())
	    {
		$this->_addButton('add_liquid_content', array(
		    'label'   => Mage::helper('iqnomy_extension')->__($addLiquidContentTitle),
		    'onclick' => "setLocation('{$this->getUrl('*/liquidcontent/new', array('case' => $this->getRequest()->getParam('id')))}')",
		    'class' => 'add'
                ));
	    }
	    
	    if($this->getCase()->getIsNewsletterCase() && $this->getCase()->getBanner() != null)
	    {
		$url = Mage::helper('iqnomy_extension')->getBaseUrl();
		if(strpos($url, "?") !== false)
		{
		    $url = $url."&preview=true&placement_id=".$this->getCase()->getBanner()->getId();
		}
		else
		{
		    $url = $url."?preview=true&placement_id=".$this->getCase()->getBanner()->getId();
		}
		
		$this->_addButton('preview_case', array(
		    'label'   => 'Preview',
		    'onclick' => "window.open('".$url."')",
                ));
	    }
	    
            $this->_addButton('save_placement', array(
                'label'   => Mage::helper('iqnomy_extension')->__('Save'),
                'onclick' => "document.getElementById('edit_form').submit();",
                'class' => 'save'
            ));
        }

        return parent::_prepareLayout();
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id')));
    }
    
    private $case = null;
    public function getCase()
    {
	if($this->case == null)
	{
	    $this->case = Mage::getModel("iqnomy_extension/case")->load($this->getRequest()->getParam('id'));
	}
        return $this->case;
    }
    
    public function getRowUrl($liquidContent)
    {
        return $this->getUrl('*/liquidcontent/edit', array('id' => $liquidContent->getId(), 'case' => $this->getRequest()->getParam('id')));
    }
    
    public function getResetHtmlUrl()
    {
	return $this->getUrl('*/*/resethtml', array('id' => $this->getRequest()->getParam('id')));
    }
    
    public function getLiquidContents()
    {
	if($this->getCase()->getContainer() == null)
	{
	    return new Varien_Data_Collection();
	}
	
        return $this->getCase()->getContainer()->getLiquidContents();
    }
    
    public function getContainerPlacements()
    {
        $case = $this->getCase();
        if($case != null)
        {
            $container = $case->getContainer();
            if($container != null)
            {
                return $container->getContainerplacements();
            }
        }
        return array();
    }
    
    public function getPlacementId()
    {
        $case = $this->getCase();
        if($case != null)
        {
            $banner = $case->getBanner();
            if($banner != null)
            {
                return $banner->getId();
            }
        }
        return null;
    }
    
    public function getIbannerGroups()
    {
        if($this->ibannersEnabled())
        {
            return Mage::getModel("ibanners/group")->getCollection();
        }
        return array();
    }
    
    public function ibannersEnabled()
    {
        return Mage::helper("iqnomy_extension")->bannersEnabled();
    }
    
    public function getHeight()
    {
        $banner = $this->getCase()->getBanner();
        if($banner == null)
        {
            return $this->getDefaultHeight();
        }
        return $banner->getHeight();
    }
    
    public function getHtmlId()
    {
        $banner = $this->getCase()->getBanner();
        if($banner == null || $banner->getHtmlId() == null)
        {
            return '';
        }
        return $banner->getHtmlId();
    }
    
    public function getIbannerGroupCode()
    {
        return str_replace("ibanners-", "", $this->getHtmlId());
    }
    
    public function getLiquidContentPreviewUrl($url)
    {
	if(strpos($url, "?") !== false)
        {
            return $url."&preview=true&placement_id=".$this->getPlacementId()."&liquid_content_id=";
        }
        else
        {
            return $url."?preview=true&placement_id=".$this->getPlacementId()."&liquid_content_id=";
        }
    }
    
    public function trans($requested)
    {
	return Mage::helper('iqnomy_extension')->__($requested);
    }
    
    public function getMaxResults()
    {
	$placements = $this->getContainerPlacements();
	if(count($placements) > 0)
	{
	    foreach($placements as $placement)
	    {
		return $placement["maxResults"];
	    }
	}
	return $this->getCase()->getMaxResults();
    }
}