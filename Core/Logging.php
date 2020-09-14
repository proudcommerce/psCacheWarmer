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
 * @version       3.1.0
 **/

namespace ProudCommerce\CacheWarmer\Core;

use \OxidEsales\Eshop\Core\Registry;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

/**
 * Class Logging
 *
 * @package ProudCommerce\CacheWarmer\Core
 */
class Logging
{

    /**
     * @var array
     */
    protected static $_aLogger = [];


    /**
     * @param string $sLogger
     * @param string $sPath
     * @param string $sLogLevel
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getLogger(string $sLogger = '', string $sPath = '', string $sLogLevel = ''): Logger
    {
        $sLogger = (!empty($sLogger) ? $sLogger : 'OXID Logger');
        if (!array_key_exists($sLogger, self::$_aLogger)) {
            $sPath = ((!empty($sPath)) ? $sPath : Registry::getConfig()->getLogsDir() . 'oxideshop.log');
            $sLogLevel = strtoupper($sLogLevel);
            $sLogLevel = ((!empty($sLogLevel) && defined("Logger::$sLogLevel")) ? constant("Logger::$sLogLevel") : Logger::DEBUG);
            self::$_aLogger[ $sLogger ] = new Logger($sLogger);
            self::$_aLogger[ $sLogger ]->pushHandler(
                new StreamHandler(
                    $sPath,
                    $sLogLevel
                )
            );
        }

        return self::$_aLogger[ $sLogger ];
    }
}
