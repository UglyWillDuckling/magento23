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



namespace Mirasvit\Sorting\Plugin\Frontend\Magento\Catalog\Model\Product\ProductList\Toolbar;


use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Mirasvit\Sorting\Api\Service\CriteriaManagementServiceInterface;

class InitDefaultDirection
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
     * Init default direction for order, if direction is not set.
     *
     * @param Toolbar $subject
     * @param bool|string $result
     *
     * @return bool|string
     */
    public function afterGetDirection(Toolbar $subject, $result)
    {
        if (!$result) {
            $order = $subject->getOrder();
            if (!$order) {
                $order = $this->criteriaManagement->getDefaultCriterion();
            }

            if ($order) {
                $result = $this->criteriaManagement->getDirection($order);
            }
        }

        return $result;
    }
}
