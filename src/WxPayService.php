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
use EasyPayment\payment\wxpay\lib\WxPayJsApiPay;
use EasyPayment\payment\wxpay\lib\WxPayApi;
use EasyPayment\payment\wxpay\lib\WxPayUnifiedOrder;
use EasyPayment\payment\wxpay\lib\WxPayNativePay;
use EasyPayment\payment\wxpay\lib\WxPayOrderQuery;

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
    private $app_secret = '';
    private $mch_id = '';
    private $key = '';
    private $app_id = '';
    private $out_trade_no = '';
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
     * 设置app_id
     * @param $app_id
     * @return $this
     */
    public function setAppId($app_id)
    {
        $this->app_id = $app_id;

        return $this;
    }
    /**
     * 设置key
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }
    /**
     * 设置商户ID
     *
     * @param $mch_id
     * @return $this
     */
    public function setMchId($mch_id)
    {
        $this->mch_id = $mch_id;

        return $this;
    }
    /**
     * 设置支付秘钥
     *
     * @param $app_secret
     * @return $this
     */
    public function setAppSecret($app_secret)
    {
        $this->app_secret = $app_secret;

        return $this;
    }

    /**
     * 设置外部订单号
     *
     * @param $out_trade_no
     * @return $this
     */
    public function setOutOrderNo($out_trade_no)
    {
        $this->out_trade_no = $out_trade_no;
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
        WxPayConfig::$APPID = $this->app_id;
        WxPayConfig::$KEY = $this->key;
        WxPayConfig::$MCHID = $this->mch_id;
        WxPayConfig::$APPSECRET = $this->app_secret;
        $tools = new WxPayJsApiPay();
        $openid = $tools->GetOpenid();

        if ($openid) {
            return $this->pay_common_obj->alertInfo(0, '', $openid);
        }

        return $this->pay_common_obj->alertInfo(1, '获取微信openid失败');
    }

    /**
     * jsAPI支付
     *
     * @param $open_id
     * @return array
     * @throws WxPayException
     */
    public function jsAPIPay($open_id)
    {
        if (empty($open_id)) {
            return $this->pay_common_obj->alertInfo(1, '微信授权ID获取失败');
        }
        // 加载配置文件
        WxPayConfig::$APPID = $this->app_id;
        WxPayConfig::$KEY = $this->key;
        WxPayConfig::$MCHID = $this->mch_id;
        WxPayConfig::$APPSECRET = $this->app_secret;
        /* -----------------------请求参数--------------------------- */
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        if (empty($this->order_sn)) {
            return $this->pay_common_obj->alertInfo(1, '商家订单号不能为空');
        }
        // 订单摘要信息，必填
        if (empty($this->subject)) {
            return $this->pay_common_obj->alertInfo(1, '订单摘要信息错误');
        }
        // 付款金额，必填
        $total_fee = $this->pay_money;
        if (!is_numeric($total_fee) || $total_fee <= 0) {
            return $this->pay_common_obj->alertInfo(1, '支付金额错误');
        }
        $total_fee = $total_fee * 100;
        // ②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($this->body);
        $input->SetAttach($this->order_sn);
        $input->SetOut_trade_no($this->out_trade_no);
        $input->SetTotal_fee($total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 3600 * 24));
        $input->SetNotify_url(WxPayConfig::NOTIFY_URL);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($open_id);
        $order = WxPayApi::unifiedOrder($input, 20);
        if (!isset($order['return_code']) || $order['return_code'] == 'FAIL') {
            return $this->pay_common_obj->alertInfo(1, $order['return_msg']);
        }
        $tools = new WxPayJsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($order);

        return $this->pay_common_obj->alertInfo(0, '', $jsApiParameters);
    }

    /**
     * 扫码支付
     */
    public function nativePay()
    {
        // 加载配置文件
        WxPayConfig::$APPID = $this->app_id;
        WxPayConfig::$KEY = $this->key;
        WxPayConfig::$MCHID = $this->mch_id;
        WxPayConfig::$APPSECRET = $this->app_secret;
        /* -----------------------请求参数--------------------------- */
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        if (empty($this->order_sn)) {
            return $this->pay_common_obj->alertInfo(1, '商家订单号不能为空');
        }
        // 订单摘要信息，必填
        if (empty($this->subject)) {
            return $this->pay_common_obj->alertInfo(1, '订单摘要信息错误');
        }
        // 付款金额，必填
        $total_fee = $this->pay_money;
        if (!is_numeric($total_fee) || $total_fee <= 0) {
            return $this->pay_common_obj->alertInfo(1, '支付金额错误');
        }
        $total_fee = $total_fee * 100;
        // ②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($this->body);
        $input->SetAttach($this->order_sn);
        $input->SetOut_trade_no($this->out_trade_no);
        $input->SetTotal_fee($total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 3600 * 24));
        $input->SetNotify_url(WxPayConfig::NOTIFY_URL);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($this->order_sn);
        $notify = new WxPayNativePay();
        $result = $notify->GetPayUrl($input);
        if ($result['return_code'] !== 'SUCCESS' || $result['result_code'] !== 'SUCCESS' || !isset($result['code_url'])) {
            return $this->pay_common_obj->alertInfo(1, isset($result['err_code_des']) ? $result['err_code_des'] : $result['return_msg']);
        }
        $url = $result["code_url"];

        return $this->pay_common_obj->alertInfo(0, '', $url);
    }
    /**
     * 查询订单支付状态
     *
     * @param string $trade_no
     * @param string $out_trade_no
     * @return array
     */
    public function queryOrder($trade_no = '', $out_trade_no = '')
    {
        WxPayConfig::$APPID = $this->app_id;
        WxPayConfig::$KEY = $this->key;
        WxPayConfig::$MCHID = $this->mch_id;
        WxPayConfig::$APPSECRET = $this->app_secret;
        $input = new WxPayOrderQuery();
        if (!empty($trade_no)) {
            $input->SetTransaction_id($trade_no);
        }
        if (!empty($out_trade_no)) {
            $input->SetOut_trade_no($out_trade_no);
        }
        $trade_info = WxPayApi::orderQuery($input, 60);
        $data['trade_info'] = $trade_info;
        if (!isset($trade_info['return_code'])
            || $trade_info['return_code'] != 'SUCCESS'
            || !isset($trade_info['result_code'])
            || $trade_info['result_code'] != 'SUCCESS'
            || !isset($trade_info['trade_state'])
            || ($trade_info['trade_state'] != 'SUCCESS' && $trade_info['trade_state'] != 'REFUND')
        ) {
            return $this->pay_common_obj->alertInfo(1, '查询失败', $data);
        }
        $dk_total_money = round($trade_info['total_fee'] / 100, 2);
        $data['dk_total_money'] = $dk_total_money;
        $data['trade_no'] = $trade_info['transaction_id'];

        return $this->pay_common_obj->alertInfo(0, '', $data);
    }

    /**
     * 支付异步回调
     *
     * @param array $trade_info
     * @return array
     */
    public function NotifyProcess($trade_info)
    {
        // 微信交易号
        $trade_no = $trade_info["transaction_id"];
        // 商户订单号
        $out_trade_no = $trade_info['out_trade_no'];
        // 支付金额
        $total_fee = $trade_info['total_fee'];
        $pay_money = round($total_fee / 100, 2);
        // 系统单号
        // 向第三方平台查询交易单据
        $trade_info_res = $this->queryOrder($trade_no, $out_trade_no);
        $data['res'] = $trade_info_res;
        if (!isset($trade_info_res['code']) || $trade_info_res['code'] !== 0) {
            return $this->pay_common_obj->alertInfo(1, $trade_info_res['msg'], $data);
        }
        $trade_info = $trade_info_res['data']['trade_info'];
        if (!$this->pay_common_obj->floatEq($pay_money, $trade_info_res['data']['dk_total_money'])) {
            return $this->pay_common_obj->alertInfo(1, '支付金额回传异常！', $data);
        }
        //支付失败处理
        if ($trade_info['return_code'] != 'SUCCESS' || $trade_info['result_code'] != 'SUCCESS') {
            return $this->pay_common_obj->alertInfo(1, '支付失败！', $data);
        }
        // 支付成功处理todo

        return $this->pay_common_obj->alertInfo(0, '成功！', $data);
    }

    /**
     * 支付同步回调
     *
     * @param array $out_trade_no
     * @return array
     */
    public function syncNotifyProcess($out_trade_no)
    {
        if (empty($out_trade_no)) {
            return $this->pay_common_obj->alertInfo(1, '外部单号为空');
        }
        $data['trade_no'] = $out_trade_no;
        // 向第三方平台查询交易单据
        $trade_info_res = $this->queryOrder('', $out_trade_no);
        $data['res'] = $trade_info_res;
        if (!isset($trade_info_res['code']) || $trade_info_res['code'] !== 0) {
            return $this->pay_common_obj->alertInfo(1, $trade_info_res['msg'], $data);
        }
        $trade_info = $trade_info_res['data']['trade_info'];
        //支付失败处理
        if ($trade_info['return_code'] != 'SUCCESS' || $trade_info['result_code'] != 'SUCCESS') {
            return $this->pay_common_obj->alertInfo(1, '支付失败！', $data);
        }
        // 支付成功处理todo

        return $this->pay_common_obj->alertInfo(0, '成功！', $trade_info);
    }
}