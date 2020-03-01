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

use Magento\Framework\Search\Request\Filter\Term;

class TermFilter
{
    /**
     * @param Term $filter
     *
     * @return array
     */
    public function build(Term $filter)
    {
        $query = [];
        if ($filter->getValue()) {
            $value = $filter->getValue();

            if (is_string($value)) {
                $value = array_filter(explode(',', $value));

                if (count($value) === 1) {
                    $value = $value[0];
                }
            }

            $condition = is_array($value) ? 'terms' : 'term';

            if (is_array($value)) {
                if (key_exists('in', $value)) {
                    $value = $value['in'];
                }

                $value = array_values($value);
            }

            $field = $filter->getField() . '_raw';

            if ($field == 'entity_id_raw') {
                $field = 'entity_id';
            }

            $query[] = [
                $condition => [
                    $field => $value,
                ],
            ];
        }

        return $query;
    }
}
