<?php

namespace EasyPayment\payment\Tests;

use EasyPayment\payment\alipayService;
use EasyPayment\payment\BdpayService;
use EasyPayment\payment\WxPayService;

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
//$bd_pay = new BdpayService();
//$bd_pay->setSpNo('1000432090');
//$bd_pay->setSpKey('zeVw3TPfeRzcHbvDbXnYQeAujVds2A4A');
//$bd_pay->setOrderSn('123456789');
//$bd_pay->setPayMoney('0.01');
//$bd_pay->setSubject('֧测试支付');
//$bd_pay->setBody('֧测试支付');
//$bd_pay->setSuccessUrl('http://www.baidu.com');
//$bd_pay->setErrorUrl('http://auto.news18a.com');
//$bd_pay->setTradeType(1);
//$res = $bd_pay->directPay();
//echo $res['data']['html_text'];exit;

/**************微信二维码支付************/
$wx_qrcode_pay = new WxPayService();
$wx_qrcode_pay->setMchId('1371844302');
$wx_qrcode_pay->setAppSecret('28878558efc10dcc4037e8077b0ba077');
$wx_qrcode_pay->setAppId('wxef13238200cbd24c');
$wx_qrcode_pay->setKey('d95e587f5dbab4a6007de1d228831fd4');
$wx_qrcode_pay->setOrderSn('12345689');
$wx_qrcode_pay->setOutOrderNo('4567891');
$wx_qrcode_pay->setPayMoney(0.01);
$wx_qrcode_pay->setSubject('测试二维码支付');
$wx_qrcode_pay->setBody('测试二维码支付');
$wx_qrcode_pay->setSuccessUrl('http://www.baidu.com');
$wx_qrcode_pay->setErrorUrl('http://www.baidu.com');
$wx_qrcode_pay->setTradeType(1);
$res = $wx_qrcode_pay->nativePay();
$qrcode = $res['data'];
echo $_SERVER['HTTP_HOST'].'/'.$qrcode;

/**************微信支付************/
//$wx_pay = new WxPayService();
//$wx_pay->setMchId('1371844302');
//$wx_pay->setAppSecret('28878558efc10dcc4037e8077b0ba077');
//$wx_pay->setAppId('wxef13238200cbd24c');
//$wx_pay->setKey('d95e587f5dbab4a6007de1d228831fd4');
//$wx_pay->setIsWap(true);
//$wx_pay->setOrderSn('123456789');
//$wx_pay->setPayMoney(0.01);
//$wx_pay->setSubject('测试微信支付');
//$wx_pay->setBody('测试微信支付');
//$wx_pay->setSuccessUrl('http://www.baidu.com');
//$wx_pay->setErrorUrl('http://www.baidu.com');
//$res = $wx_pay->jsAPIPay('454645'); // 微信的openid
//var_dump($res);exit;