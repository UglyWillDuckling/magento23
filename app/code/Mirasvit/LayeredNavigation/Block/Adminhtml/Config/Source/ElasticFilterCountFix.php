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



namespace Mirasvit\LayeredNavigation\Block\Adminhtml\Config\Source;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Mirasvit\LayeredNavigation\Api\Service\VersionServiceInterface;
use Magento\Backend\Block\Template\Context;
use Mirasvit\LayeredNavigation\Api\Service\ElasticsearchServiceInterface;

class ElasticFilterCountFix extends \Magento\Config\Block\System\Config\Form\Field
{

    public function __construct(
        Context $context,
        VersionServiceInterface $versionService,
        ElasticsearchServiceInterface $elasticsearchService,
        array $data = []
    ) {
        $this->versionService = $versionService;
        $this->elasticsearchService = $elasticsearchService;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if ($this->versionService->isEe() && $this->elasticsearchService->isElasticEnabled()) {
            return parent::render($element);
        }

        return false;
    }
}
