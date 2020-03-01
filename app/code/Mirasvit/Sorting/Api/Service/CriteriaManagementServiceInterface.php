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


interface CriteriaManagementServiceInterface
{
    /**
     * Get default criterion code.
     *
     * @return bool
     */
    public function getDefaultCriterion();

    /**
     * Get criterion sort direction.
     *
     * @param string $criterionCode
     *
     * @return bool
     */
    public function getDirection($criterionCode);

    /**
     * Check whether the given criteria is active or not.
     *
     * @param string $criterionCode
     *
     * @return bool
     */
    public function isActive($criterionCode);

    /**
     * Check whether the attribute is default Magento or not.
     *
     * @param string $criterionCode
     *
     * @return bool
     */
    public function isDefault($criterionCode);

    /**
     * Sort criteria.
     *
     * @param array $criteria
     *
     * @return array
     */
    public function sortCriteria(array $criteria = []);

    /**
     * Retrieve default Magento Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getDefaultCriteria();
}
