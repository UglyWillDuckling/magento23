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



namespace  Mirasvit\Brand\Ui\BrandPage\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiComponentDataProvider;
use Mirasvit\Brand\Api\Data\BrandPageStoreInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Mirasvit\Brand\Api\Service\BrandAttributeServiceInterface;

class DataProvider extends UiComponentDataProvider
{
    /**
     * DataProvider constructor.
     * @param ResourceConnection $resource
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param ConfigInterface $config
     * @param BrandAttributeServiceInterface $brandAttributeService
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        ResourceConnection $resource,
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        ConfigInterface $config,
        BrandAttributeServiceInterface $brandAttributeService,
        array $meta = [],
        array $data = []
    ) {
        $this->connection = $resource;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->config = $config;
        $this->brandAttributeService = $brandAttributeService;
    }

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();
        $arrItems['items'] = [];

        $storeIds = [];
        if ($data = $searchResult->getData()) { //prepare store_id for multistore
            foreach ($data as $value) {
                $storeIds[$value[BrandPageStoreInterface::BRAND_PAGE_ID]] = $value[BrandPageStoreInterface::STORE_ID];
            }
        }

        foreach ($searchResult->getItems() as $item) {
            if (isset($storeIds[$item->getId()])) {  //prepare store_id for multistore
                $item->setData(BrandPageStoreInterface::STORE_ID, $storeIds[$item->getId()]);
            }
            $arrItems['items'][] = $item->getData();

        }

        return $arrItems;
    }

    /**
     * Returns Search result
     *
     * @return SearchResultInterface
     */
    public function getSearchResult()
    {
        $groups     = [];
        $fieldStoreValue = '';

        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($this->getSearchCriteria()->getFilterGroups() as $group) {
            if (empty($group->getFilters())) {
                continue;
            }
            $filters = [];
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == BrandPageStoreInterface::STORE_ID) {
                    $fieldStoreValue = $filter->getValue();
                    continue;
                }
                $filters[] = $filter;
            }
            $group->setFilters($filters);
            $groups[] = $group;
        }
        $this->getSearchCriteria()->setFilterGroups($groups);

        $collection = $this->getPreparedCollection($fieldStoreValue);

        return $collection;
    }

    /**
     * @param string $fieldStoreValue
     * @return \Mirasvit\SeoAutolink\Model\ResourceModel\Link\Grid\Collection
     */
    protected function getPreparedCollection($fieldStoreValue)
    {
        $collection = $this->reporting->search($this->getSearchCriteria());
        $this->prepareCollectionWithBrandAttribute($collection);

        if ($fieldStoreValue) {
            $dataIds = $this->getDataIds($fieldStoreValue);
            $collection->addStoreColumn()->getSelect()
                ->where(
                    new \Zend_Db_Expr(BrandPageStoreInterface::BRAND_PAGE_ID . ' IN (' . implode(',', $dataIds) . ')')
                );

        }

        return $collection;
    }

    /**
     * @param $collection
     * return void
     */
    protected function prepareCollectionWithBrandAttribute($collection)
    {
        if ($brandAttributeId = $this->brandAttributeService->getBrandAttributeId()) {
            $collection->addFieldTofilter(BrandPageInterface::ATTRIBUTE_ID,
                $brandAttributeId
            );
        } else {
            $collection->addFieldTofilter(BrandPageInterface::ATTRIBUTE_ID, 0); //hide all rows
        }
    }

    /**
     * @param string $fieldStoreValue
     * @return array
     */
    protected function getDataIds($fieldStoreValue)
    {
        $query = 'SELECT ' . BrandPageStoreInterface::BRAND_PAGE_ID . ' FROM '
            . $this->connection->getTableName(BrandPageStoreInterface::TABLE_NAME)
            . ' WHERE ' . BrandPageStoreInterface::STORE_ID
            . ' IN (' . addslashes(implode(',', $fieldStoreValue)) . ')';

        $storeData = $this->connection->getConnection('read')->fetchAll($query);
        $dataIds = [];
        foreach ($storeData as $store) {
            $dataIds[] = $store[BrandPageStoreInterface::BRAND_PAGE_ID];
        }

        if (!$dataIds) {
            $dataIds[] = 0;
        }

        return $dataIds;
    }

}
