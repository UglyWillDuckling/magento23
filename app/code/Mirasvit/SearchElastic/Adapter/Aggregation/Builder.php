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

use Magento\Framework\Search\RequestInterface;
use Mirasvit\SearchElastic\Adapter\DataProvider;

class Builder
{
    /**
     * @var DynamicBucket
     */
    private $dynamicBucket;

    /**
     * @var TermBucket
     */
    private $termBucket;

    /**
     * @param DynamicBucket $dynamicBucket
     * @param TermBucket    $termBucket
     * @param DataProvider  $dataProvider
     */
    public function __construct(
        DynamicBucket $dynamicBucket,
        TermBucket $termBucket,
        DataProvider $dataProvider
    ) {
        $this->dynamicBucket = $dynamicBucket;
        $this->termBucket = $termBucket;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param RequestInterface $request
     * @param array            $response
     * @return array
     * @throws \Exception
     */
    public function extract(RequestInterface $request, array $response)
    {
        $aggregations = [];
        $buckets = $request->getAggregation();

        foreach ($buckets as $bucket) {
            if ($bucket->getType() == 'dynamicBucket') {
                $aggregations[$bucket->getName()] = $this->dynamicBucket->build(
                    $bucket,
                    $request->getDimensions(),
                    $response,
                    $this->dataProvider
                );
            } elseif ($bucket->getType() == 'termBucket') {
                $aggregations[$bucket->getName()] = $this->termBucket->build(
                    $bucket,
                    $response
                );
            } else {
                throw new \Exception("Bucket type not implemented.");
            }
        }

        return $aggregations;
    }
}
