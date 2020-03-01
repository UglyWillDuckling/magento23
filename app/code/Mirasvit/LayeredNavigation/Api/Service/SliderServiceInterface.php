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



namespace Mirasvit\LayeredNavigation\Api\Service;

interface SliderServiceInterface
{
    const MATCH_PREFIX = 'slider_match_prefix_';
    const SLIDER_DATA = 'sliderdata';
    const SLIDER_PARAM_TEMPLATE ='m_ln_'
    . SliderServiceInterface::SLIDER_REPLACE_VARIABLE
    . '_slider_from-m_ln_'
    . SliderServiceInterface::SLIDER_REPLACE_VARIABLE
    . '_slider_to';

    const SLIDER_REPLACE_VARIABLE ='[]';

    /**
     * @param string $requestVar
     * @return bool
     */
    public function isSliderEnabled($requestVar);

    /**
     * @param array $facetedData
     * @param string $requestVar
     * @param array $fromToData
     * @param string $url
     * @param string $class
     * @return mixed
     *
     * param $class use for debug
     */
    public function getSliderData($facetedData, $requestVar, $fromToData, $url, $class);

    /**
     * @param \Mirasvit\LayeredNavigation\Model\Layer\Filter\Price |
     * \Mirasvit\LayeredNavigation\Model\Layer\Filter\Decimal $filter
     * @param string $template
     * @return string
     */
    public function getSliderUrl($filter, $template);

    /**
     * @param \Mirasvit\LayeredNavigation\Model\Layer\Filter\Price |
     * \Mirasvit\LayeredNavigation\Model\Layer\Filter\Decimal $filter
     * @return string
     */
    public function getParamTemplate($filter);

    /**
     * @param string $attributeCode
     * @return string
     */
    public function getRegisterMatchedValue($attributeCode);
}