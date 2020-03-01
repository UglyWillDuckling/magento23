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



namespace Mirasvit\Sorting\Api\Service;


use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Mirasvit\Sorting\Api\Data\CriterionInterface;

interface CriteriaApplierServiceInterface
{
    /**
     * Apply sorting criteria to collection.
     *
     * @param CriterionInterface $criterion
     * @param AbstractCollection $collection
     */
    public function applyCriterion(CriterionInterface $criterion, AbstractCollection $collection);
}
