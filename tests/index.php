<?php

namespace EasyPayment\payment\Tests;

use EasyPayment\payment\alipayService;
use EasyPayment\payment\BdpayService;
use EasyPayment\payment\WxPayService;

require './../vendor/autoload.php';
/*********支付宝*************/
$pay = new AlipayService();
$pay->setPartner(''); // 合作商户
$pay->setSellerId(''); // 合作商户ID
$pay->setKey(''); // 合作商户支付秘钥
$pay->setOrderSn('13354666');
$pay->setPayMoney('0.01');
$pay->setSubject('֧测试支付');
$pay->setBody('֧测试支付');
$pay->setSuccessUrl('http://www.baidu.com');
$pay->setErrorUrl('http://auto.news18a.com');
$pay->setTradeType(1);
$res = $pay->directPay();
echo $res['data']['content'];exit;

/*******支付宝查询******/
$query_pay = new AlipayService();
$query_pay->setKey('');// 合作商户支付秘钥
$query_pay->setSellerId('');// 合作商户ID
$query_pay->setPartner('');// 合作商户
// $trade_no 支付宝交易流水号 $out_trade_no 商户网站订单系统中唯一订单号，必填
$query_res = $query_pay->queryOrder($trade_no, $out_trade_no);


/**************百度钱包************/
$bd_pay = new BdpayService();
$bd_pay->setSpNo('9000100005'); // 合作商户ID
$bd_pay->setSpKey('pSAw3bzfMKYAXML53dgQ3R4LsKp758Ss'); // 合作支付秘钥
$bd_pay->setOrderSn('123456789');
$bd_pay->setPayMoney('0.01');
$bd_pay->setSubject('֧测试支付');
$bd_pay->setBody('֧测试支付');
$bd_pay->setSuccessUrl('http://www.baidu.com');
$bd_pay->setErrorUrl('http://auto.news18a.com');
$bd_pay->setTradeType(1);
$res = $bd_pay->directPay();
echo $res['data']['html_text'];exit;

/**********百度钱包支付查询***********/
$bd_query_pay = new BdpayService();
$bd_query_pay->setSpNo('9000100005');// 合作商户ID
$bd_query_pay->setSpKey('pSAw3bzfMKYAXML53dgQ3R4LsKp758Ss');// 合作商户的支付秘钥
// $out_trade_no 商户网站订单系统中唯一订单号，必填
$bd_query_res = $bd_query_pay->queryOrder($out_trade_no);


/**************百度钱包发起退款****************/
$bd_refund_pay = new BdpayService();
$bd_refund_pay->setSpNo('9000100005');// 合作商户ID
$bd_refund_pay->setSpKey('pSAw3bzfMKYAXML53dgQ3R4LsKp758Ss');// 合作商户的支付秘钥
$bd_refund_pay->setSuccessUrl('www.baidu.com');
$bd_refund_pay->setErrorUrl('www.baidu.com');
$bd_refund_pay->setRefundType(2);// 退款类型 1 退回钱包余额 2 原路退回
$bd_refund_pay->setCashBackAmount(0.01); //退款金额
$bd_refund_pay->setCashBackTime(date('YmdHis')); // 退款请求时间
$order_sn = date("YmdHis"). sprintf ( '%06d', rand(0, 999999));
$bd_refund_pay->setOrderSn($order_sn);// 商户退款流水号
$refund_res = $bd_refund_pay->orderRefund();
echo '<pre>';
var_dump($refund_res);

/**************百度钱包查询退款结果***************/
$bd_query_refund_pay = new BdpayService();
$bd_query_refund_pay->setSpNo('9000100005');
$bd_query_refund_pay->setOrderSn('20140814173437256936'); // 百度钱包订单号
$bd_query_refund_pay->setOutTradeNo('2014081417354462'); // 百度钱包退款流水号
// 根据商户交易流水号查询
$order_sn_query_res = $bd_query_refund_pay->queryRefundByOrderOn();
// 根据退款流水号查询
$out_trade_no_res = $bd_query_refund_pay->queryRefundBySpRefundOn();


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
$wx_pay = new WxPayService();
$wx_pay->setMchId('');
$wx_pay->setAppSecret('');
$wx_pay->setAppId('');
$wx_pay->setKey('');
$wx_pay->setIsWap(true);
$wx_pay->setOrderSn('123456789');
$wx_pay->setPayMoney(0.01);
$wx_pay->setSubject('测试微信支付');
$wx_pay->setBody('测试微信支付');
$wx_pay->setSuccessUrl('http://www.baidu.com');
$wx_pay->setErrorUrl('http://www.baidu.com');
$res = $wx_pay->jsAPIPay('454645'); // 微信的openid
var_dump($res);exit;

/*************微信支付查询*************/

$wx_pay_query = new WxPayService();
$wx_pay_query->setMchId('');
$wx_pay_query->setAppSecret('');
$wx_pay_query->setAppId('');
$wx_pay_query->setKey('');
// $trade_no 微信交易流水号 $out_trade_no 商户网站订单系统中唯一订单号，必填
$wx_pay_query_res = $wx_pay_query->queryOrder($trade_no, $out_trade_no);