<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @copyright (c) Proud Sourcing GmbH | 2016
 * @link www.proudcommerce.com
 * @package psCacheWarmer
 * @version 1.0.1
**/

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'psCacheWarmer',
    'title'        => 'psCacheWarmer',
    'description'  => array(
        'de' => 'Website anhand der XML-Sitemap automatisch aufrufen, z. B. zum "Aufwärmen" eines Caches.<br>
                <b>URL:</b> <a href="'.oxRegistry::getConfig()->getConfigParam('sShopURL').'?cl=psCacheWarmer&key='.oxRegistry::getConfig()->getShopConfVar('psCacheWarmerKey', oxRegistry::getConfig()->getShopId()).'" target="_blank">'.oxRegistry::getConfig()->getConfigParam('sShopURL').'?cl=psCacheWarmer&key='.oxRegistry::getConfig()->getShopConfVar('psCacheWarmerKey', oxRegistry::getConfig()->getShopId()).'</a>',
        'en' => 'Automatically calling urls using the xml-sitemap, eg. for cache warming.
                <b>URL:</b> <a href="'.oxRegistry::getConfig()->getConfigParam('sShopURL').'?cl=psCacheWarmer&key='.oxRegistry::getConfig()->getShopConfVar('psCacheWarmerKey', oxRegistry::getConfig()->getShopId()).'" target="_blank">'.oxRegistry::getConfig()->getConfigParam('sShopURL').'?cl=psCacheWarmer&key='.oxRegistry::getConfig()->getShopConfVar('psCacheWarmerKey', oxRegistry::getConfig()->getShopId()).'</a>',
    ),
    'thumbnail'    => 'logo.jpg',
    'version'      => '1.0.1',
    'author'       => 'Proud Sourcing GmbH',
    'url'          => 'http://www.proudcommerce.com/',
    'email'        => 'support@proudcommerce.com',
    'extend'       => array(
    ),
    'files' => array(
        'pscachewarmer'     => 'proudsourcing/psCacheWarmer/application/controllers/pscachewarmer.php',
    ),
    'templates' => array(
    ),
    'blocks' => array(
    ),
    'settings' => array(
        array(
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerSitemapUrl',
            'type'  => 'str',
            'value' => 'sitemap.xml',
        ),
        array(
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerKey',
            'type'  => 'str',
            'value' => md5(time()),
        ),
        array(
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerUser',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerPass',
            'type'  => 'str',
            'value' => '',
        ),
    ),
);