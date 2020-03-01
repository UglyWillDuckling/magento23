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


use Mirasvit\Sorting\Api\Data\CriterionInterface;

class NewCriterion implements CriterionInterface
{
    const TABLE_NAME = 'catalog_product_entity';

    const PRODUCT_ID = 'entity_id';
    const CREATED_AT = 'created_at';

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return 'new_arrivals';
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return __('New');
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
        return self::CREATED_AT;
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
        return null;
    }
}
