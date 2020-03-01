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

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Dynamic\Algorithm\Repository;
use Magento\Framework\Search\Dynamic\EntityStorageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Eav\Model\Config as EavConfig;
use Mirasvit\LayeredNavigation\Api\Service\SliderServiceInterface;
use Mirasvit\SearchElastic\Adapter\Aggregation\DynamicBucket;

class SliderSetDataElasticPlugin
{
    public function __construct(
        EavConfig $eavConfig,
        Repository $algorithmRepository,
        EntityStorageFactory $entityStorageFactory
    ) {
        $this->eavConfig = $eavConfig;
        $this->algorithmRepository = $algorithmRepository;
        $this->entityStorageFactory = $entityStorageFactory;
    }

    /**
     * @param DynamicBucket $subject
     * @param \Closure $proceed
     * @param RequestBucketInterface $bucket
     * @param array $dimensions
     * @param array $queryResult
     * @param DataProviderInterface $dataProvider
     * @return array
     */
    public function aroundBuild(
        $subject,
        \Closure $proceed,
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $bucket->getField());
        $backendType = $attribute->getBackendType();

        if ($backendType != FilterList::DECIMAL_FILTER) {
            return $proceed($bucket, $dimensions, $bucket, $dataProvider);
        }

        $attributeCode = $attribute->getAttributeCode();

        $minMaxSliderData = false;

        if ($attributeCode == 'price') {
            $minMaxSliderData[SliderServiceInterface::SLIDER_DATA . $attributeCode]
                = $dataProvider->getAggregations($this->getEntityStorage($queryResult));
            $minMaxSliderData[SliderServiceInterface::SLIDER_DATA . $attributeCode]['value']
                = SliderServiceInterface::SLIDER_DATA . $attributeCode;
        }

        if ($minMaxSliderData && is_array($minMaxSliderData)) {
            $data = $proceed($bucket, $dimensions, $queryResult, $dataProvider);

            return array_merge($minMaxSliderData, $data);
        }

        return $proceed($bucket, $dimensions, $queryResult, $dataProvider);
    }

    /**
     * @param array $queryResult
     * @return EntityStorage
     */
    private function getEntityStorage(array $queryResult)
    {
        $ids = [];
        foreach ($queryResult['hits']['hits'] as $document) {
            $ids[] = $document['_id'];
        }

        return $this->entityStorageFactory->create($ids);
    }
}
