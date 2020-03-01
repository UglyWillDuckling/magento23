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



namespace Mirasvit\Sorting\Criteria;


use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Sorting\Api\Data\CriterionInterface;

class BestsellersCriterion implements CriterionInterface
{
    const TABLE_NAME   = 'mst_sorting_bestsellers';

    const INDEX_ID     = 'index_id';
    const STORE_ID     = 'store_id';
    const PRODUCT_ID   = 'product_id';
    const COUNT        = 'count'; // count of particular product in different orders
    const QTY_ORDERED  = 'qty_ordered'; // sum of product's qty_ordered across different orders
    const SALES_AMOUNT = 'sales_amount'; // sum of product's row_total across different orders
    const SALES_RATIO  = 'sales_ratio'; // some dynamically calculated rate
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'bestsellers';
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return __('Bestsellers');
    }

    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return self::TABLE_NAME;
    }

    /**
     * @inheritdoc
     */
    public function getJoinType()
    {
        return self::JOIN_LEFT;
    }

    /**
     * @inheritdoc
     */
    public function getField()
    {
        return self::COUNT;
    }

    /**
     * @inheritdoc
     */
    public function getPk()
    {
        return self::PRODUCT_ID;
    }

    /**
     * @inheritdoc
     */
    public function getCondition()
    {
        return "{{table}}.store_id = {$this->storeManager->getStore()->getId()}";
    }
}
