<?php

namespace EasyPayment\payment\Tests;

use EasyPayment\payment\alipayService;
use EasyPayment\payment\BdpayService;
require './../vendor/autoload.php';
/*********支付宝*************/
//$pay = new AlipayService();
//$pay->setPartner('2088421749717068');
//$pay->setSellerId('2088421749717068');
//$pay->setKey('g3a99ar2vtp0l7784pqw9lh1apt0is30');
//$pay->setOrderSn('13354666');
//$pay->setPayMoney('0.01');
//$pay->setSubject('֧测试支付');
//$pay->setBody('֧测试支付');
//$pay->setSuccessUrl('http://www.baidu.com');
//$pay->setErrorUrl('http://auto.news18a.com');
//$pay->setTradeType(1);
//$res = $pay->directPay();
//echo $res['data']['content'];exit;

/**************百度钱包************/

$bd_pay = new BdpayService();
$bd_pay->setSpNo('1000432090');
$bd_pay->setSpKey('zeVw3TPfeRzcHbvDbXnYQeAujVds2A4A');
$bd_pay->setOrderSn('123456789');
$bd_pay->setPayMoney('0.01');
$bd_pay->setSubject('֧测试支付');
$bd_pay->setBody('֧测试支付');
$bd_pay->setSuccessUrl('http://www.baidu.com');
$bd_pay->setErrorUrl('http://auto.news18a.com');
$bd_pay->setTradeType(1);
$res = $bd_pay->directPay();
var_dump($res);
echo $res['data']['html_text'];exit;