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



namespace Mirasvit\Brand\Model\Brand\PostData;

use Mirasvit\Brand\Api\Data\PostData\ProcessorInterface;
use Mirasvit\Brand\Api\Repository\BrandRepositoryInterface;

class TitleProcessor implements ProcessorInterface
{
    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function preparePostData($data)
    {
        if (isset($data['brand_title']) && !$data['brand_title']
            && isset($data['attribute_option_id']) && $data['attribute_option_id']
        ) {
            $data['brand_title'] = $this->brandRepository->get($data['attribute_option_id'])->getLabel();
        }

        return $data;
    }
}
