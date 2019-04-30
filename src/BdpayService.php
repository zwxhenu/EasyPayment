<?php
/**
 * 百度钱包支付操作类
 */
namespace EasyPayment\payment;

use EasyPayment\payment\bdpay\lib\BdpaySdk;
use EasyPayment\payment\PayContract;
use EasyPayment\payment\bdpay\lib\BdpayConfig;
use EasyPayment\payment\PayCommon;

class BdpayService implements PayContract
{
    /**
     * 商户订单流水号
     *
     * @var string
     */
    private $out_trade_no = '';
    /**
     * 支付金额
     *
     * @var int
     */
    private $pay_money = 0;
    /**
     * 描述
     * @var string
     */
    private $subject = '';
    /**
     * 商品简介
     *
     * @var string
     */
    private $body = '';
    /**
     * 商品详情页
     *
     * @var string
     */
    private $showUrl = '';
    /**
     * 交易类型
     *
     * @var int
     */
    private $trade_type = 0;
    /**
     * 交易订单号
     *
     * @var string
     */
    private $order_sn = '';
    /**
     * 支付成功回调地址
     *
     * @var string
     */
    private $success_url = '';
    /**
     * 支付失败回调地址
     *
     * @var string
     */
    private $error_url = '';
    /**
     * 是否是wap  true为wap false 为PC
     *
     * @var bool
     */
    private $is_wap = false;
    /**
     * 商户ID
     *
     * @var string
     */
    private $sp_no = '';
    /**
     * 商户秘钥
     *
     * @var string
     */
    private $sp_key = '';
    /**
     * 后台通知请求方式 1 get 2 post 默认post
     *
     * @var int
     */
    private $method = 2;
    /**
     * 退款金额,以分为单位
     *
     * @var string
     */
    private $cash_back_amount = '';
    /**
     * 退款时间
     *
     * @var string
     */
    private $cash_back_time = '';
    /**
     * 退款类型 1 退回钱包余额 2 原路退回
     * 注：若指定退至钱包余额，但交易为纯网关交易，则自动更改为原路退回。实际退款类型在同步返回结果及退款通知中体现
     *
     * @var int
     */
    private $refund_type = 2;
    /**
     * 分润退款参数
     *
     * @var string
     */
    private $refund_profit_solution = '';
    private $pay_common_obj = null;

    public function __construct()
    {
        $this->pay_common_obj = new PayCommon();
    }

    /**
     * 设置退款金额
     *
     * @param $cash_back_amount
     * @return $this
     */
    public function setCashBackAmount($cash_back_amount)
    {
        $this->cash_back_amount = $cash_back_amount;

        return $this;
    }
    /**
     * 设置退款时间
     *
     * @param $cash_back_time 格式 YYYYMMDDHHMMSS
     * @return $this
     */
    public function setCashBackTime($cash_back_time)
    {
        $this->cash_back_time = $cash_back_time;

        return $this;
    }

    /**
     * 设置退款类型 1退款到钱包余额 2 为原路退回
     * 注：若指定退至钱包余额，但交易为纯网关交易，则自动更改为原路退回。实际退款类型在同步返回结果及退款通知中体现。
     *
     * @param $refund_type
     * @return $this
     */
    public function setRefundType($refund_type)
    {
        $this->refund_type = $refund_type;

        return $this;
    }

    /**
     * 设置分润退款
     *
     * @param $refund_profit_solution
     * @return $this
     */
    public function setRefundProfitSolution($refund_profit_solution)
    {
        $this->refund_profit_solution = $refund_profit_solution;

        return $this;
    }
    /**
     * 后台通知请求方式 1 get 2 post 默认post
     *
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * 设置商户订单流水号
     *
     * @param $out_trade_no
     * @return $this
     */
    public function setOutTradeNo($out_trade_no)
    {
        $this->out_trade_no = $out_trade_no;

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
     * @param string $sp_no
     * @return $this
     */
    public function setSpNo($sp_no)
    {
        $this->sp_no = $sp_no;

        return $this;
    }

    /**
     * @param string $sp_key
     * @return $this
     */
    public function setSpKey($sp_key)
    {
        $this->sp_key = $sp_key;

        return $this;
    }

    /**
     * 发起支付
     *
     * @return array
     */
    public function directPay()
    {
        // 加载配置文件
        BdpayConfig::$SP_NO = $this->sp_no;
        BdpayConfig::$SP_KEY = $this->sp_key;
        BdpayConfig::$NOTIFY_URL = $this->success_url;
        BdpayConfig::$RETURN_URL = $this->success_url;
        /* -----------------------请求参数--------------------------- */
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        if (empty($this->order_sn)) {
            return $this->pay_common_obj->alertInfo(1, '商家订单号不能为空');
        }
        $this->out_trade_no = $this->order_sn . $this->pay_type_id;
        // 订单摘要信息，必填
        if (empty($this->subject)) {
            return $this->pay_common_obj->alertInfo(1, '订单摘要信息错误');
        }
        // 付款金额，必填
        $total_money = $this->pay_money * 100;
        if (!is_numeric($total_money) || $total_money <= 0) {
            return $this->pay_common_obj->alertInfo(1, '支付金额错误');
        }
        //--------------------------发起支付
        $apiurl = BdpayConfig::BFB_PAY_DIRECT_NOLOGIN_URL;
        if ($this->is_wap) {
            $apiurl = BdpayConfig::BFB_PAY_WAP_DIRECT_NOLOGIN_URL;
        }
        /*
         * 字符编码转换，百度钱包默认的编码是GBK，商户网页的编码如果不是，请转码。涉及到中文的字段请参见接口文档 步骤 字符编码转码，转成GBK
         * $good_name = iconv("UTF-8", "GBK", urldecode($good_name)); $good_desc
         * = iconv("UTF-8", "GBK", urldecode($good_desc));
         */
        // 商户请求支付接口的表单参数，具体的表单参数各项的定义和取值参见接口文档
        $params = array(
            'service_code' => BdpayConfig::BFB_PAY_INTERFACE_SERVICE_ID,
            'sp_no' => BdpayConfig::$SP_NO,
            'order_create_time' => date("YmdHis"),
            'order_no' => $this->out_trade_no,
            'goods_name' =>  $this->subject,
            'goods_desc' =>  $this->body,
            'goods_url' => $this->showUrl,
            'unit_amount' => '',
            'unit_count' => '',
            'transport_amount' => '',
            'total_amount' => $total_money,
            'currency' => BdpayConfig::BFB_INTERFACE_CURRENTCY,
            'buyer_sp_username' => '',
            'return_url' => BdpayConfig::$NOTIFY_URL,
            'page_url' => BdpayConfig::$RETURN_URL,
            'pay_type' => 2,  // 1 余额支付 ，2 余额支付/快捷支付/网银支付
            'bank_no' => '',
            'expire_time' => date('YmdHis', strtotime('+2 day')),
            'input_charset' => BdpayConfig::BFB_INTERFACE_ENCODING,
            'version' => BdpayConfig::BFB_INTERFACE_VERSION,
            'sign_method' => BdpayConfig::SIGN_METHOD_MD5,
            'extra' => $this->order_sn
        );
        $bdpay_sdk = new BdpaySdk();
        $order_url = $bdpay_sdk->createBaifubaoPayOrderUrl($params, $apiurl);
        if (false === $order_url) {
            return $this->pay_common_obj->alertInfo(1, '发起支付失败');
        }
        $order_url = "<script>window.location=\"" . $order_url . "\";</script>";
        $data = array('html_text' => $order_url, 'pay_type_id' => $this->pay_type_id, 'out_trade_no' => $this->out_trade_no, 'mchid'=>$this->sp_no);

        return $this->pay_common_obj->alertInfo(0, '', $data);
    }

    /**
     * 支付同步回调
     */
    public function payReturn()
    {
        $res = $this->notify();

        if (isset($res['data']['error_url'])) {
            if (isset($res['code']) && $res['code'] === 0) {
                //拼接参数
                $trade_no = $res['data']['trade_no'];
                $success_url = $res['data']['success_url'];
                $jump_url = strstr($success_url, '?') ? $success_url . '&trade_no=' . $trade_no : $success_url . '?trade_no=' . $trade_no;
            } else {
                $jump_url = $res['data']['error_url'];
            }
        }

        return $this->pay_common_obj->alertInfo($res['code'], $res['msg'], array('url' => $jump_url));
    }

    /**
     * 支付异步回调
     */
    public function payNotify()
    {
        $res = $this->notify();

        return $this->pay_common_obj->alertInfo($res['code'], $res['msg']);
    }

    /**
     * 支付回调处理
     * @return array
     */
    private function notify()
    {
        // 商户订单号
        $out_trade_no = trim($_GET['order_no']);
        // 百度钱包交易号
        $trade_no = trim($_GET['bfb_order_no']);
        // 交易状态
        //$pay_result = trim($_GET['pay_result']);
        // 支付金额
        $pay_money = (float)$_GET['total_amount'];
        $pay_money = round($pay_money / 100, 2);
        // 系统单号
        //$order_sn = trim($_GET['extra']);
        $data = array();
        $data['trade_no'] = $trade_no;
        $pay_success_url = base64_decode($this->success_url);
        $pay_error_url = base64_decode($this->error_url);
        $data['error_url'] = $pay_error_url;
        $data['success_url'] = $pay_success_url;
        // 验证签名
        BdpayConfig::$SP_NO = $this->sp_no;
        BdpayConfig::$SP_KEY = $this->sp_key;
        $bdpay_sdk = new BdpaySdk();
        $check = $bdpay_sdk->checkBaifubaoPayResultNotify();
        if ($check === false) {
            return $this->pay_common_obj->alertInfo(1, '支付校验失败!', $data);
        }

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
        if ($trade_info['pay_result'] != 1) {
            return $this->pay_common_obj->alertInfo(1, '支付失败！', $data);
        }

        return $this->pay_common_obj->alertInfo(0, '支付成功', $data);
    }

    /**
     * 查询订单支付状态
     * @param string $out_trade_no
     * @return array
     */
    public function queryOrder($out_trade_no)
    {
        // 查询交易单据是否存在
        BdpayConfig::$SP_NO = $this->sp_no;
        BdpayConfig::$SP_KEY = $this->sp_key;
        $bdpay_sdk = new BdpaySdk();
        $trade_info = $bdpay_sdk->queryBaifubaoPayResultByOrderNo($out_trade_no);
        $trade_info = $this->pay_common_obj->gbkToUtf8($trade_info);
        $data['trade_info'] = $trade_info;

        if (empty($trade_info)) {
            return $this->pay_common_obj->alertInfo(1, '查询失败', $data);
        }

        $dk_total_money = round($trade_info['total_amount'] / 100, 2);
        $data['dk_total_money'] = $dk_total_money;
        $data['trade_no'] = $trade_info['bfb_order_no'];

        return $this->pay_common_obj->alertInfo(0, '成功！', $data);
    }

    /**
     * 发起退款
     *
     * @return array
     */
    public function orderRefund()
    {
        // 加载配置文件
        BdpayConfig::$SP_NO = $this->sp_no;
        BdpayConfig::$SP_KEY = $this->sp_key;
        BdpayConfig::$NOTIFY_URL = $this->success_url;
        BdpayConfig::$RETURN_URL = $this->success_url;
        $params = array (
            'service_code' => BdpayConfig::BFB_REFUND_INTERFACE_SERVICE_ID,
            'input_charset' => BdpayConfig::BFB_INTERFACE_ENCODING,
            'sign_method' => BdpayConfig::SIGN_METHOD_MD5,
            'output_type' => BdpayConfig::BFB_INTERFACE_OUTPUT_FORMAT,
            'output_charset' => BdpayConfig::BFB_INTERFACE_ENCODING,
            'return_url' => BdpayConfig::$NOTIFY_URL,
            'return_method' => $this->method,
            'version' =>  BdpayConfig::BFB_INTERFACE_VERSION,
            'sp_no' => BdpayConfig::SP_NO,
            'order_no'=>$this->order_sn,
            'cashback_amount' => $this->cashback_amount,
            'cashback_time' => $this->cashback_time,
            'currency' => BdpayConfig::BFB_INTERFACE_CURRENTCY,
            'sp_refund_no' => $this->out_trade_no
        );
        $bdpay_sdk = new BdpaySdk();
        $refund_url = $bdpay_sdk->createBaiFuBaoRefundUrl($params, BdpayConfig::BFB_REFUND_URL);

        return $this->pay_common_obj->alertInfo(0, '成功！', array('refund_url' => $refund_url));
    }

    /**
     * 退款支付回调处理
     *
     * @return array
     */
    public function refundNotify()
    {
        $bdpay_sdk = new BdpaySdk();
        $notify_res = $bdpay_sdk->checkBaifubaoRefundResultNotify();

        if(false === $notify_res){
            return $this->pay_common_obj->alertInfo(1, '失败！');
        }
        /**
         * 处是商户收到百度钱包退款结果通知后需要做的自己的具体业务逻辑，比如修改订单状态之类的。 只有当商户收到百度钱包退款 结果通知后，
         * 所有的预处理工作都返回正常后，才执行该部分
         * todo
         */
        // 向百度钱包发起回执
        $bdpay_sdk->notifyBaifubao();
        return $this->pay_common_obj->alertInfo(0, '成功！');
    }

    /**
     * 根据百度钱包退款流水号查询退款信息
     */
    public function queryRefundBySpRefundOn()
    {
        BdpayConfig::$SP_NO = $this->sp_no;
        $bdpay_sdk = new BdpaySdk();
        $query_res = $bdpay_sdk->queryBaifubaoRefundResultBySprefundNo($this->order_sn, $this->out_trade_no);

        return $this->pay_common_obj->alertInfo(0, '成功！', array('content' => $query_res));
    }

    /**
     * 根据百度钱包订单号查询退款信息
     *
     * @return array
     */
    public function queryRefundByOrderOn()
    {
        BdpayConfig::$SP_NO = $this->sp_no;
        $bdpay_sdk = new BdpaySdk();
        $query_res = $bdpay_sdk->queryBaifubaoRefundResultBySprefundNo($this->order_sn);

        return $this->pay_common_obj->alertInfo(0, '成功！', array('content' => $query_res));
    }
}