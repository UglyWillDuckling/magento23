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

use Magento\Framework\Search\Request\FilterInterface;
use Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;

class MultiselectCategoryPriceFilterQuery
{
    public function __construct(
        Session $customerSession = null,
        ResourceConnection $resource
    ) {
        $this->customerSession = $customerSession;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }

    /**
     * @param Preprocessor $subject
     * @param \Closure $proceed
     * @param FilterInterface $filter
     * @param $isNegation
     * @param $query
     * @return mixed|string
     */
    public function aroundProcess(
        Preprocessor $subject,
        \Closure $proceed,
        FilterInterface $filter,
        $isNegation,
        $query
    ) {
        if ($filter->getField() === 'category_ids'
            && is_array($filter->getValue())
            && isset($filter->getValue()['in'])) {
                return $this->getCategoryQuery($filter->getValue());
        }

        if ($filter->getField() === 'price'
            && ((strpos($filter->getFrom(), ',') !== false)
                || (strpos($filter->getTo(), ',') !== false))
            ) {
                return $this->getPriceQuery($filter->getFrom(), $filter->getTo());
        }

        return $proceed($filter, $isNegation, $query);
    }

    /**
     * @param array $filterValue
     * @return string
     */
    private function getCategoryQuery($filterValue)
    {
        return 'category_ids_index.category_id IN (' . implode(',', $filterValue['in']) . ')';
    }

    /**
     * @param string $filterFrom
     * @param string $filterTo
     * @return string
     */
    private function getPriceQuery($filterFrom, $filterTo)
    {
        $select = [];
        $from = explode(',', $filterFrom);
        $to = explode(',', $filterTo);
        $from = $this->prepareFromFilter($from);

        foreach ($from as $key => $value) {
            $toPrepared = (isset($to[$key]) && $to[$key]) ? ' AND price_index.min_price <= ' . $to[$key] : '';
            $select[] = '(price_index.min_price >= ' . $value . $toPrepared  . ')';
        }

        $resultQuery = implode(' OR ', $select);

        $resultQuery .= sprintf(
            ' AND %s = %s',
            $this->connection->quoteIdentifier('price_index.customer_group_id'),
            $this->customerSession->getCustomerGroupId()
        );

        return $resultQuery;
    }

    /**
     * @param array $from
     * @return string
     */
    private function prepareFromFilter($from)
    {
        foreach ($from as $key => $value) {
            if ($value == '') {
                $from[$key] = 0;
            }
        }

        return $from;
    }
}
