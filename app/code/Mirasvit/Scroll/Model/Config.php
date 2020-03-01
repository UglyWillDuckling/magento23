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



namespace Mirasvit\Scroll\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Mirasvit\Scroll\Model\Config\Source\Mode;

class Config
{
    const XML_PATH_SCROLL_MODE = 'mst_scroll/general/mode';
    const XML_PATH_PREV_TEXT = 'mst_scroll/general/prev_text';
    const XML_PATH_NEXT_TEXT = 'mst_scroll/general/next_text';
    const XML_PATH_PRODUCT_LIST_SELECTOR = 'mst_scroll/general/product_list_selector';
    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }
    /**
     * Get selector of blocks to which apply the infinity scroll widget.
     *
     * @return string
     */
    public function getProductListSelector()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_PRODUCT_LIST_SELECTOR, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_SCROLL_MODE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getLoadPrevText()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_PREV_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getLoadNextText()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_NEXT_TEXT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return in_array($this->getMode(), [Mode::MODE_BUTTON, MODE::MODE_INFINITE], true);
    }
}
