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


namespace Mirasvit\Sorting\Model\Indexer;

use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Mirasvit\Sorting\Criteria\DiscountCriterion;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Discount implements IndexerActionInterface, MviewActionInterface
{
    /**
     * @var array index structure
     */
    protected $data;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        ResourceConnection $resource,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->resource = $resource;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $connection = $this->resource->getConnection();
        $table      = $this->resource->getTableName(DiscountCriterion::TABLE_NAME);
        $select     = $connection->select();

        $select->from($this->resource->getTableName('catalog_product_index_price'), [
                'entity_id',
                'website_id',
                'customer_group_id',
                DiscountCriterion::DISCOUNT_PERCENT  => new \Zend_Db_Expr('(price - final_price) / (price / 100)'),
                DiscountCriterion::DISCOUNT_AMOUNT => new \Zend_Db_Expr('(price - final_price)'),
            ])
            ->where('entity_id IN(?)', $ids)
            ->where('price <> final_price');

        $connection->query($connection->insertFromSelect($select, $table, [
            DiscountCriterion::PRODUCT_ID,
            DiscountCriterion::WEBSITE_ID,
            DiscountCriterion::CUSTOMER_GROUP_ID,
            DiscountCriterion::DISCOUNT_PERCENT,
            DiscountCriterion::DISCOUNT_AMOUNT,
        ], AdapterInterface::INSERT_ON_DUPLICATE));
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $websiteIds = array_keys($this->storeManager->getWebsites());

        foreach ($websiteIds as $websiteId) {
            $this->executeFullByStore($websiteId);
        }
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    /**
     * Execute full indexation by storeID
     *
     * @param int $websiteId
     */
    private function executeFullByStore($websiteId)
    {
        $connection = $this->resource->getConnection();
        $table      = $this->resource->getTableName(DiscountCriterion::TABLE_NAME);
        $select     = $connection->select();

        $connection->delete($table, ["website_id = {$websiteId}"]);

        $select->from($this->resource->getTableName('catalog_product_index_price'), [
                'entity_id',
                'website_id',
                'customer_group_id',
                DiscountCriterion::DISCOUNT_PERCENT  => new \Zend_Db_Expr('(price - final_price) / (price / 100)'),
                DiscountCriterion::DISCOUNT_AMOUNT => new \Zend_Db_Expr('(price - final_price)'),
            ])
            ->where('website_id = ?', $websiteId)
            ->where('price <> final_price');

        $connection->query($connection->insertFromSelect($select, $table, [
            DiscountCriterion::PRODUCT_ID,
            DiscountCriterion::WEBSITE_ID,
            DiscountCriterion::CUSTOMER_GROUP_ID,
            DiscountCriterion::DISCOUNT_PERCENT,
            DiscountCriterion::DISCOUNT_AMOUNT,
        ]));
    }
}
