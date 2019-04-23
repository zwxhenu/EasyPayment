<?php
/**
 * 百度钱包支付操作类
 *
 * @author dxk
 * @version 2016-07-18
 */
namespace Shark\Library\Service\pay\bdpay;

use Shark\Library\Service\pay\bdpay\lib\bdpay_sdk;
use Shark\Library\Service\pay\bdpay\lib\sp_conf;
use Shark\Library\Service\pay\PayCommon;
use Shark\Library\Service\pay\PayContract;
use Shark\Model\Account\SupplierPayType;
use Shark\Model\Service\PayOrder;

class bdpay extends PayCommon implements PayContract
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
     * 发起支付
     * @return array
     */
    public function directPay()
    {
        // 加载配置文件
        require_once(PAY_ROOT . 'bdpay/lib/bdpay_sdk.php');
        require_once(PAY_ROOT . 'bdpay/lib/bdpay_pay.cfg.php');
        $pay_config = self::getPayConfig($this->receipt_supplier_id);
        if (!is_array($pay_config)) {
            return alert_info(1, $pay_config);
        }
        $this->pay_config = $pay_config;
        sp_conf::$SP_NO = $pay_config['sp_no'];
        sp_conf::$SP_KEY = $pay_config['sp_key'];
        /* -----------------------请求参数--------------------------- */
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        if (empty($this->order_sn)) {
            return alert_info(1, '商家订单号不能为空');
        }
        $this->out_trade_no = $this->order_sn . $this->pay_type_id;
        // 订单摘要信息，必填
        if (empty($this->subject)) {
            return alert_info(1, '订单摘要信息错误');
        }
        // 付款金额，必填
        $total_money = $this->pay_money * 100;
        if (!is_numeric($total_money) || $total_money <= 0) {
            return alert_info(1, '支付金额错误');
        }
        //--------------------------发起支付
        $apiurl = sp_conf::BFB_PAY_DIRECT_NOLOGIN_URL;
        if ($this->is_wap) {
            $apiurl = sp_conf::BFB_PAY_WAP_DIRECT_NOLOGIN_URL;
        }
        /*
         * 字符编码转换，百度钱包默认的编码是GBK，商户网页的编码如果不是，请转码。涉及到中文的字段请参见接口文档 步骤 字符编码转码，转成GBK
         * $good_name = iconv("UTF-8", "GBK", urldecode($good_name)); $good_desc
         * = iconv("UTF-8", "GBK", urldecode($good_desc));
         */
        // 商户请求支付接口的表单参数，具体的表单参数各项的定义和取值参见接口文档
        $params = [
            'service_code' => sp_conf::BFB_PAY_INTERFACE_SERVICE_ID,
            'sp_no' => sp_conf::$SP_NO,
            'order_create_time' => date("YmdHis"),
            'order_no' => $this->out_trade_no,
            'goods_name' => iconv("UTF-8", "GBK", urldecode($this->subject)),
            'goods_desc' => iconv("UTF-8", "GBK", urldecode($this->body)),
            'goods_url' => $this->showUrl,
            'unit_amount' => '',
            'unit_count' => '',
            'transport_amount' => '',
            'total_amount' => $total_money,
            'currency' => sp_conf::BFB_INTERFACE_CURRENTCY,
            'buyer_sp_username' => '',
            'return_url' => sp_conf::$NOTIFY_URL,
            'page_url' => sp_conf::$RETURN_URL,
            'pay_type' => 2,  // 1 余额支付 ，2 余额支付/快捷支付/网银支付
            'bank_no' => '',
            'expire_time' => date('YmdHis', strtotime('+2 day')),
            'input_charset' => sp_conf::BFB_INTERFACE_ENCODING,
            'version' => sp_conf::BFB_INTERFACE_VERSION,
            'sign_method' => sp_conf::SIGN_METHOD_MD5,
            'extra' => $this->order_sn
        ];
        $bdpay_sdk = new bdpay_sdk();
        $order_url = $bdpay_sdk->create_baifubao_pay_order_url($params, $apiurl);
        if (false === $order_url) {
            return alert_info(1, '发起支付失败');
        }
        $order_url = "<script>window.location=\"" . $order_url . "\";</script>";
        $data = ['html_text' => $order_url, 'pay_type_id' => $this->pay_type_id, 'out_trade_no' => $this->out_trade_no, 'mchid'=>$pay_config['sp_no']];
        $this->writePayOrder();
        return alert_info(0, '', $data);
    }

    /**
     * 写入支付单据
     */
    private function writePayOrder()
    {
        $pay_data = [
            'receipt_supplier_id' => $this->receipt_supplier_id,
            'supplier_id'=>$this->supplier_id,
            'pay_config' => $this->pay_config['sp_no'],
            'pay_type_id' => $this->pay_type_id,
            'is_wap' => (int)$this->is_wap,
            'trade_type' => $this->trade_type,
            'out_trade_no' => $this->out_trade_no,
            'subject' => $this->subject,
            'pay_money' => $this->pay_money,
            'pay_ip' => ip(),
            'order_sn' => $this->order_sn,
            'handle' => $this->handle_obj,
            'success_url' => $this->success_url,
            'error_url' => $this->error_url
        ];
        $PayOrderModel = new PayOrder();
        return $PayOrderModel->writePayOrder($pay_data);
    }

    /**
     * 获取配置设置
     * @param int $receipt_supplier_id 收款商户ID
     * @return array|bool|mixed
     */
    public function getPayConfig($receipt_supplier_id)
    {
        //检测是否允许自有支付
        $check_res = self::checkAllowPay($receipt_supplier_id, self::$bd_self_pay_type_id);
        if ($check_res !== false) {
            $pay_config = $check_res;
            if (!is_array($pay_config) || empty($pay_config) || empty($pay_config['sp_no']) || empty($pay_config['sp_key'])) {
                return '商户支付信息配置错误';
            }
            $this->pay_type_id = self::$bd_self_pay_type_id;
            return $pay_config;
        }
        //检测是否允许非自有支付
        $check_res = self::checkAllowPay($receipt_supplier_id, self::$bd_pay_type_id);
        if ($check_res === false) {
            return '该商户不支持百度钱包支付';
        }
        //非自有支付 - 获取默认账户配置
        $default_config = [];
        require(PAY_ROOT . 'bdpay/lib/bdpay_pay.cfg.default.php');
        $this->receipt_supplier_id = 0;
        $this->pay_type_id = self::$bd_pay_type_id;
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
            require(PAY_ROOT . 'bdpay/lib/bdpay_pay.cfg.default.php');
            if (!empty($partner) && $default_config['sp_no'] != $partner) {
                return '该商户支付账号不匹配';
            }
            return $default_config;
        }
        //获取自有支付配置
        $SupplierPayTypeModel = new SupplierPayType($receipt_supplier_id);
        $payset_info = $SupplierPayTypeModel->getOneBySupplierIdPayTypeId($receipt_supplier_id, self::$bd_self_pay_type_id);
        if (empty($payset_info)) {
            return '获取不到支付时的配置参数';
        }
        $pay_config = json_decode($payset_info->pay_config, true);
        if (!is_array($pay_config) || empty($pay_config) || empty($pay_config['sp_no']) || empty($pay_config['sp_key'])) {
            return '该商户支付参数配置错误';
        }
        if (!empty($partner) && $pay_config['sp_no'] != $partner) {
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
        $data = ['trade_info' => [], 'payorder_info' => [], 'dk_total_money' => 0, 'trade_no' => $trade_no];
        $payorder_info = self::queryPayOrder($trade_no, $out_trade_no);
        $data['payorder_info'] = $payorder_info;
        if (empty($payorder_info)) {
            return alert_info(1, '查询不到单据信息', $data);
        }
        if (!in_array($payorder_info['pay_type_id'], [self::$bd_pay_type_id, self::$bd_self_pay_type_id])) {
            return alert_info(1, '单据支付方式信息异常', $data);
        }
        $receipt_supplier_id = $payorder_info['receipt_supplier_id'];
        $out_trade_no = $payorder_info['out_trade_no'];
        $pay_config = self::getQueryConfig($receipt_supplier_id, $payorder_info['pay_config']);
        if (empty($pay_config)) {
            return alert_info(1, '无法获取到配置文件', $data);
        }
        //查询支付信息
        require_once(PAY_ROOT . "bdpay/lib/bdpay_sdk.php");
        require_once(PAY_ROOT . "bdpay/lib/bdpay_pay.cfg.php");
        sp_conf::$SP_NO = $pay_config['sp_no'];
        sp_conf::$SP_KEY = $pay_config['sp_key'];
        $bdpay_sdk = new bdpay_sdk();
        $trade_info = $bdpay_sdk->query_baifubao_pay_result_by_order_no($out_trade_no);
        $trade_info = gbk_to_utf8($trade_info);
        $data['trade_info'] = $trade_info;
        if (empty($trade_info)) {
            return alert_info(1, '查询失败', $data);
        }
        $dk_total_money = round($trade_info['total_amount'] / 100, 2);
        $data['dk_total_money'] = $dk_total_money;
        $data['trade_no'] = $trade_info['bfb_order_no'];
        return alert_info(0, '', $data);
    }
}