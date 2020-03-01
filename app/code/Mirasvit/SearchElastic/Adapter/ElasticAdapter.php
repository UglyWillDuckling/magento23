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



namespace Mirasvit\SearchElastic\Adapter;

use Mirasvit\SearchElastic\Model\Config;
use Mirasvit\SearchElastic\Model\Engine;
use Magento\Framework\Search\AdapterInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Adapter\Mysql\ResponseFactory;
use Mirasvit\SearchElastic\Adapter\Aggregation\Builder as AggregationBuilder;
use Magento\Framework\Search\Adapter\Mysql\Adapter as MysqlAdapter;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ElasticAdapter implements AdapterInterface
{
    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var AggregationBuilder
     */
    private $aggregationBuilder;

    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var MysqlAdapter
     */
    private $mysqlAdapter;

    public function __construct(
        Mapper $mapper,
        ResponseFactory $responseFactory,
        AggregationBuilder $aggregationBuilder,
        Engine $engine,
        Config $config,
        MysqlAdapter $mysqlAdapter
    ) {
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->engine = $engine;
        $this->config = $config;
        $this->mysqlAdapter = $mysqlAdapter;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\Search\Response\QueryResponse
     * @SuppressWarnings(PHPMD)
     */
    public function query(RequestInterface $request)
    {
        $client = $this->engine->getClient();
        $query = $this->mapper->buildQuery($request);

        if (!$this->engine->isAvailable()) {
            return $this->mysqlAdapter->query($request);
        }

        if ($request->getName() == 'quick_search_container'
            || $request->getName() == 'catalog_view_container'
            || $request->getName() == 'catalogsearch_fulltext'
        ) {
            $query = $this->filterByStockStatus($query);
        }

        if (filter_input(INPUT_GET, 'debug')!== null) {
            var_dump($query);
        }

        unset($query['body']['query']['bool']['must']['2']);
        $query['body']['query']['bool']['must'] = array_values($query['body']['query']['bool']['must']);

        $attempt = 0;
        $response = false;
        $exception = false;

        while ($attempt < 5 && $response === false) {
            $attempt++;

            try {
                $response = $client->search($query);
            } catch (\Exception $e) {
                $exception = $e;
            }
        }

        if (!$response && $exception) {
            throw $exception;
        }

        if (filter_input(INPUT_GET, 'debug')!== null) {
            var_dump($response);
        }

        $hits = isset($response['hits']['hits']) ? $response['hits']['hits'] : [];
        $hits = array_slice($hits, 0, $this->config->getResultsLimit());

        $documents = [];
        foreach ($hits as $doc) {
            $d = [
                'id'        => $doc['_id'],
                'entity_id' => $doc['_id'],
                'score'     => $doc['_score'],
                'data'      => isset($doc['_source']) ? $doc['_source'] : [],
            ];

            $documents[] = $d;
        }

        return $this->responseFactory->create([
            'documents'    => $documents,
            'aggregations' => $this->aggregationBuilder->extract($request, $response),
            'total'        => count($documents)
        ]);
    }

    /**
     * @param array $query
     * @return array
     */
    private function filterByStockStatus($query)
    {
        if ($this->config->isShowOutOfStock() == false) {
            $query['body']['query']['bool']['must'][] = [
                'term' => [
                    'is_in_stock_raw' => 1,
                ],
            ];
        }

        return $query;
    }
}
