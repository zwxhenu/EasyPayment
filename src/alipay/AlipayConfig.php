<?php
/**
 * 设置支付宝配置信息类
 */

namespace EasyPayment\payment\alipay;

use EasyPayment\payment\AlipayConfigContract;

class AlipayConfig implements AlipayConfigContract
{
    /**
     * 服务器异步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
     *
     * @var string
     */
    private $notify_url = '';

    /**
     * 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
     * @var string
     */
    private $return_url = '';
    /**
     * 签名方式
     *
     * @var string
     */
    private $sign_type = 'md5';
    /**
     * 字符编码格式 目前支持utf-8
     * @var string
     */
    private $input_charset = 'utf-8';

    /**
     * ca证书路径地址，用于curl中ssl校验 请保证cacert.pem文件在当前文件夹目录中
     * @var string
     */
    private $cacert = 'alipay/lib/cacert.pem';
    /**
     * 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
     * @var string
     */
    private $transport = 'http';
    /**
     * 支付类型 ，无需修改
     *
     * @var string
     */
    private $payment_type = '1';
    /**
     * 超时时间,设置未付款交易的超时时间，一旦超时，
     * 该笔交易就会自动被关闭.取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
     * @var string
     */
    private $it_b_pay = '30m';
    /**
     * 产品类型，无需修改 默认pc 移动端 alipay.wap.create.direct.pay.by.user
     *
     * @var string
     */
    private $service = 'create_direct_pay_by_user';

    // ↓↓↓↓↓↓↓↓↓↓ 请在这里配置防钓鱼信息，如果没开通防钓鱼功能，为空即可 ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    /**
     * 防钓鱼时间戳 若要使用请调用类文件submit中的query_timestamp函数
     *
     * @var string
     */
    private $anti_phishing_key = "";
    /**
     * 客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
     *
     * @var string
     */
    private $exter_invoke_ip = "";

    //↑↑↑↑↑↑↑↑↑↑请在这里配置防钓鱼信息，如果没开通防钓鱼功能，为空即可 ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    /**
     * 合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
     *
     * @var string
     */
    private $partner = '2088421749717068';
    /**
     * 收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
     *
     * @var string
     */
    private $seller_id = '2088421749717068';
    /**
     * MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
     *
     * @var string
     */
    private $key = 'g3a99ar2vtp0l7784pqw9lh1apt0is30';
    /**
     * 支付类型
     *
     * @param $payment_type
     * @return $this
     */
    public function setPaymentType($payment_type)
    {
        $this->payment_type = $payment_type;
        return $this;
    }

    /**
     * 访问模式 http https
     *
     * @param $transport
     * @return $this
     */
    public function setTransport($transport)
    {
        $this->transport = $transport;
        return $this;
    }
    /**
     * ca证书路径地址
     *
     * @param $cacert
     * @return $this
     */
    public function setCacert($cacert)
    {
        $this->cacert = $cacert;
        return $this;
    }

    /**
     * 设置字符编码
     *
     * @param $input_charset
     * @return $this
     */
    public function setInputCharset($input_charset)
    {
        $this->input_charset = $input_charset;
        return $this;
    }

    /**
     * 设置签名方式
     *
     * @param $sign_type
     * @return $this
     */
    public function setSignType($sign_type)
    {
        $this->sign_type = $sign_type;
        return $this;
    }

    /**
     * 设置服务器异步通知页面路径
     *
     * @param $notify_url
     * @return $this
     */
    public function setNotifyUrl($notify_url)
    {
        $this->notify_url = $notify_url;
        return $this;
    }

    /**
     * 设置服务器同步通知页面路径
     *
     * @param $return_url
     * @return $this
     */
    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;
        return $this;
    }

    /**
     * 设置交易超时时间
     *
     * @param $it_b_pay
     * @return $this
     */
    public function setItPay($it_b_pay)
    {
        $this->it_b_pay = $it_b_pay;
        return $this;
    }

    /**
     * 产品类型
     * pc : create_direct_pay_by_user
     * wap : alipay.wap.create.direct.pay.by.user
     * @param $service
     * @return $this
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * 设置防钓鱼时间戳
     *
     * @param $phishing_key
     * @return $this
     */
    public function setPhishingKey($phishing_key)
    {
        $this->anti_phishing_key = $phishing_key;
        return $this;
    }

    /**
     * 客户端的IP地址
     *
     * @param $invoke_ip
     * @return $this
     */
    public function setInvokeIp($invoke_ip)
    {
        $this->exter_invoke_ip = $invoke_ip;
        return $this;
    }

    /**
     * 设置商户合作者身份ID
     *
     * @param $partner
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;
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
        $this->seller_id = $seller_id;
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
        $this->key = $key;
        return $this;
    }
}