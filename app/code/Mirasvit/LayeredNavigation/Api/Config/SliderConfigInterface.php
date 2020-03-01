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



namespace Mirasvit\LayeredNavigation\Api\Config;

interface SliderConfigInterface
{
    const SLIDER_FILTER_CONFIG_SEPARATOR = '|';

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getSliderOptions($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getSliderHandleColor($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getSliderHandleBorderColor($store = null);


    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getSliderConnectColor($store = null);

    /**
     * @param null|int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getSliderTextColor($store = null);
}