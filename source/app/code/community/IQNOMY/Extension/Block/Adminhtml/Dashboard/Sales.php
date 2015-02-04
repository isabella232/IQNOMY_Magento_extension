<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Block_Adminhtml_Dashboard_Sales extends Mage_Adminhtml_Block_Dashboard_Sales
{
    protected function _construct()
    {
        $this->setModuleName('Mage_Adminhtml');

        return parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->setChild('iqnomy.dashboard',
            $this->getLayout()->createBlock('iqnomy_extension/adminhtml_dashboard_iqnomy')
        );

        return parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        $extraHtml = '';
        try {
            $extraHtml = $this->getChild('iqnomy.dashboard')->toHtml();
        }
        catch (Exception $exception) {
        }

        return $extraHtml . parent::_toHtml();
    }
}
