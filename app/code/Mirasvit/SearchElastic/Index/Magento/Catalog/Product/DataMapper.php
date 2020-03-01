<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-elastic
 * @version   1.2.45
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchElastic\Index\Magento\Catalog\Product;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\Search\Api\Data\Index\DataMapperInterface;
use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;

class DataMapper implements DataMapperInterface
{
    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StockStatusHelper
     */
    private $stockStatusHelper;

    /**
     * @var array
     */
    private $childrenData = [];

    /**
     * @var array
     */
    private $relationsData = [];

    static  $attributeCache = [];

    public function __construct(
        IndexRepositoryInterface $indexRepository,
        EavConfig $eavConfig,
        ResourceConnection $resource,
        ProductMetadataInterface $productMetadata,
        ScopeConfigInterface $scopeConfig,
        StockStatusHelper $stockStatusHelper
    ) {
        $this->indexRepository = $indexRepository;
        $this->eavConfig       = $eavConfig;
        $this->resource        = $resource;
        $this->productMetadata = $productMetadata;
        $this->scopeConfig     = $scopeConfig;
        $this->stockStatusHelper = $stockStatusHelper;
    }

    /**
     * @param array                                         $documents
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @param string                                        $indexIdentifier
     *
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function map(array $documents, $dimensions, $indexIdentifier)
    {
        $dimension = current($dimensions);
        $this->getRelationsData(array_keys($documents));
        $this->stockStatusHelper->init($dimension->getValue());

        $rawDocs = [];
        foreach (['catalog_product_entity_varchar', 'catalog_product_entity_text', 'catalog_product_entity_decimal', 'catalog_product_index_eav'] as $table) {
            $dt = $this->eavMap($table, array_keys($documents) + $this->childrenData, $dimension->getValue());

            foreach ($dt as $row) {
                $entityId    = isset($row['row_id']) ? $row['row_id'] : $row['entity_id'];
                $entityId    = isset($row['parent_id']) ? $row['parent_id'] : $entityId;
                $attributeId = $row['attribute_id'];

                if (!isset(self::$attributeCache[$attributeId])) {
                    self::$attributeCache[$attributeId] = $this->eavConfig->getAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeId);
                }

                $attribute = self::$attributeCache[$attributeId];

                $rawDocs[$entityId][$attribute->getAttributeCode()][] = $row['value'];
            }
        }

        foreach ($documents as $id => $doc) {
            $rawData = @$rawDocs[$id];
            $documents[$id] = $this->prepareRawData($rawData, $id, $doc);
            unset($rawDocs[$id]);
        }

        $parentIds = [];
        foreach ($rawDocs as $id => $doc) {
            if(isset($this->relationsData[$id])){
                $parentIds[] = $this->relationsData[$id]['parent_id'];
                $documents[$this->relationsData[$id]['parent_id']]['children'][] = $this->prepareRawData($rawData, $id, $doc);
            }
        }

        foreach (array_diff(array_keys($documents), $parentIds) as $diffKey) {
            if (isset($documents[$diffKey])) {
                $documents[$diffKey]['children'][] = $documents[$diffKey];
            }
        }

        $productIds = array_keys($documents);

        $categoryIds = $this->getCategoryProductIndexData($productIds, $dimension->getValue());

        foreach ($documents as $id => $doc) {
            $doc['category_ids_raw'] = isset($categoryIds[$id]) ? $categoryIds[$id] : [];
            $documents[$id]          = $doc;
        }

        return $documents;
    }

    private function prepareRawData($rawData, $id, $doc)
    {
        $rawData['is_in_stock'] = $this->stockStatusHelper->getProductStockStatus($id);

        foreach ($doc as $key => $value) {
            if (is_array($value) && !in_array($key, ['autocomplete_raw', 'autocomplete'])) {
                $doc[$key] = implode(' ', $value);

                foreach ($value as $v) {
                    $doc[$key . '_raw'][] = intval($v);
                }
            }
        }

        foreach ($rawData as $attribute => $value) {
            if (is_scalar($value) || is_array($value)) {
                if ($attribute != 'media_gallery'
                    && $attribute != 'options_container'
                    && $attribute != 'quantity_and_stock_status'
                    && $attribute != 'country_of_manufacture'
                    && $attribute != 'tier_price'
                ) {
                    $doc[$attribute . '_raw'] = $value;
                }
            }
        }

        return $doc;
    }

    private function getRelationsData($parentIds)
    {
        if (empty($parentIds)) {
            return false;
        }

        $select = $this->resource->getConnection()->select();

        if ($this->productMetadata->getEdition() == 'Enterprise' || $this->productMetadata->getEdition() == 'B2B') {
            $select->from(
                ['relation' => $this->resource->getTableName('catalog_product_relation')],
                ['child_id']
            );

            $select->join(['product_entity' => $this->resource->getTableName('catalog_product_entity')],
                'product_entity.row_id = relation.parent_id',
                ['parent_id' => 'product_entity.row_id']
            );

            $select->join(['child_product_entity' => $this->resource->getTableName('catalog_product_entity')],
                'child_product_entity.entity_id = relation.child_id',
                []
            );

            $select->group('product_entity.row_id');
        } else {
            $select->from(
                ['relation' => $this->resource->getTableName('catalog_product_relation')],
                ['child_id', 'parent_id']
            );

            $select->join(['product_entity' => $this->resource->getTableName('catalog_product_entity')],
                'product_entity.entity_id = relation.parent_id',
                []
            );
        }

        $select->where('product_entity.entity_id IN (' . implode(',', $parentIds) . ')');

        $children = $this->resource->getConnection()->fetchAssoc($select);

        $this->childrenData = array_keys($children);
        $this->relationsData = $children;
    }

    /**
     * @param array $productIds
     * @param array $storeId
     *
     * @return array
     */
    private function getCategoryProductIndexData($productIds, $storeId)
    {
        $productIds[] = 0;

        $connection = $this->resource->getConnection();

        $tableName = $this->resource->getTableName('catalog_category_product_index') . '_store' . $storeId;
        if (!$this->resource->getConnection()->isTableExists($tableName)) {
            $tableName = $this->resource->getTableName('catalog_category_product_index');
        }

        $select = $connection->select()->from(
            [$tableName],
            ['category_id', 'product_id']
        );

        $select->where('product_id IN (?)', $productIds);

        $result = [];
        foreach ($connection->fetchAll($select) as $row) {
            $result[$row['product_id']][] = $row['category_id'];
        }

        $select = $connection->select()->from(
            [$this->resource->getTableName('catalog_category_product')],
            ['category_id', 'product_id']
        );

        $select->where('product_id IN (?)', $productIds);

        foreach ($connection->fetchAll($select) as $row) {
            $result[$row['product_id']][] = $row['category_id'];
            $result[$row['product_id']]   = array_values(array_unique($result[$row['product_id']]));
        }

        return $result;
    }

    private function eavMap($table, $ids, $storeId)
    {
        $select = $this->resource->getConnection()->select();

        $select->from(
            ['eav' => $this->resource->getTableName($table)],
            ['*']
        )->where('eav.store_id in (0, ?)', $storeId);

        if (($this->productMetadata->getEdition() == 'Enterprise'
            || $this->productMetadata->getEdition() == 'B2B') && $table != 'catalog_product_index_eav') {
            $select->join(['product_entity' => $this->resource->getTableName('catalog_product_entity')],
                'product_entity.row_id = eav.row_id', ['parent_id' => 'product_entity.entity_id']);
            $select->where('product_entity.entity_id in (?)', $ids);
        } else {
            $select->where('eav.entity_id in (?)', $ids);
        }

        return $this->resource->getConnection()->fetchAll($select);
    }

}
