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


namespace Mirasvit\SearchElastic\Model\Search;

use Magento\Framework\Search\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\Adapter\Mysql\IndexBuilderInterface;

class IndexBuilder implements IndexBuilderInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\CatalogSearch\Model\Search\TableMapper
     */
    private $tableMapper;

    /**
     * @param ResourceConnection                              $resource
     * @param \Magento\CatalogSearch\Model\Search\TableMapper $tableMapper
     */
    public function __construct(
        ResourceConnection $resource,
        \Magento\CatalogSearch\Model\Search\TableMapper $tableMapper
    ) {
        $this->resource = $resource;
        $this->tableMapper = $tableMapper;
    }

    /**
     * @param RequestInterface $request
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build(RequestInterface $request)
    {
    }
}
