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



namespace Mirasvit\Sorting\Api\Data;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

interface CriterionInterface
{
    const JOIN_LEFT = 'left';

    /**
     * Criterion code.
     *
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getTableName();

    /**
     * @return string
     */
    public function getJoinType();

    /**
     * Get select field name.
     *
     * @return string
     */
    public function getField();

    /**
     * Get field name used to join criterion table with main table.
     *
     * @return string
     */
    public function getPk();

    /**
     * Condition for joining criterion table.
     *
     * @return array|string|null
     */
    public function getCondition();
}
