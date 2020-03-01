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



namespace Mirasvit\LayeredNavigation\Traits;

trait ElasticQueryTrait
{
    /**
     * @return int
     */
    public function prepareElasticQuery($searchQuery)
    {
        if (isset($searchQuery['body']['query']['bool']['must'])) {
            foreach ($searchQuery['body']['query']['bool']['must'] as $index => $node) {
                if (isset($searchQuery['body']['query']['bool']['must'][$index]['terms'])
                    && ($terms = $searchQuery['body']['query']['bool']['must'][$index]['terms'])
                    && ($key = key($terms)) && isset($terms[$key]['in'])) {
                    $searchQuery['body']['query']['bool']['must'][$index]['terms'][$key] = $terms[$key]['in'];
                }
            }
        }

        return $searchQuery;
    }

    /**
     * Fix only 10 items in navigation for Elasticsearch 1.7.6
     *
     * @return int
     */
    public function setElasticAggregationSize($searchQuery)
    {
        if (isset($searchQuery['body']['aggregations'])
                && is_array($searchQuery['body']['aggregations'])) {
            foreach ($searchQuery['body']['aggregations'] as &$bucket) {
                if (isset($bucket['terms'])
                    && !isset($bucket['terms']['size'])) {
                        $bucket['terms']['size'] = 999;
                }
            }
        }

        return $searchQuery;
    }
}
