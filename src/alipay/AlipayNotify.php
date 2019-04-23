<?php

namespace EasyPayment\payment\alipay;


class AlipayNotify
{
    public function __construct(){}

    /**
     * 支付同步回调
     */
    public function payReturn()
    {
        $res = self::notify(false);
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
        $res = self::notify(true);
        if (!isset($res['code']) || $res['code'] !== 0) {
            die('fail');
        } else {
            die('success');
        }
    }

    /**
     * 支付回调处理
     * @param bool $is_notify 是否为异步回调
     * @return array
     */
    private function notify($is_notify = false)
    {
        // 商户订单号
        $out_trade_no = trim($_GET['out_trade_no']);
        // 支付宝交易号
        $trade_no = trim($_GET['trade_no']);
        // 交易状态
        $pay_result = $_GET['trade_status'];
        // 支付金额
        $pay_money = (float)$_GET['total_fee'];
        // 系统单号
        $out_trade_no_t = explode('-', $out_trade_no);
        $order_sn = $out_trade_no_t[0];
        $data = [];
        $data['trade_no'] = $trade_no;
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
        $is_wap = (int)$payorder_info['is_wap'];
        // 判断回传金额
        if (!float_eq($pay_money, $payorder_info['pay_money'])) {
            return alert_info(1, '支付金额回传异常', $data);
        }
        // 验证签名
        require_once(PAY_ROOT . "alipay/lib/alipay_notify.class.php");
        if ($is_wap) {
            require(PAY_ROOT . "alipay/lib/aliconfig.wap.php");
        } else {
            require(PAY_ROOT . "alipay/lib/aliconfig.direct.php");
        }
        $receipt_supplier_id = $payorder_info['receipt_supplier_id'];
        $alipay = new pay();
        $pay_config = $alipay->getQueryConfig($receipt_supplier_id);
        $alipay_config['partner'] = $pay_config['partner'];
        $alipay_config['key'] = $pay_config['key'];
        $alipay_config['seller_id'] = $pay_config['partner'];
        $alipayNotify = new \Shark\Library\Service\pay\alipay\lib\AlipayNotify($alipay_config);
        if ($is_notify == true) {
            $verify_result = $alipayNotify->verifyNotify();
        } else {
            $verify_result = $alipayNotify->verifyReturn();
        }
        if (!$verify_result) {
            return alert_info(1, '支付校验失败!', $data);
        }
        // 支付失败处理
        if ($pay_result != 'TRADE_SUCCESS') {
            return alert_info(1, '支付失败！', $data);
        }
        return alert_info(0, '支付成功！', $data);
    }
}