<?php

/**
 * IQNOMY Source model
 *
 * Returns all available (visible) product attributes.
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Adminhtml_System_Config_Source_Dimensions_Product
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $attributeCollection */
        $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFilter('is_visible', 1);

        $attributes = $attributeCollection->getColumnValues('attribute_code');

        // add non-EAV attributes
        $attributes = array_merge($attributes, array(
            'id',
            'type_id',
            'attribute_set_id',
        ));

        natsort($attributes);

        $options = array();
        foreach ($attributes as $_attributeCode) {
            $options[] = array(
                'value' => $_attributeCode,
                'label' => $_attributeCode
            );
        }

        return $options;
    }
}
