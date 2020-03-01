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



namespace Mirasvit\AllProducts\Service;

use Magento\Framework\App\Request\Http;
use Mirasvit\AllProducts\Api\Service\MetaServiceInterface;
use Magento\Framework\View\Result\Page;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\AllProducts\Api\Config\ConfigInterface;
use Magento\Framework\View\Element\Template\Context;

class MetaService implements MetaServiceInterface
{
    public function __construct(
        ConfigInterface $config,
        Context $context
    ) {
        $this->config = $config;
        $this->context = $context;

    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return ($this->config->getTitle()) ? : $this->getDefaultMeta();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return ($this->config->getMetaTitle()) ? : $this->getDefaultMeta();
    }

    /**
     * {@inheritdoc}
     */
    public function getKeyword()
    {
        return ($this->config->getMetaKeyword()) ? : $this->getDefaultMeta();
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return ($this->config->getMetaDescription()) ? : $this->getDefaultMeta();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultMeta()
    {
        return __(MetaServiceInterface::DEFAULT_META);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Page $page)
    {
        $pageConfig = $page->getConfig();
        $pageConfig->getTitle()->set(__($this->getMetaTitle()));
        $pageConfig->setMetadata('description', $this->getMetaDescription());
        $pageConfig->setMetadata('keywords', $this->getKeyword());
        $layout = $this->context->getLayout();
        if ($pageMainTitle = $layout->getBlock('page.main.title')) {
            $pageMainTitle->setPageTitle($this->getTitle());
        }

        return $page;
    }
}