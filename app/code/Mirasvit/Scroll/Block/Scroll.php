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
 * @package   mirasvit/module-ajax-scroll
 * @version   1.0.7
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Scroll\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Scroll\Model\Config;

class Scroll extends Template
{
    const PRODUCT_LIST_SELECTOR_DEFAULT = '.products.products-grid, .products.product-list';

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config, Context $context, array $data = [])
    {
        $this->config = $config;

        parent::__construct($context, $data);
    }

    /**
     * Get selector of blocks to which apply the infinity scroll widget.
     *
     * @return string
     */
    public function getProductListSelector()
    {
        return $this->config->getProductListSelector() ?: self::PRODUCT_LIST_SELECTOR_DEFAULT;
    }

    /**
     * Get pager block.
     *
     * @return bool|\Magento\Theme\Block\Html\Pager
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPager()
    {
        return $this->getLayout()->getBlock('product_list_toolbar_pager');
    }

    /**
     * Get options for initializing scroll component.
     *
     * @return array
     */
    public function getJsConfig()
    {
        $pager  = $this->getPager();
        if (!$pager || !$pager->getCollection()) {
            return [];
        }

        $currentPageNum = (int) $pager->getCurrentPage();

        return [
            'mode' => $this->config->getMode(),
            'pageNum' => $currentPageNum,
            'initPageNum' => $currentPageNum,
            'prevPageNum' => $currentPageNum === 1 ? false : $currentPageNum - 1,
            'nextPageNum' => $currentPageNum === (int) $pager->getLastPageNum() ? false : $currentPageNum + 1,
            'lastPageNum' => $pager->getLastPageNum(),
            'loadPrevText' => __($this->config->getLoadPrevText() ?: 'Load Previous Page'),
            'loadNextText' => __($this->config->getLoadNextText() ?: 'Load More'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        return json_encode($this->getJsConfig());
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isEnabled();
    }
}
