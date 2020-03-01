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

use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;

class TermBucket
{
    /**
     * @param RequestBucketInterface $bucket
     * @param array                  $response
     *
     * @return array
     */
    public function build(
        RequestBucketInterface $bucket,
        array $response
    ) {
        $values = [];
        if (isset($response['aggregations'][$bucket->getName()]['buckets'])) {
            foreach ($response['aggregations'][$bucket->getName()]['buckets'] as $resultBucket) {
                $values[$resultBucket['key']] = [
                    'value' => $resultBucket['key'],
                    'count' => $resultBucket['doc_count'],
                ];
            }
        } else {
            foreach ($response['aggregations'][$bucket->getName()]['child-filter'][$bucket->getName()]['buckets'] as $resultBucket) {
                $values[$resultBucket['key']] = [
                    'value' => $resultBucket['key'],
                    'count' => $resultBucket[$bucket->getName() . '_count']['doc_count'],
                ];
            }
        }

        return $values;
    }
}
