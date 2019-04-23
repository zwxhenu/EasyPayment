<?php
/**
 * 支付宝支付操作类
 *
 */
namespace EasyPayment\payment\alipay;

use EasyPayment\payment\alipay\lib\AlipaySubmit;
use EasyPayment\payment\PayContract;
use EasyPayment\payment\alipay\AlipayConfig;
use EasyPayment\payment\PayCommon;

class pay implements PayContract
{
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
    private $partner = '';
    private $key = '';
    private $seller_id = '';

    /**
     * 设置合作者身份
     *  2088421749717068
     *
     * @param $partner
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partner = trim($partner);
        return $this;
    }

    /**
     * 设置收款支付宝账号
     *
     * @param $seller_id
     * @return $this
     */
    public function setSellerId($seller_id)
    {
        $this->seller_id = (int)$seller_id;
        return $this;
    }

    /**
     * 设置秘钥
     *
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = trim($key);
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
     * 发起支付
     * @return array
     */
    public function directPay()
    {
        $service = $this->is_wap == true ? 'alipay.wap.create.direct.pay.by.user' : 'create_direct_pay_by_user';
        $alipay_config = array('partner' => $this->partner,
            'key' => $this->key,
            'seller_id' => $this->seller_id,
            'service' => $service,
            'payment_type' =>1,
            'it_b_pay' => '30m',
            'notify_url' => 'www.baidu.com',
            'return_url' => 'www.baidu.com',
            'input_charset' => 'utf-8',
            'anti_phishing_key' => "",
            'exter_invoke_ip' => "",
            'sign_type' => 'md5',
            'cacert' => __DIR__.'/lib/cacert.pem');
        /* -----------------------请求参数--------------------------- */
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        if (empty($this->order_sn)) {
            return alertInfo(1, '商家订单号不能为空');
        }
        $this->out_trade_no = $this->order_sn;
        // 订单摘要信息，必填
        if (empty($this->subject)) {
            return alertInfo(1, '订单摘要信息错误');
        }
        // 付款金额，必填
        $total_fee = $this->pay_money;
        if (!is_numeric($total_fee) || $total_fee <= 0) {
            return alertInfo(1, '支付金额错误');
        }
        /* ------------------ 构造要请求的参数数组，无需改动---------------- */
        // 其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
        // 如"参数名" => "参数值" 注：上一个参数末尾需要“,”逗号。
        $params = [
            "service" => $alipay_config['service'],
            "partner" => $alipay_config['partner'],
            "seller_id" => $alipay_config['seller_id'],
            "payment_type" => $alipay_config['payment_type'],
            "it_b_pay" => $alipay_config['it_b_pay'],
            "notify_url" => $alipay_config['notify_url'],
            "return_url" => $alipay_config['return_url'],
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            "out_trade_no" => $this->out_trade_no,
            "subject" => $this->subject,
            "total_fee" => $total_fee,
            "show_url" => $this->showUrl,
            "extra_common_param" => $this->order_sn,
            "body" => $this->body
        ];
        if ($this->is_wap === false) {
            $params['anti_phishing_key'] = $alipay_config['anti_phishing_key'];
            $params['exter_invoke_ip'] = $alipay_config['exter_invoke_ip'];
        }
        // 建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($params, "get", "确认");
    }


    /**
     * 获取配置设置
     * @param int $receipt_supplier_id 收款商户ID
     * @return array|bool|mixed
     */
    public function getPayConfig($receipt_supplier_id)
    {
        //检测是否允许自有支付
        $check_res = self::checkAllowPay($receipt_supplier_id, self::$ali_self_pay_type_id);
        if ($check_res !== false) {
            $pay_config = $check_res;
            if (!is_array($pay_config) || empty($pay_config) || empty($pay_config['partner']) || empty($pay_config['key'])) {
                return '商户支付信息配置错误';
            }
            $this->pay_type_id = self::$ali_self_pay_type_id;
            return $pay_config;
        }
        //检测是否允许非自有支付
        $check_res = self::checkAllowPay($receipt_supplier_id, self::$ali_pay_type_id);
        if ($check_res === false) {
            return '该商户不支持支付宝支付';
        }
        //非自有支付 - 获取默认账户配置
        $default_config = [];
        require(PAY_ROOT . 'alipay/lib/aliconfig.default.account.php');
        $this->receipt_supplier_id = 0;
        $this->pay_type_id = self::$ali_pay_type_id;
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
            require(PAY_ROOT . 'alipay/lib/aliconfig.default.account.php');
            if (!empty($partner) && $default_config['partner'] != $partner) {
                return '该商户支付账号不匹配';
            }
            return $default_config;
        }
        //获取自有支付配置
        $SupplierPayTypeModel = new SupplierPayType($receipt_supplier_id);
        $payset_info = $SupplierPayTypeModel->getOneBySupplierIdPayTypeId($receipt_supplier_id, self::$ali_self_pay_type_id);
        if (empty($payset_info)) {
            return '获取不到支付时的配置参数';
        }
        $pay_config = json_decode($payset_info->pay_config, true);
        if (!is_array($pay_config) || empty($pay_config) || empty($pay_config['partner']) || empty($pay_config['key'])) {
            return '该商户支付参数配置错误';
        }
        if (!empty($partner) && $pay_config['partner'] != $partner) {
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
            return alertInfo(1, '查询不到单据信息', $data);
        }
        if (!in_array($payorder_info['pay_type_id'], [self::$ali_pay_type_id, self::$ali_self_pay_type_id])) {
            return alertInfo(1, '单据支付方式信息异常', $data);
        }
        $receipt_supplier_id = $payorder_info['receipt_supplier_id'];
        $out_trade_no = $payorder_info['out_trade_no'];
        $pay_config = self::getQueryConfig($receipt_supplier_id, $payorder_info['pay_config']);
        if (empty($pay_config)) {
            return alertInfo(1, '无法获取到配置文件', $data);
        }
        //查询支付信息
        require(PAY_ROOT . "alipay/lib/aliconfig.direct.php");
        require_once(PAY_ROOT . "alipay/lib/alipay_submit.class.php");
        $alipay_config['partner'] = $pay_config['partner'];
        $alipay_config['key'] = $pay_config['key'];
        $alipay_config['seller_id'] = $pay_config['partner'];
        $parameter = [
            "service" => "single_trade_query",
            "partner" => trim($alipay_config['partner']),
            "trade_no" => $trade_no,  // 支付宝交易流水号
            "out_trade_no" => $out_trade_no,  // 商户网站订单系统中唯一订单号，必填
            "_input_charset" => trim(strtolower($alipay_config['input_charset']))
        ];
        // 建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestHttp($parameter);
        $trade_info = json_decode(json_encode(simplexml_load_string($html_text)), true);
        $data['trade_info'] = $trade_info;
        if (empty($trade_info) || !is_array($trade_info) || $trade_info['is_success'] != 'T') {
            return alertInfo(1, '查询失败，查询结果为空' . $trade_info['error'], $data);
        }
        $dk_total_money = round($trade_info['total_fee'], 2);
        $data['dk_total_money'] = $dk_total_money;
        $data['trade_no'] = $trade_info['trade_no'];
        return alertInfo(0, '', $data);
    }
}