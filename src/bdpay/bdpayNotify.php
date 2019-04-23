<?php
/**
 * 百度支付回调
 * @author dxk
 * @version 2016-07-26
 */
namespace Shark\Library\Service\pay\bdpay;

use Shark\Library\Service\pay\bdpay\lib\bdpay_sdk;
use Shark\Library\Service\pay\bdpay\lib\sp_conf;
use Shark\Library\Service\pay\Pay;
use Shark\Library\Service\pay\PayCommon;
use Shark\Model\Pay\PayLog;
use Shark\Model\Service\PayOrder;
use Input;

class bdpayNotify extends PayCommon
{
    private $PayLogModel = null;
    private $PayOrderModel = null;
    private $Pay = null;

    public function __construct()
    {
        $this->PayLogModel = new PayLog();
        $this->PayOrderModel = new PayOrder();
        $this->Pay = new Pay();
    }

    /**
     * 支付同步回调
     */
    public function payReturn()
    {
        $res = self::notify();
        $this->PayLogModel->bdpayReturnLog($res['data']['trade_no'], $res['data']['res'], $res['msg']);
        if (isset($res['data']['error_url'])) {
            if (isset($res['code']) && $res['code'] === 0) {
                //拼接参数
                $trade_no = $res['data']['trade_no'];
                $success_url = $res['data']['success_url'];
                $url = strstr($success_url, '?') ? $success_url . '&trade_no=' . $trade_no : $success_url . '?trade_no=' . $trade_no;
                header('Location: ' . $url);
            } else {
                header('Location: ' . $res['data']['error_url']);
            }
        } else {
            die($res['msg']);
        }
    }

    /**
     * 支付异步回调
     */
    public function payNotify()
    {
        $res = self::notify();
        $this->PayLogModel->bdpayReturnLog($res['data']['trade_no'], $res['data']['res'], $res['msg']);
        if (!isset($res['code']) || $res['code'] !== 0) {
            die('fail');
        } else {
            die('success');
        }
    }

    /**
     * 支付回调处理
     * @return array
     */
    private function notify()
    {
        // 商户订单号
        $out_trade_no = Input::get('order_no');
        // 百度钱包交易号
        $trade_no = Input::get('bfb_order_no');
        // 交易状态
        $pay_result = Input::get('pay_result');
        // 支付金额
        $pay_money = (float)Input::get('total_amount');
        $pay_money = round($pay_money / 100, 2);
        // 系统单号
        $order_sn = Input::get('extra');
        $data = [];
        $data['trade_no'] = $trade_no;
        $this->PayLogModel->bdpayReturnLog($trade_no, $pay_result, '接收回调数据');
        if (empty($out_trade_no) || empty($trade_no) || $pay_money <= 0 || empty($order_sn)) {
            return alert_info(1, '回传数据异常', $data);
        }
        // 查询系统记录的单据
        $payorder_info = self::queryPayOrder($trade_no, $out_trade_no);
        $data['res'] = $payorder_info;
        if (empty($payorder_info)) {
            return alert_info(1, '查询不到单据', $data);
        }
        $pay_success_url = base64_decode($payorder_info['success_url']);
        $pay_error_url = base64_decode($payorder_info['error_url']);
        $data['error_url'] = $pay_error_url;
        $data['success_url'] = $pay_success_url;
        // 判断回传金额
        if (!float_eq($pay_money, $payorder_info['pay_money'])) {
            return alert_info(1, '支付金额回传异常', $data);
        }
        // 验证签名
        require_once(PAY_ROOT . "bdpay/lib/bdpay_sdk.php");
        require_once(PAY_ROOT . "bdpay/lib/bdpay_pay.cfg.php");
        $bdpay = new bdpay();
        $receipt_supplier_id = $payorder_info['receipt_supplier_id'];
        $pay_config = $bdpay->getQueryConfig($receipt_supplier_id);
        sp_conf::$SP_NO = $pay_config['sp_no'];
        sp_conf::$SP_KEY = $pay_config['sp_key'];
        $bdpay_sdk = new bdpay_sdk();
        $check = $bdpay_sdk->check_bfb_pay_result_notify();
        if ($check === false) {
            return alert_info(1, '支付校验失败!', $data);
        }
        // 向第三方平台查询交易单据
        $trade_info_res = $bdpay->queryOrder($trade_no, $out_trade_no);
        $data['res'] = $trade_info_res;
        if (!isset($trade_info_res['code']) || $trade_info_res['code'] !== 0) {
            return alert_info(1, $trade_info_res['msg'], $data);
        }
        $trade_info = $trade_info_res['data']['trade_info'];
        if (!float_eq($pay_money, $trade_info_res['data']['dk_total_money'])) {
            return alert_info(1, '支付金额回传异常！', $data);
        }
        //支付失败处理
        if ($trade_info['pay_result'] != 1) {
            $this->Pay->payOrderError($out_trade_no, $pay_result);
            return alert_info(1, '支付失败！', $data);
        }
        // 支付成功处理
        $res = $this->Pay->payOrderSuccess($out_trade_no, $trade_no, $order_sn, $pay_money);
        $data['res'] = $res;
        if (!isset($res['code']) || $res['code'] !== 0) {
            return alert_info(1, $res['msg'], $data);
        }
        return alert_info(0, $res['msg'], $data);
    }
}