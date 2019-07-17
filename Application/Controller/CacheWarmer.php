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

namespace ProudCommerce\CacheWarmer\Application\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Controller\BaseController;

class CacheWarmer extends BaseController
{
    protected $_sFileAndPathToExportFile = '';
    protected $_handle = null;
    protected $_sSeparator = ";";
    protected $_sEnclosure = "'";

    // todo: add to config
    protected $_aHttpCodesIsOkay = array('200','302');
    /**
     * Executes cache warmer
     */
    public function render()
    {
        $sMessage = "<b>psCacheWarmer</b><br>".$this->_getSitemapUrl()."<br>---<br>";

        $this->_sFileAndPathToExportFile = $this->_getPathWithFileName();
        if(Registry::getConfig()->getShopConfVar('psCacheWarmerWriteCsv') == true)
        {
            $this->_handle = fopen( $this->_sFileAndPathToExportFile, "w+");;
        }

        if($this->_checkAuthentification()) {
            $aUrls = $this->_getSitemapContent();
            if(!empty(Registry::getConfig()->getShopConfVar('psCacheWarmerSitemapUrl'))  && count($aUrls) > 0) {
                foreach($aUrls as $sUrl) {
                    $oCurl = $this->_runCurlConnect($sUrl);
                    $sMessage .= $this->_checkCurlResults($oCurl,$sUrl);
                    curl_close($oCurl);
                }
            } else {
                $sMessage .= '<span style="color: red;">Keine Daten vorhanden!</span>';
            }
        } else {
            $sMessage .= '<span style="color: red;">Authentifizierung fehlgeschlagen!</span>';
        }

        if(Registry::getConfig()->getShopConfVar('psCacheWarmerWriteCsv') == true)
        {
            fclose($this->_handle);
        }
        echo '<pre>'.$sMessage.'</pre>';
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

    protected function _checkCurlResults($oCurl,$sUrl)
    {
        $sMessage = '';
        $httpStatus = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        if(curl_error($oCurl)) {
            $sMessage .= '<span style="color: orange;">ERROR '.$httpStatus.': ' . curl_error($oCurl) . '</span><br>';
            $sStatusMsg = 'ERROR';
            $sTmpText = curl_error($oCurl);
        } else {
            $sTmpText = $sUrl;
            if(in_array(trim($httpStatus),$this->_aHttpCodesIsOkay))
            {
                $sMessage .= '<span style="color: green;">OK '.$httpStatus.': ' . $sUrl . '</span><br>';
                $sStatusMsg = 'OK';
            }
            else{
                $sMessage .= '<span style="color: red;">ERROR <b>'.$httpStatus.'</b>: ' . $sUrl. '</span><br>';
                $sStatusMsg = 'ERROR';
            }
        }

        if(Registry::getConfig()->getShopConfVar('psCacheWarmerWriteCsv') == true)
        {
            $aTmp = array($sStatusMsg,
                        $httpStatus,
                          $sTmpText
            );

            if(trim($httpStatus) == '200' && Registry::getConfig()->getShopConfVar('psCacheWarmerWriteCsvOnlyError') == true)
            {
                $aTmp = array();
            }

            if(count($aTmp)) {
                fputcsv($this->_handle, $aTmp, $this->_sSeparator, $this->_sEnclosure);
            }
        }

        return $sMessage;
    }

    /**
     * Returens urls from sitemap
     *
     * @return array    urls
     */
    protected function _getSitemapContent($sSitemapUrl = "")
    {
        $aUrls = array();
        if(empty($sSitemapUrl)) {
            $sSitemapUrl = $this->_getSitemapUrl();
        }

        $sUsername = Registry::getConfig()->getShopConfVar('psCacheWarmerUser');
        $sPassword = Registry::getConfig()->getShopConfVar('psCacheWarmerPass');
        $sSitemapUrl = str_replace("://", "://".$sUsername.":".$sPassword."@", $sSitemapUrl);

        $sSitemapXmlData = @file_get_contents($sSitemapUrl);
        if($oSitemap = @simplexml_load_string($sSitemapXmlData)) {
            if (count($oSitemap->sitemap) > 0) {
                foreach ($oSitemap->sitemap as $oSubSitemap) {
                    $sNextSitemapUrl = (string)$oSubSitemap->loc;
                    $aUrls = array_merge($aUrls, $this->_getSitemapContent($sNextSitemapUrl));
                }
            }

            if(count($oSitemap->url) > 0) {
                foreach($oSitemap->url as $oSitemapUrl) {
                    $aUrls[] = (string)$oSitemapUrl->loc;
                }
            }
        }
        #print_r($aUrls);
        return $aUrls;
    }

    /**
     * Returens sitemap url
     *
     * @return string   sitemap url
     */
    protected function _getSitemapUrl()
    {
        $sSitemapUrl = Registry::getConfig()->getConfigParam('sShopURL');
        $sSitemapUrl .= Registry::getConfig()->getShopConfVar('psCacheWarmerSitemapUrl');
        return $sSitemapUrl;
    }

    /**
     * Checks authentification
     *
     * @return bool true|false
     */
    protected function _checkAuthentification()
    {
        $oConfig = Registry::getConfig();
        $sKey = Registry::getConfig()->getRequestParameter("key");
        $sSavedKey = $oConfig->getShopConfVar('psCacheWarmerKey', $oConfig->getShopId());
        if($sSavedKey == $sKey) {
            return true;
        }
        return false;
    }


    /**
     * Return Path with Filename, from /log
     *
     * @return string
     */
    protected function _getPathWithFileName()
    {
        return Registry::getConfig()->getConfigParam('sShopDir').'/log/'.$this->_getFileName();
    }

    /**
     * Return Filename, Formae psCacheWarmerReport_20190717-122345.csv
     *
     * @return string
     */
    protected function _getFileName()
    {
        return 'psCacheWarmerReport_'.date("Ymd-His").".csv";
    }

}
