<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2019/4/24
 * Time: 13:17
 */

namespace EasyPayment\payment;


interface BdpayConfigContract
{
    /**
     * 设置商户ID
     *
     * @return mixed
     */
    public function setSpNo();

    /**
     * 设置商户的百度钱包合作密钥
     *
     * @return mixed
     */
    public function setSpKey();

    /**
     * 支付成功同步回调地址
     *
     * @return mixed
     */
    public function setReturnUrl();

    /**
     * 支付成功异步回调地址
     *
     * @return mixed
     */
    public function setNotifyUrl();

}