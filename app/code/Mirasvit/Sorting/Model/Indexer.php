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



namespace Mirasvit\Sorting\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;


class Indexer implements IndexerActionInterface, MviewActionInterface, IdentityInterface
{
    /**
     * Indexer ID in configuration
     */
    const INDEXER_ID = 'mst_sorting';

    /**
     * @var \Magento\Framework\Indexer\ActionInterface[]
     */
    private $indexers;
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    public function __construct(
        ManagerInterface $eventManager,
        array $indexers = []
    ) {
        $this->indexers = $indexers;
        $this->eventManager = $eventManager;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        foreach ($this->indexers as $indexer) {
            $indexer->executeFull();
        }

        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this]);
    }

    /**
     * Get affected cache tags
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getIdentities()
    {
        return [
            \Magento\Catalog\Model\Category::CACHE_TAG
        ];
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
    }
}
