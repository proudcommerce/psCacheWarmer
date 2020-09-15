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

namespace ProudCommerce\CacheWarmer\Core;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class CacheWarmer
 *
 * @package ProudCommerce\CacheWarmer\Core
 */
class CacheWarmer
{

    /**
     *
     */
    public function run()
    {
        $aUrls = $this->_getSitemapContent();
        if (!empty(Registry::getConfig()->getShopConfVar('psCacheWarmerSitemapUrl')) && !empty($aUrls)) {
            foreach ($aUrls as $sUrl) {
                $oCurl = $this->_runCurlConnect($sUrl);
                $this->_checkCurlResults($oCurl, $sUrl);
                curl_close($oCurl);
            }
        }
    }

    /**
     * @param $sUrl
     *
     * @return false|resource
     */
    protected function _runCurlConnect($sUrl)
    {
        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $sUrl);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 25);
        curl_setopt($oCurl, CURLOPT_HEADER, true);
        $sUsername = Registry::getConfig()->getShopConfVar('psCacheWarmerUser');
        $sPassword = Registry::getConfig()->getShopConfVar('psCacheWarmerPass');
        curl_setopt($oCurl, CURLOPT_USERPWD, $sUsername . ":" . $sPassword);
        curl_exec($oCurl);

        return $oCurl;
    }

    /**
     * @param $oCurl
     * @param $sUrl
     */
    protected function _checkCurlResults($oCurl, $sUrl)
    {
        $httpStatus = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        if (curl_error($oCurl)) {
            $sStatusMsg = 'ERROR';
            $sMessage = curl_error($oCurl);
        } else {
            $sMessage = $sUrl;
            if (in_array(trim($httpStatus), Registry::getConfig()->getShopConfVar('psCacheWarmerHttpCodes'))) {
                $sStatusMsg = 'OK';
            } else {
                $sStatusMsg = 'ERROR';
            }
        }
        $httpStatus = trim($httpStatus);
        $aLog = [$sStatusMsg, $httpStatus, $sMessage];
        print_r($aLog);

        if (!empty($aLog) && ((Registry::getConfig()->getShopConfVar('psCacheWarmerWriteCsvOnlyError') == true && $httpStatus != '200') || Registry::getConfig()->getShopConfVar('psCacheWarmerWriteCsv') == true)) {
            $logger = Logging::getLogger('psCacheWarmer', Registry::getConfig()->getLogsDir() . 'pscachewarmer_' . date("dmY_His") . '.log');
            $logger->info(implode(' | ', $aLog) . "\r");
        }
    }

    /**
     * @param string $sSitemapUrl
     *
     * @return array
     */
    protected function _getSitemapContent($sSitemapUrl = "")
    {
        $aUrls = [];
        if (empty($sSitemapUrl)) {
            $sSitemapUrl = $this->_getSitemapUrl();
        }

        $sUsername = Registry::getConfig()->getShopConfVar('psCacheWarmerUser');
        $sPassword = Registry::getConfig()->getShopConfVar('psCacheWarmerPass');
        $sSitemapUrl = str_replace("://", "://" . $sUsername . ":" . $sPassword . "@", $sSitemapUrl);

        echo $sSitemapUrl . "\r\n";

        $sSitemapXmlData = @file_get_contents($sSitemapUrl);
        if ($oSitemap = @simplexml_load_string($sSitemapXmlData)) {
            if (count($oSitemap->sitemap) > 0) {
                foreach ($oSitemap->sitemap as $oSubSitemap) {
                    $sNextSitemapUrl = (string) $oSubSitemap->loc;
                    $aUrls = array_merge($aUrls, $this->_getSitemapContent($sNextSitemapUrl));
                }
            }

            if (count($oSitemap->url) > 0) {
                foreach ($oSitemap->url as $oSitemapUrl) {
                    $aUrls[] = (string) $oSitemapUrl->loc;
                }
            }
        }

        return $aUrls;
    }

    /**
     * @return string
     */
    protected function _getSitemapUrl()
    {
        $sSitemapUrl = Registry::getConfig()->getShopURL();
        $sSitemapUrl .= Registry::getConfig()->getShopConfVar('psCacheWarmerSitemapUrl');

        return $sSitemapUrl;
    }

}
