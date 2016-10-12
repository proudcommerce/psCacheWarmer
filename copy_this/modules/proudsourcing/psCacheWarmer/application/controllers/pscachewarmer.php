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
class psCacheWarmer extends oxUBase
{
    /**
     * Executes cache warmer
     */
    public function render()
    {
        $sMessage = "<b>psCacheWarmer</b><br>".$this->_getSitemapUrl()."<br>---<br>";

        if($this->_checkAuthentification()) {
            $aUrls = $this->_getSitemapContent();
            if(!empty(oxRegistry::getConfig()->getShopConfVar('psCacheWarmerSitemapUrl'))  && count($aUrls) > 0) {
                foreach($aUrls as $sUrl) {
                    $oCurl = curl_init();
                    curl_setopt($oCurl, CURLOPT_URL, $sUrl);
                    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 25);
                    curl_setopt($oCurl, CURLOPT_HEADER, true);
                    $sUsername = oxRegistry::getConfig()->getShopConfVar('psCacheWarmerUser');
                    $sPassword = oxRegistry::getConfig()->getShopConfVar('psCacheWarmerPass');
                    curl_setopt($oCurl, CURLOPT_USERPWD, $sUsername . ":" . $sPassword);
                    curl_exec($oCurl);
                    $httpStatus = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
                    if(curl_error($oCurl)) {
                        $sMessage .= '<span style="color: orange;">ERROR '.$httpStatus.': ' . curl_error($oCurl) . '</span><br>';
                    } else {
                        $sMessage .= '<span style="color: green;">OK '.$httpStatus.': ' . $sUrl . '</span><br>';
                    }
                    curl_close($oCurl);
                }
            } else {
                $sMessage .= '<span style="color: red;">Keine Daten vorhanden!</span>';
            }
        } else {
            $sMessage .= '<span style="color: red;">Authentifizierung fehlgeschlagen!</span>';
        }

        echo '<pre>'.$sMessage.'</pre>';
        exit;
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

        $sUsername = oxRegistry::getConfig()->getShopConfVar('psCacheWarmerUser');
        $sPassword = oxRegistry::getConfig()->getShopConfVar('psCacheWarmerPass');
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
        $sSitemapUrl = oxRegistry::getConfig()->getConfigParam('sShopURL');
        $sSitemapUrl .= oxRegistry::getConfig()->getShopConfVar('psCacheWarmerSitemapUrl');
        return $sSitemapUrl;
    }

    /**
     * Checks authentification
     *
     * @return bool true|false
     */
    protected function _checkAuthentification()
    {
        $oConfig = oxRegistry::getConfig();
        $sKey = oxRegistry::getConfig()->getRequestParameter("key");
        $sSavedKey = $oConfig->getShopConfVar('psCacheWarmerKey', $oConfig->getShopId());
        if($sSavedKey == $sKey) {
            return true;
        }
        return false;
    }
}
