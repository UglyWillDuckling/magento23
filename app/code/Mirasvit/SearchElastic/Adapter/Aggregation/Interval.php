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

use Magento\Framework\Search\Dynamic\IntervalInterface;
use Magento\Framework\Search\Request\Dimension;
use Mirasvit\SearchElastic\Model\Config;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Mirasvit\SearchElastic\Model\Engine;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;

class Interval implements IntervalInterface
{
    const DELTA = 0.9;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var array
     */
    private $entityIds;

    /**
     * @var IndexScopeResolver
     */
    private $resolver;

    public function __construct(
        Config $config,
        Engine $engine,
        IndexScopeResolver $resolver,
        $fieldName,
        $storeId,
        $entityIds
    )
    {
        $this->config = $config;
        $this->engine = $engine;
        $this->resolver = $resolver;
        $this->fieldName = $fieldName;
        $this->storeId = $storeId;
        $this->entityIds = $entityIds;
    }

    /**
     * {@inheritdoc}
     */
    public function load($limit, $offset = null, $lower = null, $upper = null)
    {
        $from = $to = [];
        if ($lower) {
            $from = ['gte' => $lower - self::DELTA];
        }
        if ($upper) {
            $to = ['lt' => $upper - self::DELTA];
        }

        $dimension = new Dimension('scope', $this->storeId);

        $indexName = $this->config->getIndexName($this->resolver->resolve(Fulltext::INDEXER_ID, [$dimension]));
        $query = [
            'index' => $indexName,
            'type'  => Config::DOCUMENT_TYPE,
            'body'  => [
                '_source' => true,
                'query'   => [
                    'bool' => [
                        'must' => [
                            [
                                'terms' => [
                                    '_id' => $this->entityIds,
                                ],
                            ],
                            [
                                'range' => [
                                    $this->fieldName . '_raw' => array_merge($from, $to),
                                ],
                            ],
                        ],
                    ],
                ],
                'size'    => $limit,
            ],
        ];
        if ($offset) {
            $query['body']['from'] = $offset;
        }

        $result = $this->engine->getClient()->search($query);

        $result = $this->arrayValuesToFloat($result['hits']['hits']);

        while (count($result) < $limit) {
            $result[] = $upper;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function loadPrevious($data, $index, $lower = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function loadNext($data, $rightIndex, $upper = null)
    {
    }

    private function arrayValuesToFloat($hits)
    {
        $returnPrices = [];
        foreach ($hits as $hit) {
            $returnPrices[] = (float)$hit['_source'][$this->fieldName . '_raw'];
        }

        return $returnPrices;
    }
}
