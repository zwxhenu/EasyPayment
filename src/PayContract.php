<?php
namespace EasyPayment\payment;

interface PayContract
{
    /**
     * 支付结果处理对象
     * @param $obj
     * @return mixed
     */
//    public function setHandleObj($obj);

    /**
     * 是否wap支付
     * @param $is_wap
     * @return mixed
     */
    public function setIsWap($is_wap);

    /**
     * 交易类型
     * @param $trade_type
     * @return mixed
     */
    public function setTradeType($trade_type);

    /**
     * 设置成功跳转地址
     * @param $success_url
     * @return mixed
     */
    public function setSuccessUrl($success_url);

    /**
     * 设置失败跳转地址
     * @param $error_url
     * @return mixed
     */
    public function setErrorUrl($error_url);

    /**
     * 订单号
     * @param $order_sn
     * @return mixed
     */
    public function setOrderSn($order_sn);

    /**
     * 支付金额
     * @param $pay_money
     * @return mixed
     */
    public function setPayMoney($pay_money);

    /**
     * 支付摘要
     * @param $subject
     * @return mixed
     */
    public function setSubject($subject);

    /**
     * 支付详情
     * @param $body
     * @return mixed
     */
    public function setBody($body);

    /**
     * 支付页展示url
     * @param $show_url
     * @return mixed
     */
    public function setShowUrl($show_url);
}