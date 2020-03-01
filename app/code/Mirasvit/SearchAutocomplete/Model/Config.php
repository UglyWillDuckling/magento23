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



namespace Mirasvit\SearchAutocomplete\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Search\Model\ResourceModel\Query\CollectionFactory as QueryCollectionFactory;
use Mirasvit\Core\Service\CompatibilityService;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const LAYOUT_1_COLUMN = '1column';
    const LAYOUT_2_COLUMNS = '2columns';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var QueryCollectionFactory
     */
    protected $queryCollectionFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        QueryCollectionFactory $queryCollectionFactory
    ) {
        $this->scopeConfig            = $scopeConfig;
        $this->queryCollectionFactory = $queryCollectionFactory;
    }

    /**
     * Is show product image
     * @return bool
     */
    public function isShowImage()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_image');
    }

    /**
     * Is show product rating
     * @return bool
     */
    public function isShowRating()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_rating');
    }

    /**
     * Is show product description
     * @return bool
     */
    public function isShowShortDescription()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_description');
    }

    /**
     * @return bool
     */
    public function isShowSku()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_sku');
    }

    /**
     * Is show add to cart button
     * @return bool
     */
    public function isShowCartButton()
    {
        return (bool)$this->scopeConfig->getValue('searchautocomplete/general/product/show_cart');
    }

    /**
     * @return bool
     */
    public function isOptimizeMobile()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/optimize_mobile');
    }

    /**
     * Product description length
     * @return int
     */
    public function getShortDescriptionLen()
    {
        return 100;
    }

    /**
     * Is show product price
     * @return bool
     */
    public function isShowPrice()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/product/show_price');
    }

    /**
     * Delay before start search (miliseconds)
     * @return int
     */
    public function getDelay()
    {
        return intval($this->scopeConfig->getValue('searchautocomplete/general/delay'));
    }

    /**
     * @return bool
     */
    public function isFastMode()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/fast_mode');
    }

    /**
     * Minimum number of chars to start search
     * @return int
     */
    public function getMinChars()
    {
        return intval($this->scopeConfig->getValue('searchautocomplete/general/min_chars'));
    }

    /**
     * Search indexes configuration
     * @return array
     */
    public function getIndicesConfig()
    {
        if (CompatibilityService::is22() || CompatibilityService::is23()) {
            $indexes = \Zend_Json::decode($this->scopeConfig->getValue('searchautocomplete/general/index'));
        } else {
            $indexes = @unserialize($this->scopeConfig->getValue('searchautocomplete/general/index'));
        }

        return $indexes;
    }

    /**
     * Additional (custom) css styles
     * @return string
     */
    public function getCssStyles()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/appearance/css');
    }

    /**
     * @param string $code
     * @return array
     */
    public function getIndexOptions($code)
    {
        if (isset($this->getIndicesConfig()[$code])) {
            return $this->getIndicesConfig()[$code];
        }

        return [];
    }

    /**
     * Get search index option value
     * @param string $code
     * @param string $option
     * @return bool|string
     */
    public function getIndexOptionValue($code, $option)
    {
        $options = $this->getIndexOptions($code);

        if (isset($options[$option])) {
            return $options[$option];
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isShowPopularSearches()
    {
        return $this->scopeConfig->getValue('searchautocomplete/popular/enabled', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getPopularLimit()
    {
        return intval($this->scopeConfig->getValue('searchautocomplete/popular/limit', ScopeInterface::SCOPE_STORE));
    }

    /**
     * @return array
     */
    public function getDefaultPopularSearches()
    {
        $result = $this->scopeConfig->getValue('searchautocomplete/popular/default', ScopeInterface::SCOPE_STORE);
        $result = array_filter(array_map('trim', explode(',', $result)));

        return $result;
    }

    /**
     * @return array
     */
    public function getIgnoredSearches()
    {
        $result = $this->scopeConfig->getValue('searchautocomplete/popular/ignored', ScopeInterface::SCOPE_STORE);
        $result = array_filter(array_map('strtolower', array_map('trim', explode(',', $result))));

        return $result;
    }

    /**
     * @return array
     */
    public function getPopularSearches()
    {
        $result = $this->getDefaultPopularSearches();

        if (!count($result)) {
            $ignored = $this->getIgnoredSearches();

            $collection = $this->queryCollectionFactory->create()
                ->setPopularQueryFilter()
                ->setPageSize(20);

            /** @var \Magento\Search\Model\Query $query */
            foreach ($collection as $query) {
                $text = $query->getQueryText();
                if (!$text) {
                    //old magento 2
                    $text = $query->getName();
                }
                $isIgnored = false;
                foreach ($ignored as $word) {
                    if (strpos(strtolower($text), $word) !== false) {
                        $isIgnored = true;
                        break;
                    }
                }

                if (!$isIgnored) {
                    $result[] = mb_strtolower($text);
                }
            }
        }

        $result = array_slice(array_unique($result), 0, $this->getPopularLimit());
        $result = array_map('ucfirst', $result);

        return $result;
    }

    /**
     * @return bool
     */
    public function isTypeAheadEnabled()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/type_ahead');
    }

    /**
     * @retun bool
     */
    public function isOutOfStockAllowed()
    {
        return $this->scopeConfig->getValue('cataloginventory/options/show_out_of_stock');
    }

    /**
     * @retun bool
     */
    public function getAutocompleteLayout()
    {
        return $this->scopeConfig->getValue('searchautocomplete/general/appearance/layout');
    }
}
