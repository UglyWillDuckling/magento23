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



namespace Mirasvit\SearchElastic\Adapter\Aggregation;

use Magento\Framework\Search\Dynamic\Algorithm\Repository;
use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Dynamic\EntityStorage;
use Magento\Framework\Search\Dynamic\EntityStorageFactory;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;

class DynamicBucket
{
    /**
     * @var Repository
     */
    private $algorithmRepository;

    /**
     * @var EntityStorageFactory
     */
    private $entityStorageFactory;

    public function __construct(Repository $algorithmRepository, EntityStorageFactory $entityStorageFactory)
    {
        $this->algorithmRepository  = $algorithmRepository;
        $this->entityStorageFactory = $entityStorageFactory;
    }

    /**
     * @param RequestBucketInterface $bucket
     * @param array                  $dimensions
     * @param array                  $queryResult
     * @param DataProviderInterface  $dataProvider
     *
     * @return array
     */
    public function build(
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        /** @var DynamicBucket $bucket */
        $method = $bucket->getName() == 'price_bucket' ? $bucket->getMethod() : 'auto';
        $algorithm = $this->algorithmRepository->get($method, ['dataProvider' => $dataProvider]);

        $data       = $algorithm->getItems($bucket, $dimensions, $this->getEntityStorage($queryResult));
        $resultData = $this->prepareData($data);

        return $resultData;
    }

    /**
     * @param array $queryResult
     *
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

    /**
     * @param array $data
     *
     * @return array
     */
    private function prepareData($data)
    {
        $resultData = [];
        foreach ($data as $value) {
            $from = is_numeric($value['from']) ? $value['from'] : '*';
            $to   = is_numeric($value['to']) ? $value['to'] : '*';
            unset($value['from'], $value['to']);

            $rangeName              = "{$from}_{$to}";
            $resultData[$rangeName] = array_merge(['value' => $rangeName], $value);
        }

        return $resultData;
    }
}
