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
 * @package   mirasvit/module-navigation
 * @version   1.0.59
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Plugin;

use \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Dynamic;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Framework\Search\Dynamic\EntityStorageFactory;
use Magento\Framework\Search\Dynamic\DataProviderInterface as DynamicDataProvider;
use Mirasvit\LayeredNavigation\Api\Service\SliderServiceInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;

class SliderSetDataPlugin
{
    public function __construct(
        EavConfig $eavConfig,
        EntityStorageFactory $entityStorageFactory,
        DynamicDataProvider $dynamicDataProvider,
        ScopeResolverInterface $scopeResolver,
        ResourceConnection $resource,
        Registry $registry,
        SliderServiceInterface $sliderService
    ) {
        $this->eavConfig = $eavConfig;
        $this->entityStorageFactory = $entityStorageFactory;
        $this->dynamicDataProvider = $dynamicDataProvider;
        $this->scopeResolver = $scopeResolver;
        $this->resource = $resource;
        $this->registry = $registry;
        $this->sliderService = $sliderService;
    }

    /**
     * @param Dynamic $subject
     * @param \Closure $proceed
     * @param DataProviderInterface $dataProvider
     * @param array $dimensions
     * @param RequestBucketInterface $bucket
     * @param Table $entityIdsTable
     * @return array
     */
    public function aroundBuild(
        Dynamic $subject,
        \Closure $proceed,
        DataProviderInterface $dataProvider,
        array $dimensions,
        RequestBucketInterface $bucket,
        Table $entityIdsTable
    ) {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $bucket->getField());
        $backendType = $attribute->getBackendType();

        if ($backendType != FilterList::DECIMAL_FILTER) {
            return $proceed($dataProvider, $dimensions, $bucket, $entityIdsTable);
        }

        $attributeCode = $attribute->getAttributeCode();

        if ($attributeCode == 'price') {
            $minMaxSliderData = $this->getPriceSliderData($attributeCode, $entityIdsTable);
        } else {
            $minMaxSliderData = $this->getDecimalSliderData($dimensions, $entityIdsTable, $attribute, $dataProvider);
        }

        if ($minMaxSliderData && is_array($minMaxSliderData)) {
            $data = $proceed($dataProvider, $dimensions, $bucket, $entityIdsTable);

            return array_merge($minMaxSliderData, $data);
        }

        return $proceed($dataProvider, $dimensions, $bucket, $entityIdsTable);
    }

    /**
     * @param string $attributeCode
     * @param Table $entityIdsTable
     * @return array
     */
    private function getPriceSliderData($attributeCode, $entityIdsTable)
    {
        $minMaxSliderData[SliderServiceInterface::SLIDER_DATA . $attributeCode]
            = $this->dynamicDataProvider->getAggregations(
            $this->entityStorageFactory->create($entityIdsTable)
        );
        $minMaxSliderData[SliderServiceInterface::SLIDER_DATA . $attributeCode]['value']
            = SliderServiceInterface::SLIDER_DATA . $attributeCode;

        return $minMaxSliderData;
    }

    /**
     * @param array $dimensions
     * @param Table $entityIdsTable
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @param DataProviderInterface $dataProvider
     * @return mixed
     */
    private function getDecimalSliderData($dimensions, $entityIdsTable, $attribute, $dataProvider)
    {
        $currentScope = $this->getCurrentScope($dimensions);
        $select = $this->resource->getConnection()->select();
        $table = $this->resource->getTableName(
            'catalog_product_index_eav_decimal'
        );
        $select->from(['main_table' => $table], ['value'])
            ->where('main_table.attribute_id = ?', $attribute->getAttributeId())
            ->where('main_table.store_id = ? ', $currentScope)
            ->joinInner(
                ['entities' => $entityIdsTable->getName()],
                'main_table.entity_id  = entities.entity_id',
                []
            );

        $query = $this->resource->getConnection()->select();
        $query->from(['main_table' => $select],
            [
                'value' => new \Zend_Db_Expr("'" . SliderServiceInterface::SLIDER_DATA
                    . str_replace('_', '', $attribute->getAttributeCode()) . "'")
            ]
        )->columns(
            [
                'min' => 'min(main_table.value)',
                'max' => 'max(main_table.value)',
                'count' => 'count(*)'
            ]
        );

        $minMaxSliderData = $dataProvider->execute($query);

        return $minMaxSliderData;
    }

    /**
     * @param $dimensions
     * @return int
     */
    private function getCurrentScope($dimensions)
    {
        return $this->scopeResolver->getScope($dimensions['scope']->getValue())->getId();

    }
}