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



namespace Mirasvit\LayeredNavigation\Service;

use Mirasvit\LayeredNavigation\Api\Service\FilterDataServiceInterface;
use Magento\Framework\Search\Adapter\Mysql\Mapper;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;

class FilterDataService implements FilterDataServiceInterface
{
    /**
     * FilterDataService constructor.
     * @param Mapper $mapper
     * @param TemporaryStorageFactory $temporaryStorageFactory
     * @param DataProviderContainer $dataProviderContainer
     * @param Container $aggregationContainer
     * @param ModuleManager $moduleManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Mapper $mapper,
        TemporaryStorageFactory $temporaryStorageFactory,
        DataProviderContainer $dataProviderContainer,
        Container $aggregationContainer,
        ModuleManager $moduleManager,
        ObjectManagerInterface $objectManager
    )
    {
        $this->mapper = $mapper;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->dataProviderContainer = $dataProviderContainer;
        $this->aggregationContainer = $aggregationContainer;
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterBucketData($request, $attributeCode)
    {
        if ($this->moduleManager->isEnabled('Mirasvit_SearchElastic')
            && $this->moduleManager->isEnabled('Mirasvit_Search')
            && $this->objectManager->create('Mirasvit\Search\Model\Config')->getEngine() == 'elastic'
            && $request->getName() != 'catalog_view_container') {
            $responseBucket = $this->getElasticBucket($request, $attributeCode);
        } else {
            $responseBucket = $this->getBucket($request, $attributeCode);
        }

        return $responseBucket;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $attributeCode
     * @return array
     */
    protected function getElasticBucket($request, $attributeCode)
    {
        $query = $this->objectManager->create('Mirasvit\SearchElastic\Adapter\Mapper')->buildQuery($request);
        $this->dataProvider = $this->objectManager->create('Mirasvit\SearchElastic\Adapter\DataProvider');
        $this->dynamicBucket = $this->objectManager->create('Mirasvit\SearchElastic\Adapter\Aggregation\DynamicBucket');
        $this->termBucket = $this->objectManager->create('Mirasvit\SearchElastic\Adapter\Aggregation\TermBucket');
        $this->engine = $this->objectManager->create('Mirasvit\SearchElastic\Model\Engine');
        $client = $this->engine->getClient();
        $response = $client->search($query);

        $bucketAggregation = $request->getAggregation();
        $currentBucket = $this->getCurrentBucket($bucketAggregation, $attributeCode);

        if (!$currentBucket) {
            return [];
        }

        if ($currentBucket->getType() == 'dynamicBucket') {
            $responseBucket = $this->dynamicBucket->build(
                $currentBucket,
                $request->getDimensions(),
                $response,
                $this->dataProvider
            );
        } elseif ($currentBucket->getType() == 'termBucket') {
            $responseBucket = $this->termBucket->build(
                $currentBucket,
                $response
            );
        } else {
            throw new \Exception("Bucket type not implemented.");
        }

        return $responseBucket;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $attributeCode
     * @return array
     */
    protected function getBucket($request, $attributeCode)
    {
        $query = $this->mapper->buildQuery($request);

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table = $temporaryStorage->storeDocumentsFromSelect($query);
        $dataProvider = $this->dataProviderContainer->get($request->getIndex());

        $bucketAggregation = $request->getAggregation();
        $currentBucket = $this->getCurrentBucket($bucketAggregation, $attributeCode);

        if (!$currentBucket) {
            return [];
        }

        $aggregationBuilder = $this->aggregationContainer->get($currentBucket->getType());
        $responseBucket = $aggregationBuilder->build(
            $dataProvider,
            $request->getDimensions(),
            $currentBucket,
            $table
        );

        return $responseBucket;
    }

    /**
     * @param array $bucketAggregation
     * @param string $attributeCode
     * @return \Magento\Framework\Search\Request\Aggregation\TermBucket
     */
    protected function getCurrentBucket($bucketAggregation, $attributeCode)
    {
        $attributeCode = $attributeCode . self::BUCKET;
        $currentBucket = false;
        foreach ($bucketAggregation as $requestBucket) {
            if ($requestBucket->getName() == $attributeCode) {
                $currentBucket = $requestBucket;
                break;
            }
        }

        return $currentBucket;
    }

}