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
 * @package   mirasvit/module-sorting
 * @version   1.0.9
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Sorting\Service\Sorter;

use Mirasvit\Sorting\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

/**
 * Sorter places out of stock products to the end of the list.
 *
 * Class OutOfStockSorterService
 * @package Mirasvit\Sorting\Service\Sorter
 */
class OutOfStockSorterService
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    public function __construct(Config $config, StockConfigurationInterface $stockConfiguration)
    {
        $this->config = $config;
        $this->stockConfiguration = $stockConfiguration;
    }

    /**
     * @inheritdoc
     */
    public function sort(Collection $collection)
    {
        if ($this->config->isSortByOutOfStock()) {
            $join = true;
            $alias = 'cataloginventory_stock_status_index';
            $from = $collection->getSelect()->getPart('from');
            if (array_key_exists('stock_status_index', $from)) {
                $join = false;
                $alias = 'stock_status_index';
            }

            if ($join && !array_key_exists($alias, $from)) {
                $websiteId = $this->stockConfiguration->getDefaultScopeId();
                $joinCondition = $collection->getConnection()->quoteInto(
                    'e.entity_id = ' . $alias . '.product_id' . ' AND ' . $alias . '.website_id = ?',
                    $websiteId
                );

                $joinCondition .= $collection->getConnection()->quoteInto(
                    ' AND ' . $alias . '.stock_id = ?',
                    \Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID
                );
                $collection->getSelect()->joinLeft(
                    [$alias => $collection->getResource()->getTable('cataloginventory_stock_status')],
                    $joinCondition,
                    ['is_salable' => 'stock_status']
                );
            }

            $collection->getSelect()->order(new \Zend_Db_Expr($alias . '.stock_status DESC'));

            return $collection;
        }
    }
}
