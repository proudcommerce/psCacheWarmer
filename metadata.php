<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @copyright (c) ProudCommerce | 2020
 * @link          www.proudcommerce.com
 * @package       psCacheWarmer
 * @version       3.1.1
 **/

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'          => 'psCacheWarmer',
    'title'       => 'psCacheWarmer',
    'description' => [
        'de' => 'Website anhand der XML-Sitemap automatisch aufrufen, z. B. zum "Aufwärmen" eines Caches.',
        'en' => 'Automatically calling urls using the xml-sitemap, eg. for cache warmup.',
    ],
    'thumbnail'   => 'logo_pc-os.jpg',
    'version'     => '3.1.2',
    'author'      => 'ProudCommerce',
    'url'         => 'https://github.com/proudcommerce/psCacheWarmer',
    'email'       => '',
    'extend'      => [
    ],
    'controllers' => [
    ],
    'templates'   => [
    ],
    'blocks'      => [
    ],
    'settings'    => [
        [
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerSitemapUrl',
            'type'  => 'str',
            'value' => 'sitemap.xml',
        ],
        [
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerKey',
            'type'  => 'str',
            'value' => md5(time()),
        ],
        [
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerUser',
            'type'  => 'str',
            'value' => '',
        ],
        [
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerPass',
            'type'  => 'str',
            'value' => '',
        ],
        [
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerHttpCodes',
            'type'  => 'arr',
            'value' => [200, 302],
        ],
        [
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerWriteCsv',
            'type'  => 'bool',
            'value' => false,
        ],
        [
            'group' => 'psCacheWarmerConfig',
            'name'  => 'psCacheWarmerWriteCsvOnlyError',
            'type'  => 'bool',
            'value' => true,
        ]
    ],
];
