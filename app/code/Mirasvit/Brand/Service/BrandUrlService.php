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

use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Api\Repository\BrandRepositoryInterface;
use Mirasvit\Brand\Api\Service\BrandUrlServiceInterface;
use Magento\Framework\Filter\FilterManager;

class BrandUrlService implements BrandUrlServiceInterface
{
    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var FilterManager
     */
    private $filter;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        BrandRepositoryInterface $brandRepository,
        ConfigInterface $config,
        FilterManager $filter
    ) {
        $this->brandRepository = $brandRepository;
        $this->config = $config;
        $this->filter = $filter;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseBrandUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl() . $this->getBaseRoute(true);
    }

    /**
     * @param bool $withSuffix - add Brand URL suffix or not
     *
     * @return string
     */
    private function getBaseRoute($withSuffix = false)
    {
        $baseRoute = $this->config->getGeneralConfig()->getAllBrandUrl();
        if ($withSuffix) {
            $baseRoute .= $this->config->getGeneralConfig()->getUrlSuffix();
        }

        return $baseRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandUrl($urlKey, $brandTitle)
    {
        if (!$urlKey) {
            $urlKey = $this->filter->translitUrl($brandTitle);
        }

        $formatBrandUrl = $this->config->getGeneralConfig()->getFormatBrandUrl();
        if ($formatBrandUrl === self::SHORT_URL) {
            $brandUrl = $urlKey;
        } else {
            $brandUrl = $this->getBaseRoute() . '/' . $urlKey;
        }

        return $brandUrl . $this->config->getGeneralConfig()->getUrlSuffix();
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathInfo)
    {
        $identifier = trim($pathInfo, '/');
        $parts = explode('/', $identifier);

        $brandUrlKeys = $this->getAvailableBrandUrlKeys();

        if ($parts[0] !== $this->getBaseRoute() && !in_array($parts[0], $brandUrlKeys, true)) {
            return false;
        }

        $formatBrandUrl = $this->config->getGeneralConfig()->getFormatBrandUrl();

        if (count($parts) === 1 && ($formatBrandUrl === self::SHORT_URL || $parts[0] === $this->getBaseRoute(true))) {
            $urlKey = $parts[0];
        } elseif ($formatBrandUrl === self::LONG_URL && isset($parts[1]) && count($parts) > 1) {
            $urlKey = implode('/', $parts);
        } else {
            return false;
        }

        if ($urlKey === $this->getBaseRoute(true)) {
            return new DataObject([
                'module_name' => 'brand',
                'controller_name' => 'brand',
                'action_name' => 'index',
                'route_name' => $urlKey,
                'params' => [],
            ]);
        } elseif (in_array($urlKey, $brandUrlKeys, true)) {
            $optionId = array_search($urlKey, $brandUrlKeys, true);

            return new DataObject([
                'module_name' => 'brand',
                'controller_name' => 'brand',
                'action_name' => 'view',
                'route_name' => $brandUrlKeys[$optionId],
                'params' => [BrandPageInterface::ATTRIBUTE_OPTION_ID => $optionId],
            ]);
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function getAvailableBrandUrlKeys()
    {
        $urlKeys = [$this->getBaseRoute(true)];
        $brandPages = $this->brandRepository->getCollection();
        foreach ($brandPages as $brand) {
            $urlKeys[$brand->getId()] = $brand->getUrl();
        }

        return $urlKeys;
    }
}
