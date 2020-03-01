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

use Magento\Framework\View\Result\Page;

interface BrandPageMetaServiceInterface
{
    /**
     * @param bool $isIndexPage
     * @return string
     */
    public function getTitle($isIndexPage);

    /**
     * @param bool $isIndexPage
     * @return string
     */
    public function getMetaTitle($isIndexPage);

    /**
     * @param bool $isIndexPage
     * @return string
     */
    public function getKeyword($isIndexPage);

    /**
     * @param bool $isIndexPage
     * @return string
     */
    public function getMetaDescription($isIndexPage);

    /**
     * @param bool $isIndexPage
     * @return string
     */
    public function getCanonical($isIndexPage);

    /**
     * @param bool $isIndexPage
     * @return string
     */
    public function getRobots($isIndexPage);

    /**
     * @return array
     */
    public function getDefaultData();

    /**
     * @param array $brandPageData
     * @return null|int
     */
    public function getBrandPageId($brandPageData);

    /**
     * @param Page $page
     * @param bool $isIndexPage
     * @return Page
     */
    public function apply(Page $page, $isIndexPage = false);
}