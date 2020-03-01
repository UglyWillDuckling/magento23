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



namespace Mirasvit\Brand\Api\Service;

interface BrandLogoServiceInterface
{
    const BRAND_TITLE_PATTERN = '{title}';
    const BRAND_SMALL_IMAGE_PATTERN = '{small_image}';
    const BRAND_IMAGE_PATTERN = '{image}';
    const BRAND_DESCRIPTION_PATTERN = '{description}';
    const BRAND_SHORT_DESCRIPTION_PATTERN = '{short_description}';

    /**
     * @return string
     */
    public function getLogoHtml();

    /**
     * @return string
     */
    public function getLogoImageUrl();

    /**
     * @return string
     */
    public function getBrandTitle();

    /**
     * @param int $optionId
     * @return void
     */
    public function setBrandDataByOptionId($optionId);
}