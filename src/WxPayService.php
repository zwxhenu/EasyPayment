<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2019/4/26
 * Time: 16:13
 */

namespace EasyPayment\payment;

use EasyPayment\payment\PayContract;
use EasyPayment\payment\PayCommon;
use EasyPayment\payment\wxpay\lib\WxPayConfig;
use EasyPayment\payment\wxpay\lib\WxPayApi;
class WxPayService implements PayContract
{
    private $pay_money = 0;
    private $subject = '';
    private $body = '';
    private $showUrl = '';
    private $trade_type = 0;
    private $order_sn = '';
    private $success_url = '';
    private $error_url = '';
    private $is_wap = false;
    private $pay_common_obj = null;

    public function __construct()
    {
        $this->pay_common_obj = new PayCommon();
    }
    /**
     * 是否为WAP支付
     *
     * @param $is_wap
     * @return $this
     */
    public function setIsWap($is_wap)
    {
        $this->is_wap = (bool)$is_wap;

        return $this;
    }

    /**
     * 支付金额
     *
     * @param int $pay_money
     * @return $this
     */
    public function setPayMoney($pay_money)
    {
        if (!is_numeric($pay_money) || $pay_money <= 0) {
            return false;
        }
        $this->pay_money = $pay_money;

        return $this;
    }

    /**
     * 支付摘要
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $subject = trim($subject);
        if (empty($subject)) {
            return false;
        }
        $this->subject = $subject;

        return $this;
    }

    /**
     * 商品详情
     *
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        // body不超过60个字符
        $this->body = trim($body);

        return $this;
    }

    /**
     * @param string $showUrl
     * @return $this
     */
    public function setShowUrl($showUrl)
    {
        $this->showUrl = trim($showUrl);

        return $this;
    }

    /**
     * @param int $trade_type
     * @return $this
     */
    public function setTradeType($trade_type)
    {
        if (!is_numeric($trade_type)) {
            return false;
        }
        $this->trade_type = (int)$trade_type;

        return $this;
    }

    /**
     * @param string $order_sn
     * @return $this
     */
    public function setOrderSn($order_sn)
    {
        $this->order_sn = $order_sn;

        return $this;
    }

    /**
     * @param string $success_url
     * @return $this
     */
    public function setSuccessUrl($success_url)
    {
        $this->success_url = trim($success_url);

        return $this;
    }

    /**
     * @param string $error_url
     * @return $this
     */
    public function setErrorUrl($error_url)
    {
        $this->error_url = $error_url;

        return $this;
    }
    /**
     * 获取openid
     *
     * @return array
     */
    public function getOpenid()
    {
        // 加载配置文件
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Config.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Api.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.JsApiPay.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Default.Account.php');
        WxPayConfig::$APPID = $pay_config['APPID'];
        WxPayConfig::$KEY = $pay_config['KEY'];
        WxPayConfig::$MCHID = $pay_config['MCHID'];
        WxPayConfig::$APPSECRET = $pay_config['APPSECRET'];
        $tools = new WxPayJsApiPay();
        $openid = $tools->GetOpenid();
        if ($openid) {
            return alert_info(0, '', $openid);
        }
        return alert_info(1, '获取微信openid失败');
    }

}