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

use Magento\Swatches\Block\LayeredNavigation\RenderLayered;
use Mirasvit\LayeredNavigation\Api\Service\FilterServiceInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Swatches\Helper\Data as SwatchesHelperData;
use Magento\Swatches\Helper\Media as SwatchesHelperMedia;
use Mirasvit\LayeredNavigation\Service\Config\ConfigTrait;

class Swatch extends RenderLayered
{
    use ConfigTrait;

    public $attributeCode;

    public $optionId;

    /**
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param AttributeFactory $layerAttribute
     * @param SwatchesHelperData $swatchHelper
     * @param SwatchesHelperMedia $mediaHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        SwatchesHelperData $swatchHelper,
        SwatchesHelperMedia $mediaHelper,
        FilterServiceInterface $filterService,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );
        $this->filterService = $filterService;
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        $template = 'Mirasvit_LayeredNavigation::layer/filter_swatch.phtml';

        return $template;
    }

    /**
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    public function getSwatchFilter()
    {
        return $this->filter;
    }

    public function getFilterUniqueValue($filter)
    {
        return $this->filterService->getFilterUniqueValue($filter);
    }

    public function getFilterRequestVar()
    {
        $filter = $this->getSwatchFilter();
        if (!is_object($filter)) {
            return '';
        }

        return $filter->getRequestVar();
    }

    public function isFilterCheckedSwatch($attributeCode, $option)
    {
        return $this->filterService->isFilterCheckedSwatch($attributeCode, $option);
    }

    public function buildUrl($attributeCode, $optionId)
    {
        $this->attributeCode = $attributeCode;
        $this->optionId = $optionId;

        return parent::buildUrl($attributeCode, $optionId);
    }
}

