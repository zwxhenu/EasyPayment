<?php

namespace EasyPayment\payment\wxpay\lib;

use EasyPayment\payment\wxpay\lib\WxPayConfig;
use EasyPayment\payment\wxpay\lib\WxPayApi;

class WxPayJsApiPay
{
    public $data = null;
    private $curl_timeout = 10;
    public $APPID = '';
    public $APPSECRET = '';

    public function GetOpenid()
    {
        // 通过code获得openid
        if (!isset($_GET['code'])) {
            // 触发微信返回code码
            $baseUrl = urlencode($this->currentUrl());
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            header("Location: $url");
            exit();
        } else {
            // 获取code码，以获取openid
            $code = $_GET['code'];
            $openid = $this->GetOpenidFromMp($code);
            return $openid;
        }
    }

    /**
     * @param $UnifiedOrderResult
     * @return string
     * @throws WxPayException
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        if (!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == "") {
            throw new WxPayException("参数错误");
        }
        $jsapi = new WxPayJsApiPayData();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

    /**
     * @param $code
     * @return mixed
     */
    public function GetOpenidFromMp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        // 初始化curl
        $ch = curl_init();
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (WxPayConfig::CURL_PROXY_HOST != "0.0.0.0" && WxPayConfig::CURL_PROXY_PORT != 0) {
            curl_setopt($ch, CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        }
        // 运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        // 取出openid
        $data = json_decode($res, true);
        $this->data = $data;
        $openid = $data['openid'];
        return $openid;
    }

    /**
     * @param $urlObj
     * @return string
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * @param $redirectUrl
     * @return string
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = WxPayConfig::$APPID;
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "8" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     * @param $code
     * @return string
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = WxPayConfig::$APPID;
        $urlObj["secret"] = WxPayConfig::$APPSECRET;
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }

    /**
     * 当前请求地址
     * @return string
     */
    private function currentUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
            || (int)$_SERVER['SERVER_PORT'] === 443) ? 'https://' : 'http://';

        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}