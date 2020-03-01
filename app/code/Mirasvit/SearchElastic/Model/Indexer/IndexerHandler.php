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



namespace Mirasvit\SearchElastic\Model\Indexer;

use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Registry;
use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;
use Mirasvit\SearchElastic\Model\Engine;
use Mirasvit\SearchElastic\Model\Config;
use Mirasvit\Core\Service\CompatibilityService;

class IndexerHandler implements IndexerInterface
{
    const ACTIVE_INDEX = 'active_index';

    /**
     * @var array
     */
    private $data;

    /**
     * @var Batch
     */
    private $batch;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    /**
     * @var ScopeProxy / IndexScopeResolver
     */
    private $indexScopeResolver;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        IndexRepositoryInterface $indexRepository,
        Batch $batch,
        Engine $engine,
        Config $config,
        Registry $registry,
        array $data,
        $batchSize = 1000
    ) {
        $this->indexRepository = $indexRepository;
        $this->batch = $batch;
        $this->engine = $engine;
        $this->config = $config;
        $this->registry = $registry;
        $this->data = $data;
        $this->batchSize = $batchSize;
        if (CompatibilityService::is22() || CompatibilityService::is23()) {
            $this->indexScopeResolver = CompatibilityService::getObjectManager()
                ->create('Magento\CatalogSearch\Model\Indexer\Scope\ScopeProxy');
        } else {
            $this->indexScopeResolver = CompatibilityService::getObjectManager()
                ->create('Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver');
        }
    }

    /**
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @param \Traversable $documents
     * @return void
     */
    public function saveIndex($dimensions, \Traversable $documents)
    {
        $index = $this->indexRepository->get($this->getIndexId());
        $instance = $this->indexRepository->getInstance($index);
        $indexName = $this->indexScopeResolver->resolve($this->getIndexName(), $dimensions);

        foreach ($this->batch->getItems($documents, $this->batchSize) as $docs) {
            foreach ($instance->getDataMappers('elastic') as $mapper) {
                $docs = $mapper->map($docs, $dimensions, $index->getIdentifier());
            }

            $this->engine->saveDocuments($indexName, $docs);
        }
    }

    /**
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @param \Traversable $documents
     * @return void
     */
    public function deleteIndex($dimensions, \Traversable $documents)
    {
        $indexName = $this->indexScopeResolver->resolve($this->getIndexName(), $dimensions);

        foreach ($this->batch->getItems($documents, $this->batchSize) as $batchDocuments) {
            $this->engine->deleteDocuments($indexName, $batchDocuments);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cleanIndex($dimensions)
    {
        $index = $this->indexRepository->get($this->getIndexId());
        $indexName = $this->indexScopeResolver->resolve($this->getIndexName(), $dimensions);

        $this->registry->register(self::ACTIVE_INDEX, $index->getIdentifier(), true);

        $this->engine->cleanDocuments($indexName);
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable($dimensions = [])
    {
        return true;
    }

    /**
     * @return string
     */
    private function getIndexName()
    {
        return $this->data['indexer_id'];
    }

    /**
     * @return string
     */
    private function getIndexId()
    {
        return isset($this->data['index_id'])
            ? $this->data['index_id']
            : $this->indexRepository->get('catalogsearch_fulltext')->getId();
    }
}
