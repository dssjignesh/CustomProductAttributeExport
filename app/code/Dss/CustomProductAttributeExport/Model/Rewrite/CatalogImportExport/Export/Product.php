<?php

declare(strict_types=1);

namespace Dss\CustomProductAttributeExport\Model\Rewrite\CatalogImportExport\Export;

use Magento\CatalogImportExport\Model\Export\Product as BaseProduct;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;

class Product extends BaseProduct
{
    public const XML_PATH_ENABLE = 'dssexport/configuration/enable';

    public const XML_PATH_ATTRIBUTE = 'dssexport/configuration/allowedattribute';

    /**
     * Set header columns
     *
     * @param array $customOptionsData
     * @param array $stockItemRows
     */
    protected function setHeaderColumns($customOptionsData, $stockItemRows)
    {
        $config = ObjectManager::getInstance()->get(ScopeConfigInterface::class);
        $status = $config->getValue(
            self::XML_PATH_ENABLE,
            ScopeInterface::SCOPE_STORE
        );

        $moduleEnabled = (bool)$status;
        $merge = [];
        if ($moduleEnabled) {
            $attr = $config->getValue(
                self::XML_PATH_ATTRIBUTE,
                ScopeInterface::SCOPE_STORE
            );
            $merge = explode(',', $attr);
        }

        if (!$this->_headerColumns) {
            $customOptCols = [
                'custom_options',
            ];
            $this->_headerColumns = array_merge(
                [
                    self::COL_SKU,
                    self::COL_STORE,
                    self::COL_ATTR_SET,
                    self::COL_TYPE,
                    self::COL_CATEGORY,
                    self::COL_PRODUCT_WEBSITES,
                ],
                $this->_getExportMainAttrCodes(),
                $merge,
                [self::COL_ADDITIONAL_ATTRIBUTES],
                reset($stockItemRows) ? array_keys(end($stockItemRows)) : [],
                [],
                [
                    'related_skus',
                    'related_position',
                    'crosssell_skus',
                    'crosssell_position',
                    'upsell_skus',
                    'upsell_position'
                ],
                ['additional_images', 'additional_image_labels', 'hide_from_product_page']
            );
            // have we merge custom options columns
            if ($customOptionsData) {
                $this->_headerColumns = array_merge($this->_headerColumns, $customOptCols);
            }
        }
    }
}
