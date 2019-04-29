<?php

namespace EasyPayment\payment\Tests;

use EasyPayment\payment\alipayService;
use EasyPayment\payment\BdpayService;
use EasyPayment\payment\WxPayService;

require './../vendor/autoload.php';
/*********支付宝*************/
//$pay = new AlipayService();
//$pay->setPartner(''); // 合作商户
//$pay->setSellerId(''); // 合作商户ID
//$pay->setKey(''); // 合作商户支付秘钥
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
//$bd_pay->setSpNo(''); // 合作商户ID
//$bd_pay->setSpKey(''); // 合作支付秘钥
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
/**
 * 微信公众号信息配置
 * APPID：绑定支付的APPID（必须配置）
 * MCHID：商户号（必须配置）
 * KEY：商户支付密钥，参考开户邮件设置（必须配置）
 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置）
 **/
$wx_qrcode_pay = new WxPayService();
// 商户号（必须配置）
$wx_qrcode_pay->setMchId('');
// 公众帐号secert（仅JSAPI支付的时候需要配置）
$wx_qrcode_pay->setAppSecret('');
// 绑定支付的APPID
$wx_qrcode_pay->setAppId('');
// KEY：商户支付密钥，参考开户邮件设置（必须配置）
$wx_qrcode_pay->setKey('');
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
//$wx_pay->setMchId('');
//$wx_pay->setAppSecret('');
//$wx_pay->setAppId('');
//$wx_pay->setKey('');
//$wx_pay->setIsWap(true);
//$wx_pay->setOrderSn('123456789');
//$wx_pay->setPayMoney(0.01);
//$wx_pay->setSubject('测试微信支付');
//$wx_pay->setBody('测试微信支付');
//$wx_pay->setSuccessUrl('http://www.baidu.com');
//$wx_pay->setErrorUrl('http://www.baidu.com');
//$res = $wx_pay->jsAPIPay('454645'); // 微信的openid
//var_dump($res);exit;