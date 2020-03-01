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

use Magento\Framework\DataObject;

interface BrandUrlServiceInterface
{
    const LONG_URL = 0;
    const SHORT_URL = 1;

    /**
     * @return string
     */
    public function getBaseBrandUrl();

    /**
     * @param string $urlKey
     * @param string $brandTitle
     * @return string
     */
    public function getBrandUrl($urlKey, $brandTitle);

    /**
     * @param string $pathInfo
     * @return bool|DataObject
     */
    public function match($pathInfo);

}