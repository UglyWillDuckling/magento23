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
use Mirasvit\Sorting\Criteria\ViewsCriterion;
use Magento\Framework\DB\Adapter\AdapterInterface;

class Views implements IndexerActionInterface, MviewActionInterface
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
        $table      = $this->resource->getTableName(ViewsCriterion::TABLE_NAME);
        $select     = $connection->select();

        $select->from($this->resource->getTableName('report_viewed_product_index'), [
                'product_id',
                'store_id',
                ViewsCriterion::COUNT => new \Zend_Db_Expr('COUNT(*)')
            ])
            ->where('index_id IN(?)', $ids)
            ->group('product_id');

        $connection->query($connection->insertFromSelect($select, $table, [
            ViewsCriterion::PRODUCT_ID,
            ViewsCriterion::STORE_ID,
            ViewsCriterion::COUNT
        ], AdapterInterface::INSERT_ON_DUPLICATE));
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $storeIds = array_keys($this->storeManager->getStores());

        foreach ($storeIds as $storeId) {
            $this->executeFullByStore($storeId);
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
     * @param int $storeId
     */
    private function executeFullByStore($storeId)
    {
        $connection = $this->resource->getConnection();
        $table      = $this->resource->getTableName(ViewsCriterion::TABLE_NAME);
        $select     = $connection->select();

        $connection->delete($table, ["store_id = {$storeId}"]);

        $select->from($this->resource->getTableName('report_viewed_product_index'), [
                'product_id',
                'store_id',
                ViewsCriterion::COUNT => new \Zend_Db_Expr('COUNT(*)')
            ])
            ->where('store_id = ?', $storeId)
            ->group('product_id');

        $connection->query($connection->insertFromSelect($select, $table, [
            ViewsCriterion::PRODUCT_ID,
            ViewsCriterion::STORE_ID,
            ViewsCriterion::COUNT
        ]));
    }
}
