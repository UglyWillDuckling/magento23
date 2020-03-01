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
 * @package   mirasvit/module-search-elastic
 * @version   1.2.45
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchElastic\Plugin;

use Mirasvit\Search\Api\Repository\IndexRepositoryInterface;
use Mirasvit\SearchElastic\Model\Config;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\Dimension;
use Magento\Store\Model\StoreManagerInterface;

class AutocompleteJsonConfigPlugin
{
    private $config;

    private $indexRepository;

    private $resolver;

    private $storeManager;

    public function __construct(
        Config $config,
        IndexRepositoryInterface $indexRepository,
        IndexScopeResolver $resolver,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->indexRepository = $indexRepository;
        $this->resolver = $resolver;
        $this->storeManager = $storeManager;
    }

    public function afterGenerate($subject, $config)
    {
        if ($config['engine'] !== 'elastic') {
            return $config;
        }

        $config = array_merge($config, $this->getEngineConfig());

        foreach ($this->storeManager->getStores() as $store) {
            foreach ($config['indexes'][$store->getId()] as $identifier => $data) {
                $data = array_merge($data, $this->getEngineIndexConfig(
                    $identifier,
                    new Dimension('scope', $store->getId())
                ));

                $config['indexes'][$store->getId()][$identifier] = $data;
            }
        }

        return $config;
    }

    /**
     * @param string $identifier
     * @param $dimension
     * @return array
     */
    public function getEngineIndexConfig($identifier, $dimension)
    {
        $instance = $this->indexRepository->getInstance($identifier);

        $indexName = $this->config->getIndexName(
            $this->resolver->resolve($instance->getIndexName(), [$dimension])
        );

        $result = [];
        $result['index'] = $indexName;
        $result['fields'] = $instance->getAttributeWeights();

        return $result;
    }

    /**
     * @return array
     */
    public function getEngineConfig()
    {
        return [
            'host'      => $this->config->getHost(),
            'port'      => $this->config->getPort(),
            'available' => true,
        ];
    }
}
