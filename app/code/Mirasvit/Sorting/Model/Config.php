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
 * @package   mirasvit/module-sorting
 * @version   1.0.9
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Sorting\Model;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return null|array
     */
    public function getCriteriaConfig()
    {
        try {
            $data = \Zend_Json::decode(
                $this->scopeConfig->getValue('mst_sorting/general/criteria', ScopeInterface::SCOPE_STORE)
            );
        } catch (\Exception $e) {
            // Magento 2.1 and lower uses serialization
            $data = @unserialize(
                $this->scopeConfig->getValue('mst_sorting/general/criteria', ScopeInterface::SCOPE_STORE)
            );
        }

        if (!$data) {
            $data = [];
        }

        return $data;
    }

    /**
     * Whether the option "Show configurable products first" enabled or not.
     *
     * @return bool
     */
    public function isShowConfigurableFirst()
    {
        return (bool) $this->scopeConfig->getValue('mst_sorting/general/configurable', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Whether the option "Push out of stock to the end" enabled or not.
     *
     * @return bool
     */
    public function isSortByOutOfStock()
    {
        return (bool) $this->scopeConfig->getValue('mst_sorting/general/out_of_stock', ScopeInterface::SCOPE_STORE);
    }
}
