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



namespace Mirasvit\Sorting\Plugin\Frontend\Magento\Catalog\Model\ResourceModel\Product\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Db\Select;
use Mirasvit\Sorting\Api\Repository\CriterionRepositoryInterface;
use Mirasvit\Sorting\Api\Service\CriteriaApplierServiceInterface;
use Mirasvit\Sorting\Api\Service\SorterServiceInterface;

class SortCollection
{
    /**
     * @var SorterServiceInterface
     */
    private $sorterService;
    /**
     * @var CriterionRepositoryInterface
     */
    private $criterionRepository;
    /**
     * @var CriteriaApplierServiceInterface
     */
    private $criteriaApplierService;

    public function __construct(
        CriteriaApplierServiceInterface $criteriaApplierService,
        SorterServiceInterface $sorterService,
        CriterionRepositoryInterface $criterionRepository
    ) {

        $this->sorterService = $sorterService;
        $this->criterionRepository = $criterionRepository;
        $this->criteriaApplierService = $criteriaApplierService;
    }

    /**
     * Apply sort criteria to collection.
     *
     * @param Collection $subject
     * @param string     $attribute
     * @param string     $dir
     */
    public function beforeSetOrder(Collection $subject, $attribute, $dir = Select::SQL_DESC)
    {
        if ($this->canSort($subject, $attribute)) {
            $this->sorterService->sort($subject);
            $this->sortByCriterion($subject, $attribute);

            $subject->setFlag($attribute, true); // flag indicates that we have already applied a criterion
        }
    }

    /**
     * Sort collection by criterion.
     *
     * @param Collection $subject
     * @param string     $attribute
     */
    private function sortByCriterion(Collection $subject, $attribute)
    {
        if ($this->criterionRepository->has($attribute)) {
            $criterion = $this->criterionRepository->get($attribute);
            $this->criteriaApplierService->applyCriterion($criterion, $subject);
        }
    }

    /**
     * Determine whether the collection is not sorted yet, so we can process to sort it.
     *
     * @param Collection $collection
     * @param string     $attribute
     *
     * @return bool
     */
    private function canSort(Collection $collection, $attribute)
    {
        return !$collection->getFlag($attribute);
    }
}
