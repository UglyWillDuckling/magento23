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



namespace Mirasvit\AllProducts\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class Styles extends \Magento\Framework\App\Action\Action
{
    /**
     * @var ProductMetadataInterface
     */
    private $metadata;
    /**
     * @var AssetRepository
     */
    private $assetRepository;

    public function __construct(
        AssetRepository $assetRepository,
        ProductMetadataInterface $metadata,
        Context $context
    ) {
        $this->assetRepository = $assetRepository;
        $this->metadata = $metadata;
        parent::__construct($context);
    }

    /**
     * Render additional styles for all products page.
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $rawResult */
        $rawResult = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $rawResult->setHeader('content-type', 'text/css');
        if (version_compare($this->metadata->getVersion(), '2.3.0', '<')) {
            $asset = $this->assetRepository->createAsset('Magento_Swatches::css/swatches.css');
            $styles = $asset->getContent();
            $rawResult->setContents($styles);
        }

        return $rawResult;
    }
}
