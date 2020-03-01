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

interface RewriteServiceInterface
{
    /**
     * Return current rewrite value
     *
     * @param string $attributeCode
     * @param int $attributeId
     * @param int $optionId
     * @return string
     */
    public function getRewriteForFilterOption($attributeCode, $attributeId, $optionId);

    /**
     * Return active filters
     *
     * @return array
     */
    public function getActiveFilters();

    /**
     * Generate new rewrite and return generated rewrite value
     *
     * @param string $attributeCode
     * @param int $attributeId
     * @param int $optionId
     * @return string
     */
    public function generateNewRewrite($attributeCode, $attributeId, $optionId);
}