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

use Magento\Framework\App\Filesystem\DirectoryList;
use Mirasvit\LayeredNavigation\Api\Service\CssServiceInterface;
use Mirasvit\LayeredNavigation\Api\Service\CssCreatorServiceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Message\ManagerInterface;
use Mirasvit\LayeredNavigation\Api\Data\CssVariableInterface;
use Magento\Variable\Model\VariableFactory;

class CssService implements CssServiceInterface
{
    public function __construct(
        DirectoryList $directoryList,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        CssCreatorServiceInterface $cssCreatorService,
        VariableFactory $variableFactory
    ) {
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->cssCreatorService = $cssCreatorService;
        $this->variableFactory = $variableFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function generateCss($websiteId, $storeId)
    {
        if(!$websiteId && !$storeId) {
            $websites = $this->storeManager->getWebsites(false, false);
            foreach ($websites as $id => $value) {
                $this->generateWebsiteCss($id);
            }
        } else {
            if($storeId) {
                $this->generateStoreCss($storeId);
            } else {
                $this->generateWebsiteCss($websiteId);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateWebsiteCss($websiteId)
    {
        $website = $this->storeManager->getWebsite($websiteId);
        foreach($website->getStoreIds() as $storeId){
            $this->generateStoreCss($storeId);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateStoreCss($storeId)
    {
        $store = $this->storeManager->getStore($storeId);
        if(!$store->isActive()) {
            return false;
        }

        $css = $this->getCss($storeId);
        if (!$css) {
            return false;
        }

        $variable = $this->loadVariable($storeId);
        if (!$variable || !$variable->getData()) {
            $this->createVariable();
            $variable = $this->loadVariable($storeId);
        }

        $variableValue = uniqid();
        $variable->setPlainValue($variableValue)->setStorePlainValue($variableValue)->save();

        $storeCode = $store->getCode();
        $fullCssDirPath = $this->getFullCssDirPath($storeCode);
        $fullCssFilePath = $this->getFullCssFilePath($storeCode, $storeId);

        try {
            if(!file_exists($fullCssDirPath)) {
                @mkdir($fullCssDirPath, 0777);
            }
            foreach (glob($fullCssDirPath . "*" . $storeCode . "*") as $filename) {
                @unlink($filename);
            }
            $file = @fopen($fullCssFilePath,"w+");
            @flock($file, LOCK_EX);
            @fwrite($file, $css);
            @flock($file, LOCK_UN);
            @fclose($file);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Failed genareation CSS file: ' . $fullCssFilePath . ' in '
                    . $this->getFullCssPath($storeCode)
                    .'<br/>Message: '.$e->getMessage())
            );
        }
    }

    /**
     * @return void
     */
    private function createVariable()
    {
        $variable = $this->variableFactory->create();
        $data = [
            'code' => CssVariableInterface::CSS_VARIABLE,
            'name' => CssVariableInterface::CSS_VARIABLE,
            'html_value' => '',
            'plain_value' => '',
        ];

        $variable->setData($data);
        $variable->save();
    }

    /**
     * @param $storeId
     * @return \Magento\Variable\Model\Variable
     */
    private function loadVariable($storeId)
    {
        return $this->variableFactory->create()
            ->setStoreId($storeId)->loadByCode(CssVariableInterface::CSS_VARIABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPath($storeCode = false, $storeId = false, $front = false)
    {
        if (!$storeCode) {
            $storeCode = $this->storeManager->getStoreCode();
        }

        if (!$storeId) {
            $storeCode = $this->storeManager->getStore()->getId();
        }

        $variable = $this->variableFactory->create()
            ->setStoreId($storeId)->loadByCode(CssVariableInterface::CSS_VARIABLE);

        $front = false;
        return $this->getCssDir($front)
        . CssServiceInterface::CSS_FIRST_PART_NAME
        . $storeCode
        . $variable->getPlainValue()
        . '.css';
    }

    /**
     * @return string
     */
    private function getFullCssDirPath()
    {
        return $this->directoryList->getRoot() . '/' . $this->getCssDir();
    }

    /**
     * @return string
     */
    private function getFullCssFilePath($storeCode, $storeId)
    {
        return $this->directoryList->getRoot() . '/' . $this->getCssPath($storeCode, $storeId);
    }

    /**
     * @return string
     */
    private function getCssDir($front = false)
    {
        $pub = ($front) ? $this->getFrontPubPath() : CssServiceInterface::PUB;
        return $pub . CssServiceInterface::CSS_PATH;
    }

    /**
     * @return string
     */
    private function getFrontPubPath()
    {
        $pub = '';
        if ($this->directoryList->getUrlPath(CssServiceInterface::PUB) == CssServiceInterface::PUB) {
            $pub = '/' . CssServiceInterface::PUB;
        }

        return $pub;
    }

    /**
     * @return string
     */
    private function getCss($storeId)
    {
        return $this->cssCreatorService->getCssContent($storeId);
    }
}