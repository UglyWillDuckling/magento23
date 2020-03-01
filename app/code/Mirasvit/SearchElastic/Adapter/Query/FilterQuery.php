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



namespace Mirasvit\SearchElastic\Adapter\Query;

use Magento\Framework\Search\Request\FilterInterface;

class FilterQuery
{
    public function __construct(
        Filter\TermFilter $termFilter,
        Filter\RangeFilter $rangeFilter,
        Filter\WildcardFilter $wildcardFilter
    ) {
        $this->termFilter = $termFilter;
        $this->rangeFilter = $rangeFilter;
        $this->wildcardFilter = $wildcardFilter;
    }

    /**
     * @param FilterInterface $filter
     * @return array
     * @throws \Exception
     */
    public function build(FilterInterface $filter)
    {
        if ($filter->getType() == FilterInterface::TYPE_TERM) {
            /** @var \Magento\Framework\Search\Request\Filter\Term $filter */
            $query = [
                'bool' => [
                    'must' => $this->termFilter->build($filter),
                ],
            ];
        } elseif ($filter->getType() == FilterInterface::TYPE_RANGE) {
            /** @var \Magento\Framework\Search\Request\Filter\Range $filter */
            $query = [
                'bool' => [
                    'must' => $this->rangeFilter->build($filter),
                ],
            ];
        } elseif ($filter->getType() == FilterInterface::TYPE_WILDCARD) {
            /** @var \Magento\Framework\Search\Request\Filter\Wildcard $filter */
            $query = [
                'bool' => [
                    'must' => $this->wildcardFilter->build($filter),
                ],
            ];
        }

        return $query;
    }
}
