<?php
/**
 *
 * THE SCRIPT IMPORTS CUSTOMERS AND THEIR ADDRESSES USING THE TWO PHP FILES
 * STORED IN THE var/customers FOLDER, THE FILES ARE EXPORTS OF TABLES USING phpMyAdmin
 *
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

use AAP\OrderExport\Helper\Export;
use Magento\Framework\App\Bootstrap;
use \League\Csv\Reader;
use \League\Csv\Writer;
use Magento\Store\Model\ScopeInterface;
use Mirasvit\LayeredNavigation\Api\Config\AdditionalFiltersConfigInterface;

include('app/bootstrap.php');
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

PHP_SAPI;

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

/**
 * @var \TTTech\Configurator\Model\ItemRepository
 */
//$objectFactory = $objectManager->get('\Magento\Framework\DataObjectFactory');
//$repo = $objectManager->get('\TTTech\Configurator\Model\ItemRepository');
/**
 * @var $filter \Magento\Cms\Model\Template\FilterProvider
 */
//$filter = $objectManager->get('\Magento\Cms\Model\Template\FilterProvider');
//$logo->load(30);
//
//$out = $filter->getBlockFilter()->filter($logo->getDescription());
//
//echo $out;
//$cron = $objectManager->get('\Schilf\Webinar\Cron\UpdateWebinarCron');
//
//$cron->execute();

//$date = '2019-12-14 17:00:00';
//
//$d = DateTime::createFromFormat(\Schilf\Webinar\Helper\WebinarHelper::WEBINAR_TERMIN_FORMAT, $date);
//
//$a = 3;
//
//echo $d->format(\Schilf\Webinar\Helper\WebinarHelper::WEBINAR_TERMIN_FORMAT);
//
//$a = [
//    26934,
//    35880,
//    26938,
//    38160,
//    26944,
//    26946,
//    26948,
//    26950,
//    26952,
//    26954,
//    26956,
//    26958,
//    26960,
//    27452,
//    27454,
//    28728,
//    28730,
//    29886,
//    29888,
//    29890,
//    29892,
//    31366,
//    31984,
//    31986,
//    34474,
//    35856,
//    36820,
//    37072,
//    37732,
//    37734,
//    40502,
//    43974,
//    42990,
//    42992,
//    43458,
//    45582,
//    47592,
//    48372,
//    48374,
//];
//
//$b = [
//    31258,
//31260,
//31262,
//31264,
//31266,
//31268,
//31270,
//31272,
//31274,
//31276,
//31278,
//31280,
//31282,
//31284,
//29890,
//31288,
//31290,
//40464,
//40478,
//40466,
//40480,
//40482,
//40484,
//40486,
//40488,
//40490,
//40494,
//40496,
//
//];

//$result = array_intersect($a, $b);
//
//print_r($result);
//
//
//$configItem = $repo->getByQuoteItemId(2169136);
//
//$data = [];
//echo md5('Y58Ec1w34403DfJf');
//$updateOrderIds = [];

//$cron->execute();
/**
 * @var $resource \Magento\Framework\App\ResourceConnection
 * @var $upgrade \AAP\OrderExport\Setup\upgrade\Upgrade200
 * @var $mview Magento\Indexer\Cron\UpdateMview
 */
//$mview = $objectManager->get('Magento\Indexer\Cron\UpdateMview');
//$mview->execute();


$resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();

$select = $connection->select()->from(
    'review_entity_summary',
    [
        'primary_id',
        'rating_summary as value',
        'entity_pk_value as entity_id'
    ]
);

$select->where('entity_pk_value in (?)', [1, 1201])
       ->where('store_id = (?)', 1)
;


$ratings = $connection->fetchAssoc($select);

$a = 3;