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

use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\RequestInterface;

class AggregationQuery
{
    /**
     * @param RequestInterface $request
     *
     * @return array
     */
    public function build(
        RequestInterface $request
    ) {
        $query = [];

        $buckets = $request->getAggregation();
        foreach ($buckets as $bucket) {
            $query = array_merge_recursive($query, $this->buildBucket($bucket));
        }

        return $query;
    }

    /**
     * @param BucketInterface $bucket
     *
     * @return array
     */
    protected function buildBucket(
        BucketInterface $bucket
    ) {
        $field = $bucket->getField();

        if ($bucket->getType() == BucketInterface::TYPE_TERM) {
            $result = [
                $bucket->getName() => [
                    'terms' => [
                        'field' => $field . '_raw',
                        'size'  => 500,
                    ],
                ],
            ];

            if (!in_array($field, ['category_ids', 'stock_status'])) {

                $result = [
                    $bucket->getName() => [
                        'nested'       => [
                            'path' => 'children',
                        ],
                        'aggregations' => [
                            'child-filter' => [
                                'filter'       => [
                                    'term' => [
                                        'children.is_in_stock_raw' => 1,
                                    ],
                                ],
                                'aggregations' => [
                                    $bucket->getName() => [
                                        'terms'        => [
                                            'field' => 'children.' . $field . '_raw',
                                            'size'  => 500,
                                        ],
                                        'aggregations' => [
                                            $bucket->getName() . '_count' => [
                                                'reverse_nested' => (object)[],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }

            return $result;
        } elseif ($bucket->getType() == BucketInterface::TYPE_DYNAMIC) {
            return [
                $bucket->getName() => [
                    'extended_stats' => [
                        'field' => $field . '_raw',
                    ],
                ],
            ];
        }

        return [];
    }
}
