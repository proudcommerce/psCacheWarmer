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

namespace ProudCommerce\CacheWarmer\Core;

if (PHP_SAPI != 'cli') {
    throw new RuntimeException('Only cli execution allowed!');
}

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Controller\BaseController;

/**
 * Class CacheWarmer
 * @package ProudCommerce\CacheWarmer\Core
 */
class CacheWarmer extends BaseController
{

    /**
     * @return string|void|null
     */
    public function render()
    {
        $sMessage = "<b>psCacheWarmer</b><br>" . $this->_getSitemapUrl() . "<br>---<br>";

        $aUrls = $this->_getSitemapContent();
        if (!empty(Registry::getConfig()->getShopConfVar('psCacheWarmerSitemapUrl')) && count($aUrls) > 0) {
            foreach ($aUrls as $sUrl) {
                $oCurl = $this->_runCurlConnect($sUrl);
                $sMessage .= $this->_checkCurlResults($oCurl, $sUrl);
                curl_close($oCurl);
            }
        } else {
            $sMessage .= '<span style="color: red;">Keine Daten vorhanden!</span>';
        }

        echo '<pre>' . $sMessage . '</pre>';
        exit;
    }

    /**
     * @param $sUrl
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
     * @return string
     */
    protected function _checkCurlResults($oCurl, $sUrl)
    {
        $sMessage = '';
        $httpStatus = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        if (curl_error($oCurl)) {
            $sMessage .= '<span style="color: orange;">ERROR ' . $httpStatus . ': ' . curl_error($oCurl) . '</span><br>';
            $sStatusMsg = 'ERROR';
            $sTmpText = curl_error($oCurl);
        } else {
            $sTmpText = $sUrl;
            if (in_array(trim($httpStatus), Registry::getConfig()->getShopConfVar('psCacheWarmerConfig'))) {
                $sMessage .= '<span style="color: green;">OK ' . $httpStatus . ': ' . $sUrl . '</span><br>';
                $sStatusMsg = 'OK';
            } else {
                $sMessage .= '<span style="color: red;">ERROR <b>' . $httpStatus . '</b>: ' . $sUrl . '</span><br>';
                $sStatusMsg = 'ERROR';
            }
        }

        if (Registry::getConfig()->getShopConfVar('psCacheWarmerWriteCsv') == true) {
            $aTmp = [$sStatusMsg, $httpStatus, $sTmpText];

            if (trim($httpStatus) == '200' && Registry::getConfig()->getShopConfVar('psCacheWarmerWriteCsvOnlyError') == true) {
                $aTmp = [];
            }

            if (count($aTmp)) {
                Registry::getUtils()->writeToLog(implode(' | ', $aTmp) . "\r", 'tabslbillomat_' . date("d.m.Y H:i:s") . '.log');
            }
        }

        return $sMessage;
    }

    /**
     * @param string $sSitemapUrl
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

        $sSitemapXmlData = @file_get_contents($sSitemapUrl);
        if ($oSitemap = @simplexml_load_string($sSitemapXmlData)) {
            if (count($oSitemap->sitemap) > 0) {
                foreach ($oSitemap->sitemap as $oSubSitemap) {
                    $sNextSitemapUrl = (string)$oSubSitemap->loc;
                    $aUrls = array_merge($aUrls, $this->_getSitemapContent($sNextSitemapUrl));
                }
            }

            if (count($oSitemap->url) > 0) {
                foreach ($oSitemap->url as $oSitemapUrl) {
                    $aUrls[] = (string)$oSitemapUrl->loc;
                }
            }
        }
        #print_r($aUrls);
        return $aUrls;
    }

    /**
     * @return string
     */
    protected function _getSitemapUrl()
    {
        $sSitemapUrl = Registry::getConfig()->getConfigParam('sShopURL');
        $sSitemapUrl .= Registry::getConfig()->getShopConfVar('psCacheWarmerSitemapUrl');
        return $sSitemapUrl;
    }

}
