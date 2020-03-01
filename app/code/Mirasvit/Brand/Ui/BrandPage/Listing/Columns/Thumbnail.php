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



namespace Mirasvit\Brand\Ui\BrandPage\Listing\Columns;

use Mirasvit\Brand\Api\Service\ImageUrlServiceInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Thumbnail extends Column
{
    /**
     * {@inheritdoc}
     */
    const NAME = 'thumbnail';

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ImageUrlServiceInterface $imageUrlService,
        array $components = [],
        array $data = []
    ) {
        $this->imageUrlService = $imageUrlService;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $name = $this->getName();
            $config = $this->getConfiguration();
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$name . '_src'] = $this->imageUrlService->getImageUrl($item[$name], 'thumbnail');
                $item[$name . '_alt'] = (isset($item[$config['altField']])) ? $item[$config['altField']] : '';
            }
        }
        return $dataSource;
    }
}
