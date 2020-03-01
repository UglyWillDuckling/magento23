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


use Magento\Customer\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Sorting\Api\Data\CriterionInterface;

class DiscountCriterion implements CriterionInterface
{
    const TABLE_NAME        = 'mst_sorting_discount';

    const INDEX_ID          = 'index_id';
    const WEBSITE_ID        = 'website_id';
    const PRODUCT_ID        = 'product_id';
    const DISCOUNT_AMOUNT   = 'discount_amount';
    const DISCOUNT_PERCENT  = 'discount_percent';
    const CUSTOMER_GROUP_ID = 'customer_group_id';
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session, StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'biggest_saving';
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return __('Biggest Saving');
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
        return self::DISCOUNT_PERCENT;
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
        $websiteId = $this->storeManager->getWebsite()->getId();
        $customerGroupId = $this->session->isLoggedIn() ? $this->session->getCustomerGroupId() : 0;

        return "{{table}}.website_id = {$websiteId} and {{table}}.customer_group_id = {$customerGroupId}";
    }
}
