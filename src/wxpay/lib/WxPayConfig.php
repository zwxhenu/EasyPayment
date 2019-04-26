<?php
/**
 * 配置账号信息
 */
namespace EasyPayment\payment\wxpay\lib;

class WxPayConfig
{
    public static $APPID = '';
    public static $MCHID = '';
    public static $KEY = '';
    public static $APPSECRET = '';
    // =======【基本信息设置】=====================================
    //
    /**
     * 微信公众号信息配置
     * APPID：绑定支付的APPID（必须配置）
     * MCHID：商户号（必须配置）
     * KEY：商户支付密钥，参考开户邮件设置（必须配置）
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置）
     *
     * @var string
     */
    // const APPID = 'wxb18cbde398876439';
    // const MCHID = '1247962101';
    // const KEY = 'AkfdDRdfjkjfielkskji1242scisdf43';
    // const APPSECRET = '356b99407cc2ecc4a59036882436a95a';
    const NOTIFY_URL = PAY_RETURN_URL . '/wxpay_notify';

    // =======【证书路径设置】=====================================
    //证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要）
    const SSLCERT_PATH = './../cert/apiclient_cert.pem';
    const SSLKEY_PATH = './../cert/apiclient_key.pem';

    // =======【curl代理设置】===================================
    /**
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认0.0.0.0和0，此时不开启代理（如有需要才设置）
     */
    const CURL_PROXY_HOST = "0.0.0.0";
    const CURL_PROXY_PORT = 0;

    // =======【上报信息配置】===================================
    /**
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     */
    const REPORT_LEVENL = 0;
}
