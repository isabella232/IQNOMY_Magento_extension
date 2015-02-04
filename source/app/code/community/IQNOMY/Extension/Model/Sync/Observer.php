<?php

/**
 * IQNOMY Sync Observer
 *
 * @category    IQNOMY
 * @package     IQNOMY_Extension
 * @copyright   Copyright (c) 2013-2015 IQNOMY (http://www.iqnomy.com)
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class IQNOMY_Extension_Model_Sync_Observer
{
    /**
     * Called after Mage_Catalog_Model_Resource_Eav_Attribute model is saved.
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogEntityAttributeSaveAfter($observer)
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        $attribute = $observer->getDataObject();

        try {
            if ($attribute->getEntityType()->getEntityTypeCode() != Mage_Catalog_Model_Product::ENTITY) {
                return;
            }
            if (!$attribute->usesSource()) {
                return;
            }

            /** @var IQNOMY_Extension_Helper_Data $_helper */
            $_helper = Mage::helper('iqnomy_extension');

            // Note: If different IQNOMY accounts can be configured for each storeview, a loop is
            //       required here so attributes for each account are synced.

            if (in_array($attribute->getAttributeCode(), $_helper->getConfiguredProductDimensions())) {
                /** @var IQNOMY_Extension_Model_Webservice $webservice */
                $webservice = $_helper->getWebservice();

                $webservice->updateDimension(
                    $attribute->getAttributeCode(),
                    $attribute->getFrontendLabel(),
                    true,
                    $attribute->getSource()->getAllOptions(false)
                );
            }
        }
        catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * Called after Mage_Catalog_Model_Resource_Eav_Attribute model is deleted.
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogEntityAttributeDeleteAfter($observer)
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        $attribute = $observer->getDataObject();

        // TODO: call webservice delete dimension
    }
}
