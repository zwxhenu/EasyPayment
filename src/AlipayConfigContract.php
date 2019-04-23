<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2019/4/23
 * Time: 13:53
 */
namespace EasyPayment\payment;

interface AlipayConfigContract
{
    /**
     * 支付类型
     * @param $payment_type
     */
    public function setPaymentType($payment_type);

    /**
     * 访问模式 http|https
     * @param $transport
     */
    public function setTransport($transport);
    /**
     * ca证书路径地址
     * @param $cacert
     */
    public function setCacert($cacert);

    /**
     * 设置字符编码
     * @param $input_charset utf-8
     */
    public function setInputCharset($input_charset);

    /**
     * 设置签名方式
     * @param $sign_type md5
     */
    public function setSignType($sign_type);

    /**
     * 设置服务器异步通知页面路径
     * @param $notify_url
     */
    public function setNotifyUrl($notify_url);

    /**
     * 设置服务器同步通知页面路径
     * @param  $return_url
     *
     */
    public function setReturnUrl($return_url);

    /**
     * 设置交易超时时间
     * @param $it_b_pay
     */
    public function setItPay($it_b_pay);

    /**
     * 产品类型
     * pc : create_direct_pay_by_user
     * wap : alipay.wap.create.direct.pay.by.user
     * @param $service
     */
    public function setService($service);

    /**
     * 设置防钓鱼时间戳
     * @param $phishing_key
     */
    public function setPhishingKey($phishing_key);

    /**
     * 客户端的IP地址
     * @param  $invoke_ip
     */
    public function setInvokeIp($invoke_ip);

    /**
     * 设置商户合作者身份ID
     * @param $partner
     */
    public function setPartner($partner);

    /**
     * 设置收款支付宝账号
     * @param $seller_id
     */
    public function setSellerId($seller_id);

    /**
     * 设置秘钥
     * @param $key
     */
    public function setKey($key);
}