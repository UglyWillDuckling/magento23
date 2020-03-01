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
use Mirasvit\Brand\Api\Data\BrandPageInterface;

interface BrandPageRepositoryInterface
{
    /**
     * @return BrandPageInterface[]|\Mirasvit\Brand\Model\ResourceModel\BrandPage\Collection
     */
    public function getCollection();

    /**
     * @return BrandPageInterface
     */
    public function create();

    /**
     * @param int $id
     * @return BrandPageInterface|DataObject|false
     */
    public function get($id);

    /**
     * @param int $optionId
     * @param int $attributeId
     *
     * @return DataObject|BrandPageInterface
     */
    public function getByOptionId($optionId, $attributeId);

    /**
     * @param BrandPageInterface $keyword
     * @return BrandPageInterface
     */
    public function save(BrandPageInterface $keyword);

    /**
     * @param BrandPageInterface $keyword
     * @return bool
     */
    public function delete(BrandPageInterface $keyword);
}