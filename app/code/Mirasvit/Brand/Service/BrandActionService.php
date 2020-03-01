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



namespace Mirasvit\Brand\Service;

use Magento\Framework\App\Request\Http;
use Mirasvit\Brand\Api\Service\BrandActionServiceInterface;

class BrandActionService implements BrandActionServiceInterface
{
    /**
     * @param Http $request
     */
    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullActionName()
    {
        return $this->request->getFullActionName();

    }

    /**
     * {@inheritdoc}
     */
    public function isBrandViewPage()
    {
        return $this->getFullActionName() === self::BRAND_VIEW_ACTION;
    }

    /**
     * {@inheritdoc}
     */
    public function isBrandIndexPage()
    {
        return $this->getFullActionName() === self::BRAND_INDEX_ACTION;
    }

    /**
     * {@inheritdoc}
     */
    public function isBrandPage()
    {
        return $this->isBrandIndexPage() || $this->isBrandViewPage();
    }
}
