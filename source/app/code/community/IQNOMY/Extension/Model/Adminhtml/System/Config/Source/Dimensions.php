<?php

/**
 * IQNOMY Source model
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Adminhtml_System_Config_Source_Dimensions
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $_helper = Mage::helper('iqnomy_extension');

        $options = array(
            array(
                'value' => 0,
                'label' => $_helper->__('Automatic')
            ),
            array(
                'value' => 1,
                'label' => $_helper->__('Custom')
            )
        );

        return $options;
    }
}
