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



namespace Mirasvit\SeoFilter\Api\Service;

interface FriendlyUrlServiceInterface
{
    /**
     * Create friendly urls for Layered Navigation (add and remove filters)
     *
     * @param string $attributeCode
     * @param int $attributeId
     * @param int $optionId
     * @param bool $remove
     * @return string
     */
    public function getFriendlyUrl($attributeCode, $attributeId, $optionId, $remove);

    /**
     * @param string $filterUrlString
     * @return string
     */
    public function getPreparedCurrentCategoryUrl($filterUrlString);
}