<?php
/**
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Level
 */
class IQNOMY_Extension_Model_Adminhtml_System_Config_Source_Category_Level
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array('category_level' => array('value' => '1', 'label' => 'test'));
    }
}