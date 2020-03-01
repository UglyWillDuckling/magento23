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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.11
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Plugin;

use Magento\Framework\Registry;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

/**
 * Use in admin panel if change
 * "Separator between words in complex filter names"
 */
class TableRefreshAdminPlugin
{
    /**
     * @param Config $config
     * @param Registry $registry
     */
    public function __construct(
        ResourceConnection $resource,
        Config $config,
        Registry $registry,
        MessageManagerInterface $messageManager
    ) {
        $this->registry = $registry;
        $this->config = $config;
        $this->resource = $resource;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Magento\Config\Model\Config $subject
     * @return void
     */
    public function beforeSave($subject)
    {
        $data = $subject->getData();
        if (isset($data['groups']['seofilter']['fields'][Config::SEPARATOR_CONFIG]['value'])) {
            $newValue = $data['groups']['seofilter']['fields'][Config::SEPARATOR_CONFIG]['value'];
            $currentValue = $this->config->getComplexFilterNamesSeparator();
            if ($newValue != $currentValue) {
                $connection = $this->resource->getConnection();
                $table = $this->resource->getTableName(RewriteInterface::TABLE_NAME);
                $query = 'TRUNCATE TABLE ' . $table;
                $connection->query($query);
                $message = '"Separator between words in complex filter names" has been changed. 
                Please refresh all cache.';
                $this->messageManager->addNotice($message);
            }
        }
    }

}