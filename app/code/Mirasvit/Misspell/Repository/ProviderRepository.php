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
 * @package   mirasvit/module-misspell
 * @version   1.0.31
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Misspell\Repository;

use Mirasvit\Misspell\Api\ProviderInterface;
use Mirasvit\Misspell\Api\Repository\ProviderRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ProviderRepository implements ProviderRepositoryInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ProviderInterface[]
     */
    private $providers;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $providers = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider()
    {
        $provider = $this->scopeConfig->getValue('search/engine/engine');

        if (isset($this->providers[$provider])) {
            return $this->providers[$provider];
        }

        return $this->providers['mysql'];
    }
}
