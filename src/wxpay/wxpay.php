<?php
/**
 * 微信支付操作类
 *
 * @author dxk
 * @version 2016-07-26
 */
namespace Shark\Library\Service\pay\wxpay;

use Shark\Library\Service\pay\PayCommon;
use Shark\Library\Service\pay\PayContract;
use Shark\Library\Service\pay\wxpay\lib\JsApiPay;
use Shark\Library\Service\pay\wxpay\lib\NativePay;
use Shark\Library\Service\pay\wxpay\lib\WxPayApi;
use Shark\Library\Service\pay\wxpay\lib\WxPayConfig;
use Shark\Library\Service\pay\wxpay\lib\WxPayOrderQuery;
use Shark\Library\Service\pay\wxpay\lib\WxPayUnifiedOrder;
use Shark\Library\Service\QrCode;
use Shark\Model\Account\SupplierPayType;
use Shark\Model\Service\PayOrder;

class wxpay extends PayCommon implements PayContract
{
    private $supplier_id = 0;
    private $receipt_supplier_id = 0;
    private $pay_type_id = 0;
    private $pay_config = [];
    private $out_trade_no = '';
    private $pay_money = 0;
    private $subject = '';
    private $body = '';
    private $showUrl = '';
    private $trade_type = 0;
    private $order_sn = '';
    private $success_url = '';
    private $error_url = '';
    private $is_wap = false;
    private $handle_obj = '';

    /**
     * 设置处理支付结果的对象
     * @param $obj
     * @return $this|bool
     */
    public function setHandleObj($obj)
    {
        if (!is_callable([$obj, 'handle'])) {
            return false;
        }
        $this->handle_obj = $obj;
        return $this;
    }

    /**
     * 是否为WAP支付
     * @param $is_wap
     * @return $this
     */
    public function setIsWap($is_wap)
    {
        $this->is_wap = (bool)$is_wap;
        return $this;
    }

    /**
     * 收款方用户ID，自有支付，非自有支付不用传参
     *
     * @param int $receipt_supplier_id
     * @return $this
     */
    public function setReceiptSupplierId($receipt_supplier_id)
    {
        if (!is_numeric($receipt_supplier_id) || $receipt_supplier_id < 0) {
            return false;
        }
        $this->receipt_supplier_id = (int)$receipt_supplier_id;
        $this->supplier_id = (int)$receipt_supplier_id;
        return $this;
    }

    /**
     * 支付金额
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
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $body = trim($body);
        $body = trim_print($body);
        // body不超过60个字符
        $body = str_limit($body, 60);
        $this->body = $body;
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
     * @return array
     */
    public function getOpenid()
    {
        // 加载配置文件
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Config.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Api.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.JsApiPay.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Default.Account.php');
        $pay_config = self::getPayConfig($this->receipt_supplier_id);
        if (!is_array($pay_config)) {
            return alert_info(1, $pay_config);
        }
        $this->pay_config = $pay_config;
        WxPayConfig::$APPID = $pay_config['APPID'];
        WxPayConfig::$KEY = $pay_config['KEY'];
        WxPayConfig::$MCHID = $pay_config['MCHID'];
        WxPayConfig::$APPSECRET = $pay_config['APPSECRET'];
        $tools = new JsApiPay();
        $openid = $tools->GetOpenid();
        if ($openid) {
            return alert_info(0, '', $openid);
        }
        return alert_info(1, '获取微信openid失败');
    }

    /**
     * jsAPI支付
     * @param $open_id
     * @return array
     * @throws lib\WxPayException
     */
    public function jsAPIPay($open_id)
    {
        if (empty($open_id)) {
            return alert_info(1, '微信授权ID获取失败');
        }
        // 加载配置文件
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Config.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Api.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.JsApiPay.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Default.Account.php');
        $pay_config = self::getPayConfig($this->receipt_supplier_id);
        if (!is_array($pay_config)) {
            return alert_info(1, $pay_config);
        }
        $this->pay_config = $pay_config;
        WxPayConfig::$APPID = $pay_config['APPID'];
        WxPayConfig::$KEY = $pay_config['KEY'];
        WxPayConfig::$MCHID = $pay_config['MCHID'];
        WxPayConfig::$APPSECRET = $pay_config['APPSECRET'];
        /* -----------------------请求参数--------------------------- */
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        if (empty($this->order_sn)) {
            return alert_info(1, '商家订单号不能为空');
        }
        $this->out_trade_no = $this->createOutTradeNo($this->order_sn, $this->pay_type_id, $open_id);
        // 订单摘要信息，必填
        if (empty($this->subject)) {
            return alert_info(1, '订单摘要信息错误');
        }
        // 付款金额，必填
        $total_fee = $this->pay_money;
        if (!is_numeric($total_fee) || $total_fee <= 0) {
            return alert_info(1, '支付金额错误');
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
            return alert_info(1, $order['return_msg']);
        }
        $tools = new JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $data = [
            'jsApiParameters' => $jsApiParameters,
            'pay_type_id' => $this->pay_type_id,
            'out_trade_no' => $this->out_trade_no,
            'success_url' => $this->success_url,
            'error_url'=>$this->error_url
        ];
        $this->is_wap = true;
        $this->writePayOrder($open_id);
        return alert_info(0, '', $data);
    }


    /**
     * 扫码支付
     */
    public function nativePay()
    {
        // 加载配置文件
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Config.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Api.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.NativePay.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Default.Account.php');
        $pay_config = self::getPayConfig($this->receipt_supplier_id);
        if (!is_array($pay_config)) {
            return alert_info(1, $pay_config);
        }
        $this->pay_config = $pay_config;
        WxPayConfig::$APPID = $pay_config['APPID'];
        WxPayConfig::$KEY = $pay_config['KEY'];
        WxPayConfig::$MCHID = $pay_config['MCHID'];
        WxPayConfig::$APPSECRET = $pay_config['APPSECRET'];
        /* -----------------------请求参数--------------------------- */
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        if (empty($this->order_sn)) {
            return alert_info(1, '商家订单号不能为空');
        }
        $this->out_trade_no = $this->createOutTradeNo($this->order_sn, $this->pay_type_id, 0);
        // 订单摘要信息，必填
        if (empty($this->subject)) {
            return alert_info(1, '订单摘要信息错误');
        }
        // 付款金额，必填
        $total_fee = $this->pay_money;
        if (!is_numeric($total_fee) || $total_fee <= 0) {
            return alert_info(1, '支付金额错误');
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
        $notify = new NativePay();
        $result = $notify->GetPayUrl($input);
        if ($result['return_code'] !== 'SUCCESS' || $result['result_code'] !== 'SUCCESS' || !isset($result['code_url'])) {
            return alert_info(1, isset($result['err_code_des']) ? $result['err_code_des'] : $result['return_msg']);
        }
        $url = $result["code_url"];
        $qrcode = QrCode::make($url, 'wxpay_qrcode', 150);
        $data = [
            'qrcode' => $qrcode,
            'pay_type_id' => $this->pay_type_id,
            'out_trade_no' => $this->out_trade_no,
            'mchid' => $pay_config['MCHID']
        ];
        $this->is_wap = false;
        $this->writePayOrder(0);
        return alert_info(0, '', $data);
    }


    /**
     * 生成外部单号
     *
     * @param string $order_sn
     * @param int $pay_type_id
     * @param string $open_id
     * @return bool|string
     */
    public function createOutTradeNo($order_sn, $pay_type_id, $open_id)
    {
        if (empty($order_sn)) {
            return false;
        }
        $PayOrderModel = new PayOrder();
        $res = $PayOrderModel->getConnectionTable()->where('order_sn', $order_sn)->where('pay_type_id', $pay_type_id)->where('remark', $open_id)->first();
        if (!empty($res)) {
            return $res->out_trade_no;
        } else {
            return WxPayConfig::$MCHID . date('ymdHis');
        }
    }

    /**
     * 写入支付单据
     * @param $remark
     * @return bool
     */
    private function writePayOrder($remark)
    {
        $pay_data = [
            'receipt_supplier_id' => $this->receipt_supplier_id,
            'supplier_id'=>$this->supplier_id,
            'pay_config' => $this->pay_config['MCHID'],
            'pay_type_id' => $this->pay_type_id,
            'is_wap' => (int)$this->is_wap,
            'trade_type' => $this->trade_type,
            'out_trade_no' => $this->out_trade_no,
            'subject' => $this->subject,
            'pay_money' => $this->pay_money,
            'pay_ip' => ip(),
            'order_sn' => $this->order_sn,
            'remark' => $remark,
            'handle' => $this->handle_obj,
            'success_url' => $this->success_url,
            'error_url' => $this->error_url
        ];
        $PayOrderModel = new PayOrder();
        return $PayOrderModel->writePayOrder($pay_data);
    }

    /**
     * 获取配置设置
     * @param $receipt_supplier_id
     * @return array|bool|mixed
     */
    public function getPayConfig($receipt_supplier_id)
    {
        //检测是否允许自有支付
        $check_res = self::checkAllowPay($receipt_supplier_id, self::$wx_self_pay_type_id);
        if ($check_res !== false) {
            $pay_config = $check_res;
            if (!is_array($pay_config) || empty($pay_config) || empty($pay_config['APPID']) || empty($pay_config['KEY']) || empty($pay_config['MCHID']) || empty($pay_config['APPSECRET'])) {
                return '该商户支付参数配置错误';
            }
            $this->pay_type_id = self::$wx_self_pay_type_id;
            return $pay_config;
        }
        //检测是否允许非自有支付
        $check_res = self::checkAllowPay($receipt_supplier_id, self::$wx_pay_type_id);
        if ($check_res === false) {
            return '该商户不支持微信支付';
        }
        //非自有支付 - 获取默认账户配置
        $default_config = [];
        require(PAY_ROOT . 'wxpay/lib/WxPay.Default.Account.php');
        $this->receipt_supplier_id = 0;
        $this->pay_type_id = self::$wx_pay_type_id;
        return $default_config;
    }

    /**
     * 获取查询配置
     * @param $receipt_supplier_id
     * @param string $partner
     * @return array|bool|mixed
     */
    public function getQueryConfig($receipt_supplier_id, $partner = '')
    {
        if ($receipt_supplier_id <= 0) {
            // 获取默认账户配置
            $default_config = [];
            require(PAY_ROOT . 'wxpay/lib/WxPay.Default.Account.php');
            if (!empty($partner) && $default_config['MCHID'] != $partner) {
                return '该商户支付账号不匹配';
            }
            return $default_config;
        }
        //获取自有支付配置
        $SupplierPayTypeModel = new SupplierPayType($receipt_supplier_id);
        $payset_info = $SupplierPayTypeModel->getOneBySupplierIdPayTypeId($receipt_supplier_id, self::$wx_self_pay_type_id);
        if (empty($payset_info)) {
            return '获取不到支付时的配置参数';
        }
        $pay_config = json_decode($payset_info->pay_config, true);
        if (!is_array($pay_config) || empty($pay_config) || empty($pay_config['APPID']) || empty($pay_config['KEY']) || empty($pay_config['MCHID']) || empty($pay_config['APPSECRET'])) {
            return '该商户支付参数配置错误';
        }
        if (!empty($partner) && $pay_config['MCHID'] != $partner) {
            return '该商户支付账号不匹配';
        }
        return $pay_config;
    }

    /**
     * 查询订单支付状态
     * @param string $trade_no
     * @param string $out_trade_no
     * @return array
     */
    public function queryOrder($trade_no = '', $out_trade_no = '')
    {
        // 查询交易单据是否存在
        $data = ['trade_info' => [], 'payorder_info' => [], 'dk_total_money' => 0, 'trade_no' => ''];
        $payorder_info = self::queryPayOrder($trade_no, $out_trade_no);
        $data['payorder_info'] = $payorder_info;
        if (empty($payorder_info)) {
            return alert_info(1, '查询不到单据信息', $data);
        }
        if (!in_array($payorder_info['pay_type_id'], [self::$wx_pay_type_id, self::$wx_self_pay_type_id])) {
            return alert_info(1, '单据支付方式信息异常', $data);
        }
        $receipt_supplier_id = $payorder_info['receipt_supplier_id'];
        $out_trade_no = $payorder_info['out_trade_no'];
        $pay_config = self::getQueryConfig($receipt_supplier_id, $payorder_info['pay_config']);
        if (empty($pay_config) || !is_array($pay_config)) {
            return alert_info(1, $pay_config, $data);
        }
        //查询支付信息
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Config.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Data.php');
        require_once(PAY_ROOT . 'wxpay/lib/WxPay.Api.php');
        include(PAY_ROOT . 'wxpay/lib/WxPay.Default.Account.php');
        WxPayConfig::$APPID = $pay_config['APPID'];
        WxPayConfig::$KEY = $pay_config['KEY'];
        WxPayConfig::$MCHID = $pay_config['MCHID'];
        WxPayConfig::$APPSECRET = $pay_config['APPSECRET'];
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
            return alert_info(1, '查询失败', $data);
        }
        $dk_total_money = round($trade_info['total_fee'] / 100, 2);
        $data['dk_total_money'] = $dk_total_money;
        $data['trade_no'] = $trade_info['transaction_id'];
        return alert_info(0, '', $data);
    }
}