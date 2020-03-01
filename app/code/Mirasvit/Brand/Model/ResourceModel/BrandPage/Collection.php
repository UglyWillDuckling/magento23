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



namespace Mirasvit\Brand\Model\ResourceModel\BrandPage;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Option\ArrayInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Api\Data\BrandPageStoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Collection extends AbstractCollection implements ArrayInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = BrandPageInterface::ID; //use in massaction

    /**
     * Collection constructor.
     * @param StoreManagerInterface $storeManager
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->connection = $connection;
        $this->resource = $resource;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\Brand\Model\BrandPage::class,
            \Mirasvit\Brand\Model\ResourceModel\BrandPage::class
        );
    }

    /**
     * Add Filter by store.
     *
     * @param int|\Magento\Store\Model\Store||\Magento\Store\Api\Data\StoreInterface $store
     *
     * @return $this
     */
    public function addStoreFilter($store)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        $this->getSelect()
            ->join(
                ['store_table' => $this->getTable(BrandPageStoreInterface::TABLE_NAME)],
                'main_table.' . BrandPageInterface::ID . ' = store_table.' . BrandPageStoreInterface::BRAND_PAGE_ID,
                []
            )
            ->where('store_table.' . BrandPageStoreInterface::STORE_ID . ' in (?)', [0, $store]);

        return $this;
    }

    /**
     * Add Filter by status.
     *
     * @param int $status
     *
     * @return $this
     */
    public function addEnableFilter($status = 1)
    {
        $this->getSelect()->where('main_table.' . BrandPageInterface::IS_ACTIVE . ' = ?', $status);

        return $this;
    }

    /**
     * @return $this
     */
    public function addStoreColumn()
    {
        $this->getSelect()
            ->columns(
                ['store_id' => new \Zend_Db_Expr(
                    "(SELECT GROUP_CONCAT(" . BrandPageStoreInterface::STORE_ID
                    . ") FROM `{$this->getTable(BrandPageStoreInterface::TABLE_NAME)}`
                    AS `" . BrandPageStoreInterface::TABLE_NAME . "`
                    WHERE main_table." . BrandPageInterface::ID
                    . " = " . BrandPageStoreInterface::TABLE_NAME
                    . "." . BrandPageStoreInterface::BRAND_PAGE_ID . ")")]
            );

        return $this;
    }

}