<?php
namespace EasyPayment\payment\Tests;
use EasyPayment\payment\alipay\pay;
require __DIR__.'/vendor/autoload.php';
$pay = new pay();
$pay->setOrderSn('13354666');
$pay->setPayMoney('0.01');
$pay->setSubject('֧测试支付');
$pay->setBody('֧测试支付');
$pay->setSuccessUrl('http://www.baidu.com');
$pay->setErrorUrl('http://auto.news18a.com');
$pay->setTradeType(1);
$res = $pay->directPay();
var_dump($res);exit;
