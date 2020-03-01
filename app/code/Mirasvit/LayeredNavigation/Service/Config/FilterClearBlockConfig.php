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



namespace Mirasvit\LayeredNavigation\Service\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\LayeredNavigation\Api\Config\FilterClearBlockConfigInterface;
use Magento\Store\Model\ScopeInterface;

class FilterClearBlockConfig implements FilterClearBlockConfigInterface
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function isHorizontalFiltersClearPanelEnabled($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/filter_clear_block/filter_clear_block_position',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isFilterClearBlockInOneRow($store = null)
    {
        return $this->scopeConfig->getValue(
            'layerednavigation/filter_clear_block/filter_clear_block_representation_attributes',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
