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



namespace Mirasvit\Sorting\Plugin\Frontend\Magento\Catalog\Block\Product\ProductList\Toolbar;


use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Mirasvit\Sorting\Api\Service\CriteriaManagementServiceInterface;

class InitDefaultOrder
{
    /**
     * @var CriteriaManagementServiceInterface
     */
    private $criteriaManagement;

    public function __construct(CriteriaManagementServiceInterface $criteriaManagement)
    {
        $this->criteriaManagement = $criteriaManagement;
    }

    /**
     * Initialize default sort order and direction.
     *
     * @param Toolbar $subject
     * @param \Magento\Framework\Data\Collection $collection
     */
    public function beforeSetCollection(Toolbar $subject, $collection)
    {
        if ($criterion = $this->criteriaManagement->getDefaultCriterion()) {
            $subject->setDefaultOrder($criterion);

            if ($dir = $this->criteriaManagement->getDirection($criterion)) {
                $subject->setDefaultDirection($dir);
            }
        }
    }
}
