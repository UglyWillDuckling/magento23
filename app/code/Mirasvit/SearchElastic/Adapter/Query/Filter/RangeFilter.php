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


namespace Mirasvit\SearchElastic\Adapter\Query\Filter;

use Magento\Framework\Search\Request\Filter\Range;

class RangeFilter
{
    /**
     * @param Range $filter
     * @return array
     */
    public function build(Range $filter)
    {
        $query = [];

        if ($filter->getFrom()) {
            $query['range'][$filter->getField() . '_raw']['gte'] = floatval($filter->getFrom());
        }

        if ($filter->getTo()) {
            $query['range'][$filter->getField() . '_raw']['lte'] = floatval($filter->getTo());
        }

        return [$query];
    }
}
