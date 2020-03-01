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



namespace Mirasvit\LayeredNavigation\Model\Request;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\RequestInterface;

use Magento\Framework\Search\Request\Builder as RequestBuilder;
use Magento\Framework\Search\Request\Config as RequestConfig;
use Magento\Framework\Search\Request\Binder as RequestBinder;
use Magento\Framework\Search\Request\Cleaner as RequestCleaner;
use Magento\Framework\App\Request\Http;

class Builder extends RequestBuilder
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $httpRequest;

    /**
     * @var array
     */
    protected $removablePlaceholders = [];

    public function __construct(
        ObjectManagerInterface $objectManager,
        RequestConfig $config,
        RequestBinder $binder,
        RequestCleaner $cleaner,
        Http $http
    )
    {
        parent::__construct($objectManager, $config, $binder, $cleaner);
        $this->httpRequest = $http;
    }

    public function bind($placeholder, $value)
    {
        $this->removablePlaceholders[$placeholder] = $value;

        return $this;
    }

    public function removePlaceholder($placeholder)
    {
        if (array_key_exists($placeholder, $this->removablePlaceholders)) {
            unset($this->removablePlaceholders[$placeholder]);
        }
        return $this;
    }

    public function hasPlaceholder($placeholder)
    {
        return array_key_exists($placeholder, $this->removablePlaceholders);
    }

    /**
     * Create request object
     *
     * @return RequestInterface
     */
    public function create()
    {
        $this->commit();
        return parent::create();
    }

    protected function commit()
    {
        foreach ($this->removablePlaceholders as $key => $value) {
            parent::bind($key, $value);
        }
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    public function hash(RequestInterface $request)
    {
        $data = [
            $request->getName(),
            $request->getFrom(),
            $request->getIndex(),
            $request->getSize(),
            print_r($request->getQuery(), true),
            print_r($request->getAggregation(), true),
        ];
        return md5(\Zend_Json::encode($data));
    }
}
