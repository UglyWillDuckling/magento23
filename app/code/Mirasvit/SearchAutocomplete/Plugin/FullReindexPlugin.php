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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.1.94
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SearchAutocomplete\Plugin;

use Mirasvit\SearchAutocomplete\Service\JsonConfigService;
use Mirasvit\SearchAutocomplete\Model\Config;

class FullReindexPlugin
{
    /**
     * @var JsonConfigService
     */
    private $jsonConfigService;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        JsonConfigService $jsonConfigService,
        Config $config
    ) {
        $this->jsonConfigService = $jsonConfigService;
        $this->config = $config;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecuteFull()
    {
        if($this->config->isFastMode()) {
            $this->jsonConfigService->ensure(JsonConfigService::AUTOCOMPLETE);
        }
    }
}
