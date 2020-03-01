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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.1.94
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchAutocomplete\Index\Magento\Catalog;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Review\Block\Product\ReviewRenderer;
use Magento\Review\Model\ReviewFactory;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Theme\Model\View\Design;
use Mirasvit\SearchAutocomplete\Index\AbstractIndex;
use Mirasvit\SearchAutocomplete\Model\Config;
use Magento\CatalogInventory\Helper\Stock as StockHelper;

use Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory as ProductProvider;
use Magento\Store\Model\StoreManagerInterface;

use Magento\Framework\UrlInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends AbstractIndex
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var ReviewRenderer
     */
    protected $reviewRenderer;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var CatalogHelper
     */
    protected $catalogHelper;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var design
     */
    protected $design;

    /**
     * @var TaxConfig
     */
    private $taxConfig;

    /**
     * @var StockRegistryInterface
     */
    private $stock;

    /**
     * @var StockHelper
     */
    private $stockHelper;

    private $productProvider;

    private $productIds = [];

    private $storeManager;

    public function __construct(
        TaxConfig $taxConfig,
        Config $config,
        ReviewFactory $reviewFactory,
        ReviewRenderer $reviewRenderer,
        ImageHelper $imageHelper,
        CatalogHelper $catalogHelper,
        PricingHelper $pricingHelper,
        RequestInterface $request,
        Design $design,
        StockRegistryInterface $stock,
        StockHelper $stockHelper,
        ProductProvider $productProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->config           = $config;
        $this->reviewFactory    = $reviewFactory;
        $this->reviewRenderer   = $reviewRenderer;
        $this->imageHelper      = $imageHelper;
        $this->catalogHelper    = $catalogHelper;
        $this->pricingHelper    = $pricingHelper;
        $this->request          = $request;
        $this->taxConfig        = $taxConfig;
        $this->design           = $design;
        $this->stock            = $stock;
        $this->stockHelper      = $stockHelper;
        $this->productProvider  = $productProvider;
        $this->storeManager     = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items      = [];
        $categoryId = intval($this->request->getParam('cat'));
        $storeId = intval($this->request->getParam('store_id'));

        $collection = $this->getCollection();

        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('short_description')
            ->addAttributeToSelect('description');

        $this->collection->getSelect()->order('score desc');

        if (!$this->config->isOutOfStockAllowed()) {
            $this->stockHelper->addInStockFilterToCollection($this->collection);
        }

        if ($categoryId) {
            $om       = ObjectManager::getInstance();
            $category = $om->create('Magento\Catalog\Model\Category')->load($categoryId);
            $collection->addCategoryFilter($category);
        }

        if ($this->config->isShowRating()) {
            $this->reviewFactory->create()->appendSummary($collection);
        }
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $map = $this->mapProduct($product, $storeId);
            if ($map) {
                $items[] = $map;
            }
        }

        return $items;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int                            $storeId
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function mapProduct($product, $storeId = 1)
    {
        $item = [
            'name'        => $product->getName(),
            'url'         => $product->getProductUrl(),
            'sku'         => null,
            'description' => null,
            'image'       => null,
            'price'       => null,
            'rating'      => null,
            'optimize'    => false,
        ];

        if ($this->config->isShowShortDescription()) {
            $item['description'] = html_entity_decode(
                strip_tags($product->getDataUsingMethod('description'))
            );
        }

        if ($this->config->isShowSku()) {
            $item['sku'] = html_entity_decode(
                strip_tags($product->getDataUsingMethod('sku'))
            );
        }

        if ($this->config->isShowImage()) {
            $image = false;

            $image = $this->imageHelper->init($product, 'product_page_image_small')
                ->setImageFile($product->getFile())
                ->resize(65 * 2, 80 * 2)
                ->getUrl();

            if (strpos($image, '/.') !== false) {
                // wrong url was generated (image doesn't present in file system)
                $image = false;
            }

            if (!$image) {
                $this->design->setDesignTheme('Magento/backend', 'adminhtml');
                $image = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
            }

            $item['image'] = $image;
        }

        if ($this->config->isShowPrice()) {
            $includingTax = true;
            if ($this->taxConfig->getPriceDisplayType() === TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX) {
                $includingTax = false;
            }

            $price = $product->getMinimalPrice();

            if ($price == 0 && $product->getFinalPrice() > 0) {
                $price = $product->getFinalPrice();
            } else {
                $price = $product->getMinPrice();
            }

            $item['price'] = $this->catalogHelper->getTaxPrice($product, $price, $includingTax);
            $item['price'] = $this->pricingHelper->currency($item['price'], false, false);
        }

        if ($this->config->isShowRating() && $product->getRatingSummary()) {
            try {
                /** @var \Magento\Store\Model\App\Emulation $emulation */
                $emulation = ObjectManager::getInstance()->get('Magento\Store\Model\App\Emulation');
                $emulation->startEnvironmentEmulation($storeId, 'frontend', true);

                /** @var \Magento\Framework\App\State $state */
                $state = ObjectManager::getInstance()->get('Magento\Framework\App\State');

                $state->emulateAreaCode('frontend', function (&$item, $product) {
                    $item['rating'] = $this->reviewRenderer
                        ->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
                }, [&$item, $product]);

                $emulation->stopEnvironmentEmulation();
            } catch (\Exception $e) {
            }
        }

        $om = ObjectManager::getInstance();

        /** @var \Magento\Catalog\Block\Product\ListProduct $productBlock */
        $productBlock = $om->create('Magento\Catalog\Block\Product\ListProduct');
        $item['cart'] = [
            'visible' => $this->config->isShowCartButton(),
            'label'   => __('Add to Cart')->render(),
            'params'  => $productBlock->getAddToCartPostParams($product),
        ];

        return $item;
    }

    /**
     * @param array                                         $documents
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @return mixed
     */
    public function map($documents, $dimensions)
    {
        if (!$this->config->isFastMode()) {
            return $documents;
        }

        $dimension = current($dimensions);
        $storeId   = $dimension->getValue();

        $om = ObjectManager::getInstance();

        $this->productIds = array_keys($documents);

        if (count($this->productIds) === 0) {
            return $documents;
        }

        $collection = $om->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection->setStoreId($storeId)
            ->addStoreFilter($storeId)
            ->addAttributeToSelect('description')
            ->addFinalPrice()
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date');
        $collection->addAttributeToFilter('entity_id', ['in' => $this->productIds]);
        $collection->load();

        $products = $this->productProvider->create()->getCollection($storeId);
        $productBlock = $om->create('Magento\Catalog\Block\Product\ListProduct');

        foreach ($collection as $product) {
            $entityId = $product->getId();
            $item = $products[$entityId];

            $image = null;
            if ($this->config->isShowImage()) {
                $this->design->setDesignTheme('Magento/backend', 'adminhtml');
                $image = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');

                if (!empty($item['images'])) {
                    $image = $item['images']->getThumbnail();
                }
            }

            $price = null;
            if ($this->config->isShowPrice()) {
                $includingTax = true;
                if ($this->taxConfig->getPriceDisplayType() === TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX) {
                    $includingTax = false;
                }

                $price = $product->getMinimalPrice();

                if ($price == 0 && $product->getFinalPrice() > 0) {
                    $price = $product->getFinalPrice();
                } else {
                    $price = $product->getMinPrice();
                }

                $price = $this->catalogHelper->getTaxPrice($product, $price, $includingTax);
                $price = $this->pricingHelper->currency($price, false, false);
            }

            $cart = [
                'visible' => $this->config->isShowCartButton(),
                'label'   => __('Add to Cart')->render(),
                'params'  => $productBlock->getAddToCartPostParams($product),
            ];

            $rating = null;
            if ($this->config->isShowRating() && $product->getRatingSummary()) {
                try {
                    /** @var \Magento\Store\Model\App\Emulation $emulation */
                    $emulation = ObjectManager::getInstance()->get('Magento\Store\Model\App\Emulation');
                    $emulation->startEnvironmentEmulation($storeId, 'frontend', true);

                    /** @var \Magento\Framework\App\State $state */
                    $state = ObjectManager::getInstance()->get('Magento\Framework\App\State');

                    $state->emulateAreaCode('frontend', function (&$rating, $product) {
                        $rating = $this->reviewRenderer
                            ->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
                    }, [&$rating, $product]);

                    $emulation->stopEnvironmentEmulation();
                } catch (\Exception $e) {
                }
            }

            $map = [
                'name'        => $item['name'],
                'url'         => $this->getUrl($item['url'], $storeId),
                'sku'         => ($this->config->isShowSku())? $item['sku'] : null,
                'description' => ($this->config->isShowShortDescription())?
                    html_entity_decode(strip_tags($product->getDataUsingMethod('description'))) : null,
                'image'       => $image,
                'price'       => $price,
                'rating'      => $rating,
                'cart'        => $cart,
                'optimize'    => false,
            ];

            $documents[$entityId]['autocomplete'] = $map;
        }

        return $documents;
    }

    public function getProductIds()
    {
        if (!$this->config->isFastMode()) {
            return false;
        }

        return $this->productIds;
    }

    private function getUrl($url, $storeId, $type = UrlInterface::URL_TYPE_LINK)
    {
        return $this->getStoreBaseUrl($storeId, $type) . ltrim($url, '/');
    }

    private function getStoreBaseUrl($storeId, $type = UrlInterface::URL_TYPE_LINK)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore($storeId);
        $isSecure = $store->isUrlSecure();
        return rtrim($store->getBaseUrl($type, $isSecure), '/') . '/';
    }
}
