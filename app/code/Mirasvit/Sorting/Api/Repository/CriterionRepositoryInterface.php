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



namespace Mirasvit\Sorting\Api\Repository;

use Mirasvit\Sorting\Api\Data\CriterionInterface;

interface CriterionRepositoryInterface
{
    /**
     * @param string $code - criterion code
     *
     * @return CriterionInterface
     */
    public function get($code);

    /**
     * Check whether the criterion with the given code exists or not.
     *
     * @param string $code
     *
     * @return bool
     */
    public function has($code);

    /**
     * @return CriterionInterface[]
     */
    public function getList();
}
