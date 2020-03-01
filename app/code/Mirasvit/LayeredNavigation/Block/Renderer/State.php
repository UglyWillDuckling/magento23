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



namespace Mirasvit\LayeredNavigation\Block\Renderer;

use Magento\LayeredNavigation\Block\Navigation\State as NavigationState;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Api\Config\HorizontalFiltersConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Api\Config\FilterClearBlockConfigInterface;

class State extends NavigationState
{
    use ConfigTrait;

    /**
     * @var string
     */
    protected $_template = 'layer/state.phtml';

    /**
     * State constructor.
     * @param HorizontalFiltersConfigInterface $horizontalFiltersConfig
     * @param Context $context
     * @param LayerResolver $layerResolver
     * @param FilterClearBlockConfigInterface $filterClearBlockConfig
     * @param array $data
     */
    public function __construct(
        HorizontalFiltersConfigInterface $horizontalFiltersConfig,
        Context $context,
        LayerResolver $layerResolver,
        FilterClearBlockConfigInterface $filterClearBlockConfig,
        array $data = []
    ) {
        $this->storeId = $context->getStoreManager()->getStore()->getStoreId();
        $this->horizontalFiltersConfig = $horizontalFiltersConfig;
        $this->filterClearBlockConfig = $filterClearBlockConfig;
        parent::__construct($context, $layerResolver, $data);
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $nameInLayout = $this->getNameInLayout();
        if (($nameInLayout == HorizontalFiltersConfigInterface::STATE_HORIZONTAL_BLOCK_NAME)
            && !$this->filterClearBlockConfig->isHorizontalFiltersClearPanelEnabled($this->storeId)) {
                return [];
        }

        if (($nameInLayout == HorizontalFiltersConfigInterface::STATE_BLOCK_NAME
             || $nameInLayout == HorizontalFiltersConfigInterface::STATE_SEARCH_BLOCK_NAME)
            && $this->filterClearBlockConfig->isHorizontalFiltersClearPanelEnabled($this->storeId)) {
            return [];
        }

        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        }
        return $filters;
    }

    public function isHorizontalFilter()
    {
        $nameInLayout = $this->getNameInLayout();
        if ($nameInLayout == HorizontalFiltersConfigInterface::STATE_HORIZONTAL_BLOCK_NAME) {
            return true;
        }

        return false;
    }


    /**
     * Prepare not multiselect price
     *
     * @param string $price
     * @return string
     */
    public function getPreparedValue($requestVar, $value)
    {
        if ($requestVar != 'price' || $this->isMultiselectEnabled()) {
            return $value;
        }

        return str_replace(',', '-', $value);
    }
}
