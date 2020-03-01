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



namespace Mirasvit\LayeredNavigation\Service;

use Magento\Framework\App\ProductMetadataInterface;
use Mirasvit\LayeredNavigation\Api\Service\VersionServiceInterface;

class VersionService implements VersionServiceInterface
{
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->productMetadata->getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getEdition()
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * {@inheritdoc}
     */
    public function isEe()
    {
        if ($this->getEdition() == 'Enterprise') {
            return true;
        }

        return false;
    }
}
