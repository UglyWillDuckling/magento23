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

class WishedCriterion implements CriterionInterface
{
    const TABLE_NAME = 'mst_sorting_wished';

    const INDEX_ID   = 'index_id';
    const STORE_ID   = 'store_id';
    const PRODUCT_ID = 'product_id';
    const COUNT      = 'count';
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
        return 'most_wished';
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return __('Most Wished');
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
