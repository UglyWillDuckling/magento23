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



namespace Mirasvit\LayeredNavigation\Service\Config;

use Magento\Framework\App\ObjectManager;
use Mirasvit\LayeredNavigation\Api\Config\ConfigInterface;

trait ConfigTrait
{
    /**
     * @return int
     */
    public function isAjaxEnabled()
    {
        return self::getConfig()->isAjaxEnabled();
    }


    /**
     * @return int
     */
    static public function isMultiselectEnabled()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Mirasvit\LayeredNavigation\Api\Config\ConfigInterface::class)
            ->isMultiselectEnabled();
    }

    /**
     * @return int
     */
    protected static function getConfig()
    {
        return ObjectManager::getInstance()->get(ConfigInterface::class);
    }

    /**
     * Is allowed to process request.
     *
     * @param \Magento\Framework\App\Request\Http|\Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     */
    protected function isAllowed($request)
    {
        return $request->isAjax() && $this->isAjaxEnabled() && !$this->isExternalRequest($request);
    }

    /**
     * Is request triggered by external modules.
     *
     * @param \Magento\Framework\App\Request\Http|\Magento\Framework\App\RequestInterface $request
     *
     * @return bool
     */
    protected function isExternalRequest($request)
    {
        $externalParams = ['ajaxscroll', 'is_scroll'];
        $params = $request->getParams();

        foreach ($externalParams as $param) {
            if (array_key_exists($param, $params)) {
                return true;
            }
        }

        return false;
    }
}
