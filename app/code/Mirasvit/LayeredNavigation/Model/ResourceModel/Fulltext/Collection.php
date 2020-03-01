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



namespace Mirasvit\LayeredNavigation\Model\ResourceModel\Fulltext;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Phrase;
use Magento\Framework\Search\Response\QueryResponse;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Mirasvit\LayeredNavigation\Model\Layer\Filter\Attribute;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Magento\Framework\Data\Collection\EntityFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Catalog\Model\ResourceModel\Helper;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Catalog\Model\Indexer\Product\Flat\State;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Search\Model\QueryFactory;
use Mirasvit\LayeredNavigation\Model\Request\Builder;
use Magento\Search\Model\SearchEngine;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;

/**
 * Fulltext Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    use ConfigTrait;

    /** @var  QueryResponse */
    private $queryResponse;

    /**
     * Catalog search data
     *
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory = null;

    /**
     * \Mirasvit\LayeredNavigation\Model\Request\Builder
     */
    private $requestBuilder;

    /**
     * @var \Magento\Search\Model\SearchEngine
     */
    private $searchEngine;

    /**
     * @var string
     */
    private $queryText;

    /**
     * @var string|null
     */
    private $order = null;

    /**
     * @var string
     */
    private $searchRequestName;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory
     */
    private $temporaryStorageFactory;

    /**
     * \Mirasvit\LayeredNavigation\Model\Request\Builder
     */
    private $cloneRequestBuilder;

    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        ResourceConnection $resource,
        EavEntityFactory $eavEntityFactory,
        Helper $resourceHelper,
        UniversalFactory $universalFactory,
        StoreManagerInterface $storeManager,
        Manager $moduleManager,
        State $catalogProductFlatState,
        ScopeConfigInterface $scopeConfig,
        OptionFactory $productOptionFactory,
        Url $catalogUrl,
        TimezoneInterface $localeDate,
        Session $customerSession,
        DateTime $dateTime,
        GroupManagementInterface $groupManagement,
        QueryFactory $queryFactory,
        Builder $requestBuilder,
        SearchEngine $searchEngine,
        TemporaryStorageFactory $temporaryStorageFactory,
        $connection = null,
        $searchRequestName = 'catalog_view_container'
    ) {
        $this->queryFactory = $queryFactory;
        $this->searchRequestName = $searchRequestName;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );

        $this->requestBuilder = $requestBuilder;
        $this->searchEngine = $searchEngine;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
    }

    /**
     * Search query filter
     *
     * @param string $query
     * @return $this
     */
    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setRequestData($builder)
    {
        $this->requestBuilder = $builder;
        $this->queryResponse = null;
        $this->_isFiltersRendered = false;
    }

    /**
     * @return \Mirasvit\LayeredNavigation\Model\Request\Builder
     */
    public function getCloneRequestBuilder()
    {
        if (!$this->cloneRequestBuilder) {
            $this->createdRequestBuilder();
        }
        return $this->cloneRequestBuilder;
    }

    /**
     * @return void
     */
    private function createdRequestBuilder()
    {
        $this->cloneRequestBuilder = clone $this->requestBuilder;
        $this->cloneRequestBuilder->bindDimension('scope', $this->getStoreId());
        if ($this->queryText) {
            $this->cloneRequestBuilder->bind('search_term', $this->queryText);
        }

        $this->cloneRequestBuilder->setRequestName($this->searchRequestName);
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function setOrder($attribute, $dir = Select::SQL_DESC)
    {
        $this->order = ['field' => $attribute, 'dir' => $dir];
        if ($attribute != 'relevance') {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Stub method for compatibility with other search engines
     *
     * @return $this
     */
    public function setGeneralDefaultQuery()
    {
        return $this;
    }

    /**
     * @param $field
     * @param QueryResponse|null $response
     * @return array
     * @throws StateException
     */
    public function getFacetedData($field, QueryResponse $response = null)
    {
        $this->_renderFilters();

        $response = $response ? $response : $this->queryResponse;

        $aggregations = $response->getAggregations();
        $bucket = $aggregations->getBucket($field . '_bucket');
        if (!$bucket) {
            return [];
        }

        $result = [];

        foreach ($bucket->getValues() as $value) {
            $metrics = $value->getMetrics();
            $result[$metrics['value']] = $metrics;
        }

        return $result;
    }

    /**
     * Specify category filter for product collection
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return $this
     */
    public function addCategoryFilter(\Magento\Catalog\Model\Category $category)
    {
        $this->addFieldToFilter('category_ids', $category->getId());

        return parent::addCategoryFilter($category);
    }

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function addCategoryMultiFilter($categoryIds)
    {
        $this->addFieldToFilter('category_ids', ['in' => $categoryIds]);

        return $this;
    }

    /**
     * Apply attribute filter to facet collection
     *
     * @param string $field
     * @param null|array|string $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($this->queryResponse !== null) {
            throw new \RuntimeException('Illegal state');
        }
        if (!is_array($condition)
            || (!in_array(key($condition), ['from', 'to'], true)
                && $field != 'visibility')) {
            $this->requestBuilder->bind($field, $condition);
        } else {
            if (!empty($condition['from'])) {
                $this->requestBuilder->bind("{$field}.from", $condition['from']);
            }
            if (!empty($condition['to'])) {
                $this->requestBuilder->bind("{$field}.to", $condition['to']);
            }
        }

        return $this;
    }

    /**
     * Set product visibility filter for enabled products
     *
     * @param array $visibility
     * @return $this
     */
    public function setVisibility($visibility)
    {
        $this->addFieldToFilter('visibility', $visibility);
        return parent::setVisibility($visibility);
    }

    /**
     * Get collection size
     *
     * @return int
     */
    public function getSize()
    {
        if (!$this->_totalRecords) {
            $sql = $this->getSelectCountSql();
            $this->_totalRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
        }

        return intval($this->_totalRecords);
    }

    /**
     * Hook for operations before rendering filters
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->requestBuilder->bindDimension('scope', $this->getStoreId());
        if ($this->queryText) {
            $this->requestBuilder->bind('search_term', $this->queryText);
        }

        $priceRangeCalculation = $this->_scopeConfig->getValue(
            \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory::XML_PATH_RANGE_CALCULATION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($priceRangeCalculation) {
            $this->requestBuilder->bind('price_dynamic_algorithm', $priceRangeCalculation);
        }

        $this->requestBuilder->setRequestName($this->searchRequestName);
        $this->cloneRequestBuilder = clone $this->requestBuilder;
        $queryRequest = $this->requestBuilder->create();
        $this->queryResponse = $this->searchEngine->search($queryRequest);

        // save response to attribute filter cache to avoid duplicate searches
        // fixes issue when price navigation step calculation set to improved algorithm
        $hash = $this->requestBuilder->hash($queryRequest);
        Attribute::$responseCache[$hash] = $this->queryResponse;

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table = $temporaryStorage->storeApiDocuments($this->queryResponse->getIterator());

        $this->getSelect()->joinInner(
            [
                'search_result' => $table->getName(),
            ],
            'e.entity_id = search_result.' . TemporaryStorage::FIELD_ENTITY_ID,
            []
        );

        $this->_totalRecords = $this->queryResponse->count();

        if ($this->order && 'relevance' === $this->order['field']) {
            $this->getSelect()->order('search_result.' . TemporaryStorage::FIELD_SCORE . ' ' . $this->order['dir']);
        }

        return parent::_renderFiltersBefore();
    }

    /**
     * Add order by entity_id
     *
     * @return $this
     */
    protected function _renderOrders()
    {
        if (!$this->_isOrdersRendered) {
            parent::_renderOrders();
            if (count($this->_orders)) { //fix for search engines)
                $filters = $this->_productLimitationFilters;
                if (isset($filters['category_id']) || isset($filters['visibility'])) {
                    $this->getSelect()->order("e.entity_id ASC");
                }
            }
        }
        return $this;
    }

    /**
     * Filter Product by Categories
     *
     * @param array $pricesFilter
     * @return $this
     */
    public function addPricesFilter(array $pricesFilter)
    {
        foreach ($pricesFilter as $field => $condition) {
            foreach ($condition as $key => $value) {
                if (!$value['to']) {
                    unset($condition[$key]['to']);
                }
                if (!$value['from']) {
                    unset($condition[$key]['from']);
                }
            }
            $this->getSelect()->where($this->getConnection()->prepareSqlCondition($field, $condition));
        }
        return $this;
    }
    public function addAttributeToSort($attribute,
                                       $dir = \Magento\Catalog\Model\ResourceModel\Product\Collection::SORT_ORDER_ASC)
    {
        if ($attribute === 'name' && $this->isEnabledFlat()) {
            if ($column = $this->getEntity()->getAttributeSortColumn($attribute)) {
                return $this->getSelect()->order(
                    new \Zend_Db_Expr("TRIM(BOTH  '\"' FROM e.{$column}) ASC") // todo use default sorting dir
                );
            }
        }

        return \Magento\Catalog\Model\ResourceModel\Product\Collection::addAttributeToSort($attribute, $dir);
    }
}
