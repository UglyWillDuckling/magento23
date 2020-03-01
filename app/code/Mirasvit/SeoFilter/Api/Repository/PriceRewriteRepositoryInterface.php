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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Api\Repository;

use Magento\Framework\DataObject;
use Mirasvit\SeoFilter\Api\Data\PriceRewriteInterface;

interface PriceRewriteRepositoryInterface
{
    /**
     * @return PriceRewriteInterface[]|\Mirasvit\SeoFilter\Model\ResourceModel\PriceRewrite\Collection
     */
    public function getCollection();

    /**
     * @return PriceRewriteInterface
     */
    public function create();

    /**
     * @param int $id
     * @return PriceRewriteInterface|DataObject|false
     */
    public function get($id);

    /**
     * @param PriceRewriteInterface $model
     * @return PriceRewriteInterface
     */
    public function save(PriceRewriteInterface $model);

    /**
     * @param PriceRewriteInterface $model
     * @return bool
     */
    public function delete(PriceRewriteInterface $model);
}