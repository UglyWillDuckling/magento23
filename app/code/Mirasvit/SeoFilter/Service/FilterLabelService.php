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


namespace Mirasvit\SeoFilter\Service;

use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Magento\Framework\Filter\FilterManager;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;
use Mirasvit\SeoFilter\Api\Service\FilterLabelServiceInterface;

class FilterLabelService implements FilterLabelServiceInterface
{

    /**
     * @param FilterManager $filter
     * @param Config $config
     */
    public function __construct(
        FilterManager $filter,
        Config $config
    ) {
        $this->filter = $filter;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel($attributeCode, $optionId, $itemValue)
    {
        $label = strtolower($this->filter->translitUrl($itemValue));
        if (empty($label)) {
            $label = $optionId;
        }

        if ($attributeCode == RewriteInterface::PRICE) {
            $label = RewriteInterface::PRICE . $label;
        } else {
            $label = $this->getLabelWithSeparator($label);
        }

        //todo maybe need create unique label

        return $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelWithSeparator($label)
    {
        $label = str_replace('__', '_', $label);
        $separator = $this->config->getComplexFilterNamesSeparator();
        switch ($separator) {
            case Config::FILTER_NAME_WITHOUT_SEPARATOR:
                $label = str_replace(RewriteInterface::FILTER_SEPARATOR, '', $label);
                break;
            case Config::FILTER_NAME_BOTTOM_DASH_SEPARATOR:
                $label = str_replace(RewriteInterface::FILTER_SEPARATOR, '_', $label);
                break;
            case Config::FILTER_NAME_CAPITAL_LETTER_SEPARATOR:
                $labelExploded = explode(RewriteInterface::FILTER_SEPARATOR, $label);
                $labelExploded = array_map('ucfirst', $labelExploded);
                $label = implode('', $labelExploded);
                $label = lcfirst($label);
                break;
        }

        return $label;
    }
}