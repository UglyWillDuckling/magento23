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

interface CssServiceInterface
{
    const PUB = 'pub';
    const CSS_FIRST_PART_NAME = 'settings_';
    const CSS_PATH = '/media/mirasvit_ln/';

    /**
     * @param int $websiteId
     * @param int $storeId
     * @return void
     */
    public function generateCss($websiteId, $storeId);

    /**
     * @param int $websiteId
     * @return void
     */
    public function generateWebsiteCss($websiteId);

    /**
     * @param int $storeId
     * @return void|bool
     */
    public function generateStoreCss($storeId);

    /**
     * @param string $storeCode
     * @param int $storeId
     * @param bool $front
     * @return void
     */
    public function getCssPath($storeCode = false, $storeId = false, $front = false);

}