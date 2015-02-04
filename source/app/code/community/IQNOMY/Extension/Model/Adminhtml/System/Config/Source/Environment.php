<?php

/**
 * IQNOMY Source model
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Adminhtml_System_Config_Source_Environment
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $_helper = Mage::helper('iqnomy_extension');

        $options = array(
            array(
                'value' => IQNOMY_Extension_Model_Webservice::ENVIRONMENT_LIVE,
                'label' => $_helper->__('Live (production)')
            ),
            array(
                'value' => IQNOMY_Extension_Model_Webservice::ENVIRONMENT_TEST,
                'label' => $_helper->__('Test')
            )
        );

        return $options;
    }
}
