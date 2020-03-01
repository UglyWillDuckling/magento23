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

interface FilterLabelServiceInterface
{
    /**
     * @param string $attributeCode
     * @param int $optionId
     * @param string $itemValue
     * @return string
     */
    public function getLabel($attributeCode, $optionId, $itemValue);

    /**
     * @param string $label
     * @return string
     */
    public function getLabelWithSeparator($label);
}