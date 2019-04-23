<?php
/**
 * 微信支付操作类
 *
 * @author dxk
 * @version 2016-04-25
 */
namespace Shark\Library\Service\pay\wxpay;

use Shark\Library\Service\pay\Pay;
use Shark\Library\Service\pay\PayCommon;
use Shark\Library\Service\pay\wxpay\lib\WxPayNotify;
use Shark\Model\Pay\PayLog;
use Shark\Model\Service\PayOrder;

require_once dirname(__FILE__) . '/../PayCommon.php';
require_once PAY_ROOT . 'wxpay/lib/WxPay.Api.php';
require_once PAY_ROOT . 'wxpay/lib/WxPay.Notify.php';

class PayNotifyCallBack extends WxPayNotify
{
    private $PayLogModel = null;
    private $PayOrderModel = null;
    private $Pay = null;

    public function __construct()
    {
        $this->PayLogModel = new PayLog();
        $this->PayOrderModel = new PayOrder();
        $this->Pay = new Pay();
        $this->PayLogModel->wxpayReturnLog('', file_get_contents('php://input'), '接收回调数据');
    }

    /**
     * 支付异步回调
     * @param array $trade_info
     * @param string $msg
     * @return array
     */
    public function NotifyProcess($trade_info, &$msg)
    {
        // 微信交易号
        $trade_no = $trade_info["transaction_id"];
        // 商户订单号
        $out_trade_no = $trade_info['out_trade_no'];
        // 交易状态
        $pay_result = $trade_info['result_code'];
        // 支付金额
        $total_fee = $trade_info['total_fee'];
        $pay_money = round($total_fee / 100, 2);
        // 系统单号
        $order_sn = trim($trade_info['attach']);
        $data = [];
        $data['trade_no'] = $trade_no;
        $data['res'] = $trade_info;
        $this->PayLogModel->wxpayReturnLog($trade_no, $data, '接收回调数据' . $msg);
        if (!array_key_exists("transaction_id", $trade_info)) {
            return self::tipInfo(1, '输入参数不正确', $data);
        }
        if (empty($out_trade_no) || empty($trade_no) || $pay_money <= 0 || empty($order_sn)) {
            return self::tipInfo(1, '回传数据异常', $data);
        }
        // 查询系统记录的单据
        $payorder_info = PayCommon::queryPayOrder($trade_no, $out_trade_no);
        $data['res'] = $payorder_info;
        if (empty($payorder_info)) {
            return self::tipInfo(1, '查询不到单据', $data);
        }
        // 判断回传金额
        if (!float_eq($pay_money, $payorder_info['pay_money'])) {
            return self::tipInfo(1, '支付金额回传异常', $data);
        }
        // 向第三方平台查询交易单据
        $wxpay = new wxpay();
        $trade_info_res = $wxpay->queryOrder($trade_no, $out_trade_no);
        $data['res'] = $trade_info_res;
        if (!isset($trade_info_res['code']) || $trade_info_res['code'] !== 0) {
            return self::tipInfo(1, $trade_info_res['msg'], $data);
        }
        $trade_info = $trade_info_res['data']['trade_info'];
        if (!float_eq($pay_money, $trade_info_res['data']['dk_total_money'])) {
            return self::tipInfo(1, '支付金额回传异常！', $data);
        }
        //支付失败处理
        if ($trade_info['return_code'] != 'SUCCESS' || $trade_info['result_code'] != 'SUCCESS') {
            $this->Pay->payOrderError($out_trade_no, $pay_result);
            return self::tipInfo(1, '支付失败！', $data);
        }
        // 支付成功处理
        $res = $this->Pay->payOrderSuccess($out_trade_no, $trade_no, $order_sn, $pay_money);
        $data['res'] = $res;
        if (!isset($res['code']) || $res['code'] !== 0) {
            return self::tipInfo(1, $res['msg'], $data);
        }
        return self::tipInfo(0, $res['msg'], $data);
    }

    /**
     * 支付同步回调
     * @param array $out_trade_no
     * @return array
     */
    public function syncNotifyProcess($out_trade_no)
    {
        if (empty($out_trade_no)) {
            return self::tipInfo(1, '外部单号为空');
        }
        $data['trade_no'] = $out_trade_no;
        // 查询系统记录的单据
        $payorder_info = PayCommon::queryPayOrder('', $out_trade_no);
        $data['res'] = $payorder_info;
        if (empty($payorder_info)) {
            return self::tipInfo(1, '查询不到单据' . $out_trade_no);
        }
        // 向第三方平台查询交易单据
        $wxpay = new wxpay();
        $trade_info_res = $wxpay->queryOrder('', $out_trade_no);
        $data['res'] = $trade_info_res;
        if (!isset($trade_info_res['code']) || $trade_info_res['code'] !== 0) {
            return self::tipInfo(1, $trade_info_res['msg'], $data);
        }
        $trade_info = $trade_info_res['data']['trade_info'];
        //支付失败处理
        if ($trade_info['return_code'] != 'SUCCESS' || $trade_info['result_code'] != 'SUCCESS') {
            $this->Pay->payOrderError($out_trade_no, $trade_info['result_code']);
            return self::tipInfo(1, '支付失败！', $data);
        }
        // 支付成功处理
        $trade_no = $trade_info['transaction_id'];
        $order_sn = trim($trade_info['attach']);
        $pay_money = $trade_info_res['dk_total_money'];
        $res = $this->Pay->payOrderSuccess($out_trade_no, $trade_no, $order_sn, $pay_money);
        $data['res'] = $res;
        if (!isset($res['code']) || $res['code'] !== 0) {
            return self::tipInfo(1, $res['msg'], $data);
        }
        return self::tipInfo(0, $res['msg'], $data);
    }

    /**
     * 信息提示
     *
     * @param $code
     * @param string $msg
     * @param array $data
     * @return bool
     */
    private function tipInfo($code, $msg = '', $data = [])
    {
        $code = (int)$code;
        $msg = trim($msg);
        $this->PayLogModel->wxpayReturnLog($data['trade_no'], $data['res'], $msg);
        if ($code !== 0) {
            return false;
        }
        return true;
    }
}