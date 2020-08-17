<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @copyright (c) ProudCommerce | 2020
 * @link www.proudcommerce.com
 * @package psCacheWarmer
 * @version 3.0.0
 **/

if (PHP_SAPI != 'cli') {
    die('Only cli execution allowed!'."\r\n");
}

$options = getopt('s:');
$shopId = $options['s'] ?? 0;
if (!$shopId) {
    $shopId = 1;
}

require_once dirname(__FILE__) . '/../../../../bootstrap.php';

use OxidEsales\Eshop\Core\Registry;
use ProudCommerce\CacheWarmer\Core\CacheWarmer;

echo 'Shop-ID '.$shopId." is used!\r\n";
Registry::getConfig()->setShopId($shopId);
Registry::set(Config::class, null);

$cacheWarmer = new CacheWarmer();
$cacheWarmer->run();
