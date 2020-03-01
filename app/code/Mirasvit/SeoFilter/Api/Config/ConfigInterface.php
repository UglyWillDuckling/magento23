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



namespace Mirasvit\SeoFilter\Api\Config;

interface ConfigInterface
{
    const FILTER_NAME_WITHOUT_SEPARATOR = 0;
    const FILTER_NAME_BOTTOM_DASH_SEPARATOR = 1;
    const FILTER_NAME_CAPITAL_LETTER_SEPARATOR = 2;

    const SEPARATOR_CONFIG = 'complex_seofilter_names_separator';

    /**
     * @param int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function isEnabled($store);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getComplexFilterNamesSeparator($store = null);
}