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



namespace Mirasvit\LayeredNavigation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\LayeredNavigation\Api\Service\CssServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\CssCreatorServiceInterface;

class AdditionalCss extends Template
{
    public function __construct(
        Context $context,
        CssServiceInterface $cssService,
        CssCreatorServiceInterface $cssCreatorService,
        array $data = []
    ) {
        $this->cssService = $cssService;
        $this->cssCreatorService = $cssCreatorService;
        $this->mediaUrl = $context->getStoreManager()->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $this->storeId = $context->getStoreManager()->getStore()->getId();
        $this->storeCode = $context->getStoreManager()->getStore()->getCode();
        $this->storeBaseUrl = $context->getStoreManager()->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCssPath()
    {
        $cssPath = $this->storeBaseUrl . $this->cssService->getCssPath($this->storeCode, $this->storeId, true);
        if (strpos($this->mediaUrl, '/pub/') === false) { //check if "pub" should be added
            $cssPath = str_replace('/pub/', '/', $cssPath);
        }

        return $cssPath;
    }

    public function isCssConfigured()
    {
        $result = false;
        $headers = @get_headers($this->getCssPath());

        if ($headers) {
            $result = stripos($headers[0], '200 OK') !== false;
        }

        if (!$result) {
            $this->cssService->generateStoreCss($this->storeId);
            $result = true;
        }

        return $result;
    }
}
