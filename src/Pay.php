<?php

namespace EasyPayment\payment;

use EasyPayment\payment\alipay\pay;

class Pay extends PayCommon
{
    /**
     * 查询单据
     * @param string $trade_no
     * @param string $out_trade_no
     * @return array|mixed
     */
    public function queryOrder($trade_no = '', $out_trade_no = '')
    {
        $trade_no = trim($trade_no);
        $out_trade_no = trim($out_trade_no);
        if (empty($trade_no) && empty($out_trade_no)) {
            return alert_info(1, '第三方支付流水号和商户订单号不能都为空');
        }
        $payorder_info = self::queryPayOrder($trade_no, $out_trade_no);
        if (empty($payorder_info) || !is_array($payorder_info)) {
            return alert_info(1, '系统中查询不到支付单据');
        }
        $pay_type_id = $payorder_info['pay_type_id'];
        $payObj = null;
        switch ($pay_type_id) {
            //支付宝支付
            case 1:
            case 3:
                $payObj = new pay();
                break;
            //微信支付
            case 2:
            case 4:
                $payObj = new wxpay();
                break;
            //百度钱包
            case 5:
            case 7:
                $payObj = new bdpay();
                break;
            //余额支付
            case 6:
                $payObj = new accountpay();
                break;
            default:
                return alert_info(1, '暂不支持此支付方式的查询');
                break;
        }
        if (!is_callable([$payObj, 'queryOrder'])) {
            return alert_info(1, '暂不支持此支付方式的查询');
        }
        return call_user_func_array([$payObj, 'queryOrder'], [$trade_no, $out_trade_no]);
    }


    /**
     * 支付单据
     * @param string $trade_no
     * @param string $out_trade_no
     * @return array|mixed
     */
    public function payOrder($trade_no = '', $out_trade_no = '')
    {
        $query_res = self::queryOrder($trade_no, $out_trade_no);
        if (!isset($query_res['code']) || $query_res['code'] !== 0) {
            return $query_res;
        }
        $trade_no = $query_res['data']['trade_no'];
        $pay_money = $query_res['data']['dk_total_money'];
        $payorder_info = $query_res['data']['payorder_info'];
        $order_sn = $payorder_info['order_sn'];
        $out_trade_no = $payorder_info['out_trade_no'];
        $pay_res = self::payOrderSuccess($out_trade_no, $trade_no, $order_sn, $pay_money);
        $PayLogModel = new PayLog();
        $PayLogModel->payLog($trade_no, $pay_res, '主动调用支付');
        return $pay_res;
    }


    /**
     * 支付失败处理
     * @param $out_trade_no
     * @param $pay_result
     * @return array
     */
    public function payOrderError($out_trade_no, $pay_result)
    {
        $out_trade_no = trim($out_trade_no);
        if (empty($out_trade_no)) {
            return alert_info(1, '商家单号不能为空');
        }
        // 查询交易单据是否存在
        $PayOrderModel = new PayOrder();
        $payorder_info = $PayOrderModel->getOneByOutTradeNo($out_trade_no);
        if (empty($payorder_info)) {
            return alert_info(1, '交易单据不存在');
        }
        $data = [
            'pay_result' => $pay_result,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $PayOrderModel->getConnectionTable()->where('id', $payorder_info->id)->where('trade_no', '')->update($data);
        self::handle($out_trade_no);
        return alert_info(0, '操作成功');
    }

    /**
     * 支付处理
     *
     * @param string $out_trade_no 商户单号
     * @param string $trade_no 交易流水号
     * @param string $order_sn 订单号
     * @param string $pay_money 支付金额
     * @return array
     */
    public function payOrderSuccess($out_trade_no, $trade_no, $order_sn, $pay_money)
    {
        $PayOrderModel = new PayOrder();
        $out_trade_no = trim($out_trade_no);
        $order_sn = trim($order_sn);
        if (empty($trade_no)) {
            return alert_info(1, '交易流水号不能为空');
        }
        if (empty($out_trade_no)) {
            return alert_info(1, '商家单号不能为空');
        }
        if (empty($order_sn)) {
            return alert_info(1, '订单号不能为空');
        }
        // 查询交易单据是否存在
        $payorder_info = $PayOrderModel->getOneByOutTradeNo($out_trade_no);
        if (empty($payorder_info)) {
            return alert_info(1, '交易单据不存在');
        }
        if ($payorder_info->order_sn != $order_sn) {
            return alert_info(1, '交易单据订单号不一致');
        }
        if ($payorder_info->trade_no != '') {
            self::handle($out_trade_no);
            return alert_info(0, '交易单据已处理');
        }
        if (!float_eq($pay_money, $payorder_info->pay_money)) {
            return alert_info(1, '支付金额异常');
        }
        // 支付成功处理--更新支付单据
        $data = [
            'trade_no' => $trade_no,
            'pay_result' => 'SUCCESS',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $PayOrderModel->getConnectionTable()->where('id', $payorder_info->id)->where('trade_no', '')->update($data);
        self::handle($out_trade_no);
        return alert_info(0, '支付成功');
    }

    /**
     * 支付处理
     * @param $out_trade_no
     * @return bool
     */
    public function handle($out_trade_no)
    {
        $PayLogModel = new PayLog();
        $PayOrderModel = new PayOrder();
        $payorder_info = $PayOrderModel->getOneByOutTradeNo($out_trade_no);
        $payorder_info = obj_to_array($payorder_info);
        $handle = unserialize($payorder_info['handle']);
        if (!is_callable([$handle, 'handle'])) {
            $PayLogModel->payLog($payorder_info['out_trade_no'], $handle, 'handle不可调用');
            return false;
        }
        try {
            $res = $handle->handle($payorder_info);
        } catch (\Exception $e) {
            $PayLogModel->payLog($payorder_info['out_trade_no'], $e->getMessage(), 'handle调用失败');
            return false;
        }
        $PayLogModel->payLog($payorder_info['out_trade_no'], $res, 'handle调用成功');
        return true;
    }
}