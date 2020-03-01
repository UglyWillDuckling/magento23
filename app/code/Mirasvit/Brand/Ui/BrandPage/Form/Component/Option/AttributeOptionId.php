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



namespace Mirasvit\Brand\Ui\BrandPage\Form\Component\Option;

use Mirasvit\Brand\Api\Repository\BrandPageRepositoryInterface;
use Mirasvit\Brand\Api\Config\ConfigInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Element\Select;

class AttributeOptionId extends Select
{
    public function __construct(
        ContextInterface $context,
        BrandPageRepositoryInterface $brandPageRepository,
        ConfigInterface $config,
        AttributeOptionIdSource $optionIdSource,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $options,
            $components,
            $data
        );
        $this->brandPageRepository = $brandPageRepository;
        $this->config = $config;
        $this->init($optionIdSource);
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if ($brandPageId = $this->getBrandPageId()) {
            $this->getPreparedConfig($brandPageId, $config);
        }
        $this->setData('config', $config);
        parent::prepare();
    }

    /**
     * Init options
     *
     * @param OptionIdSource $optionIdSource
     * @return void
     */
    private function init(AttributeOptionIdSource $optionIdSource)
    {
        $brandPageId = $this->getBrandPageId();

        if ($brandPageId) {
            $attributeCode = $this->getBrandAttributeCode($brandPageId);
            $optionIdSource->setCurrentOptionId($this->getBrand($brandPageId)->getAttributeOptionId());
        } else {
            $attributeCode = $this->config->getGeneralConfig()->getBrandAttribute();
        }
        $this->options = $optionIdSource->setAttributeCode($attributeCode)->toOptionArray();
    }

    /**
     * @return string
     */
    private function getBrandPageId()
    {
        $context = $this->getContext();
        return $context->getRequestParam(
            $context->getDataProvider()->getRequestFieldName(),
            null
        );
    }

    /**
     * @return string
     */
    private function getBrandAttributeCode($brandPageId)
    {
        return $this->getBrand($brandPageId)->getAttributeCode();
    }

    /**
     * @return string
     */
    private function getBrand($brandPageId)
    {
        return $this->brandPageRepository->get($brandPageId);
    }

    /**
     * @param $brandPageId
     * @param $config
     * @return mixed
     */
    private function getPreparedConfig($brandPageId, $config)
    {
        $brandAttributeCode = $this->getBrandAttributeCode($brandPageId);
        $configAttributeCode = $this->config->getGeneralConfig()->getBrandAttribute();
        if ($brandAttributeCode != $configAttributeCode) {
            $config['disabled'] = true;
        }

        return $config;
    }
}