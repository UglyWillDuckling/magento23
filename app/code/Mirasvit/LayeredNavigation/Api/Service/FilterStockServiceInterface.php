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



namespace Mirasvit\LayeredNavigation\Api\Service;

use Magento\Framework\DB\Select;
use Magento\Framework\DB\Ddl\Table;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

interface FilterStockServiceInterface
{
    /**
     * @param array $dimensions
     * @param Table $entityIdsTable
     * @return Select
     */
    public function createStockFilterSelect($dimensions, $entityIdsTable);

    /**
     * @param array $dimensions
     * @return int
     */
    public function getCurrentScope($dimensions);

    /**
     * @param Select $select
     * @return void
     */
    public function addStockToSelect($select);

}