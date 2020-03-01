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
 * @package   mirasvit/module-navigation
 * @version   1.0.59
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Brand\Api\Repository;

use Magento\Framework\DataObject;
use Mirasvit\Brand\Api\Data\BrandInterface;

interface BrandRepositoryInterface
{
    /**
     * @return BrandInterface[]|\Magento\Framework\Data\Collection
     */
    public function getCollection();

    /**
     * @return BrandInterface|DataObject
     */
    public function create();

    /**
     * @param int $id
     * @return BrandInterface|DataObject
     */
    public function get($id);
}
