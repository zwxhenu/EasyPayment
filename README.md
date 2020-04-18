# EasyPayment（Third party payment class library,Contain alipay, wxpay, bdpay）

[![Latest Stable Version](https://poser.pugx.org/zwxhenu/easy-payment/v/stable)](https://packagist.org/packages/zwxhenu/easy-payment)
[![Total Downloads](https://poser.pugx.org/zwxhenu/easy-payment/downloads)](https://packagist.org/packages/zwxhenu/easy-payment)
[![Latest Unstable Version](https://poser.pugx.org/zwxhenu/easy-payment/v/unstable)](https://packagist.org/packages/zwxhenu/easy-payment)
[![License](https://poser.pugx.org/zwxhenu/easy-payment/license)](https://packagist.org/packages/zwxhenu/easy-payment)

### 参考文档[支付文档]
<a href="https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_1">微信支付SDK</a><br/><a href="https://opendocs.alipay.com/open/62/104743/">支付宝支付SDK</a><br/><a href="https://b.baifubao.com/static/spcenter/fe-wallet-open-platform/entry/develop-document/#/document?mdUrl=5bd00a26557d0a2f834cd231">百度钱包SDK</a></p>

# 环境要求
- php版本:>=5.6

# 安装
```php
composer require zwxhenu/easy-payment

```
# 当前支持功能

## 微信支付
   - [JSAPI支付](#JSAPI支付)
   - [Native支付](#Native支付)
   - [支付查询](#支付查询)
   - [金额退款](#金额退款)
   - [退款结果查询](#退款结果查询)
   - [微信普通现金红包](#微信普通现金红包)
   - [微信裂变红包](#微信裂变红包)
   - [微信红包信息查询](#微信红包信息查询)
## 支付宝
   - [支付宝网页版支付](#支付宝网页支付)
   - [支付宝支付查询](#支付宝支付查询)
   - [支付宝即时有密退款](#支付宝即时有密退款)
## 百度钱包 
   - [百度钱包网页版支付](#百度钱包网页版支付)
   - [百度钱包支付查询](#百度钱包支付查询)
   - [百度钱包退款](#百度钱包退款)
   - [百度钱包查询退款结果](#百度钱包查询退款结果)
   
# 使用示例

## 支付宝示例

### <a id="支付宝网页支付">支付宝网页支付</a>

```php
use EasyPayment\payment\alipayService;

/*********支付宝*************/

$pay = new AlipayService();
#-----公共参数配置------#
$pay->setPartner(''); //合作商户
$pay->setInputCharset('utf-8'); // 参数编码字符集 默认utf-8
$pay->setSignType('MD5'); // 签名方式默认 MD5
$pay->setNotifyUrl(''); // 服务器异步通知页面路径
#-----公共参数配置结束-----#

$pay->setSellerId(''); // 卖家支付宝用户号
$pay->setOrderSn('13354666'); // 商户网站唯一订单号
$pay->setPayMoney('0.01'); // 交易金额
$pay->setSubject('֧测试支付'); //商品名称
$pay->setBody('֧测试支付'); // 商品描述
$pay->setReturnUrl('http://www.baidu.com'); // 页面跳转同步通知页面路径
$pay->setTradeType(1); // 支付类型
$res = $pay->directPay();

```

### <a id="支付宝支付查询">支付宝支付查询</a>

```php
/*******支付宝查询******/
$trade_no = '';
$out_trade_no = '';
$pay->setKey('');// 合作商户支付秘钥
$pay->setSellerId('');// 合作商户ID
$pay->setPartner('');// 合作商户
$pay->setOutTradeNo('');// 支付宝交易流水号
$pay->setOrderSn('');// 商户网站订单系统中唯一订单号
// $trade_no 支付宝交易流水号 $out_trade_no 商户网站订单系统中唯一订单号，必填
$query_res = $pay->queryOrder();

```

### <a id="支付宝即时有密退款">支付宝即时有密退款</a>

```php

/*******支付宝即时退款*****/
$pay->setSellerEmail('');// 卖家支付宝账号
$pay->setSellerId('');// 卖家用户ID
$pay->setBatchOn('');// 退款批次号 格式为：退款日期（8位）+流水号（3～24位）。
$pay->setBatchNum(1);// 总笔数 最大支持1000笔
$pay->setDetailData(''); // 单笔数据集 例如：2014040311001004370000361525^5.00^协商退款
$refund_res = $pay->fastPayRefundByPlatformPwd();

```

## 微信支付示例

### <a id="Native支付">Native支付</a>
```php
use EasyPayment\payment\WxPayService;
/**************微信二维码支付************/
/**
 * 微信公众号信息配置
 * APPID：绑定支付的APPID（必须配置）
 * MCHID：商户号（必须配置）
 * KEY：商户支付密钥，参考开户邮件设置（必须配置）
 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置）
 **/
$wx_pay = new WxPayService();
#--------公共配置--------#
// 商户号（必须配置）
$wx_pay->setMchId('');
// 公众帐号secert（仅JSAPI支付的时候需要配置）
$wx_pay->setAppSecret('');
// 绑定支付的APPID
$wx_pay->setAppId('');
// KEY：商户支付密钥，参考开户邮件设置（必须配置）
$wx_pay->setKey('');
#--------公共配置结束--------#

$wx_pay->setOrderSn('12345689');
$wx_pay->setOutOrderNo('4567891');
$wx_pay->setPayMoney(0.01);
$wx_pay->setSubject('测试二维码支付');
$wx_pay->setBody('测试二维码支付');
$wx_pay->setSuccessUrl('http://www.baidu.com');
$wx_pay->setErrorUrl('http://www.baidu.com');
$wx_pay->setTradeType(1);
$res = $wx_pay->nativePay();
$qrcode = $res['data'];
echo $_SERVER['HTTP_HOST'].'/'.$qrcode;

```
### <a id="JSAPI支付">JSAPI支付</a>
```php
/**************微信支付************/

$wx_pay->setIsWap(true);
$wx_pay->setOrderSn('123456789');
$wx_pay->setPayMoney(0.01);
$wx_pay->setSubject('测试微信支付');
$wx_pay->setBody('测试微信支付');
$wx_pay->setSuccessUrl('http://www.baidu.com');
$wx_pay->setErrorUrl('http://www.baidu.com');
$res = $wx_pay->jsAPIPay('454645'); // 微信的openid
```
### <a id="支付查询">支付查询</a>
```php
/*************微信支付查询*************/

$wx_pay->setWxOrderId(''); //微信交易流水号
$wx_pay->setOutOrderNo(''); // 商户网站订单系统中唯一订单号，必填
$wx_pay_query_res = $wx_pay->queryOrder();
```
### <a id="金额退款">金额退款</a>
```php
/*******微信支付退款***/
$wx_pay->setWxOrderId(''); //微信交易流水号
$wx_pay->setOutOrderNo(''); // 商户网站订单系统中唯一订单号，必填
$wx_pay->setOutRefundNo(''); // 商户定义的退款订单编号
$wx_pay->setTotalFee(); // 标价金额
$wx_pay->setRefundFee(); // 退款金额
$wx_pay->setRefundDesc(); // 退款原因
$wx_pay->setRefundAccount(); // 退款资金来源
$wx_pay->setNotifyUrl(); // 退款结果通知url
$wx_pay->refund();

```
### <a id="退款结果查询">退款结果查询</a>

```php
/*********微信退款结果查询*******/

$wx_pay->setWxOrderId(''); //微信交易流水号
$wx_pay->setOutOrderNo(''); // 商户网站订单系统中唯一订单号，必填
$wx_pay->setOutRefundNo(''); // 商户定义的退款订单编号
$wx_pay->setRefundId(''); // 微信退款订单号
###以上四选一就可以查询####
$wx_pay->refundQuery();

```
## 微信现金红包示例

### <a id="微信普通现金红包">微信普通现金红包</a>

```php
 use EasyPayment\payment\WxRedPackService;
 $wx_red_pack = new WxRedPackService();
 
 /*******普通红包********/
 // 随机字符串，不长于32位
 $wx_red_pack->setNonceStr('');
 // 商户订单号（每个订单号必须唯一。取值范围：0~9，a~z，A~Z）
 // 接口根据商户订单号支持重入，如出现超时可再调用。
 $wx_red_pack->setMchBillNo('');
 // 微信支付分配的商户号
 $wx_red_pack->setMchId('');
 // 微信分配的公众账号ID（企业号corpid即为此appId）。
 // 在微信开放平台（open.weixin.qq.com）申请的移动应用appid无法使用该接口。
 $wx_red_pack->setWxAppId('');
 // 红包发送者名称 注意：敏感词会被转义成字符*
 $wx_red_pack->setSendName('');
 // 接受红包的用户openid openid为用户在wxappid下的唯一标识
 $wx_red_pack->setReOpenId('');
 // 付款金额，单位分
 $wx_red_pack->setTotalAmount('');
 // 红包发放总人数
 $wx_red_pack->setTotalNum('');
 // 红包祝福语
 $wx_red_pack->setWishing('');
 // 调用接口的机器Ip地址
 $wx_red_pack->setClientIp('');
 // 活动名称 注意：敏感词会被转义成字符*
 $wx_red_pack->setActName('');
 // 发放红包使用场景，红包金额大于200或者小于1元时必传
 //
 //PRODUCT_1:商品促销
 //
 //PRODUCT_2:抽奖
 //
 //PRODUCT_3:虚拟物品兑奖
 //
 //PRODUCT_4:企业内部福利
 //
 //PRODUCT_5:渠道分润
 //
 //PRODUCT_6:保险回馈
 //
 //PRODUCT_7:彩票派奖
 //
 //PRODUCT_8:税务刮奖
 $wx_red_pack->setSceneId('');
 //posttime:用户操作的时间戳
 //
 //mobile:业务系统账号的手机号，国家代码-手机号。不需要+号
 //
 //deviceid :mac 地址或者设备唯一标识
 //
 //clientversion :用户操作的客户端版本
 //
 //把值为非空的信息用key=value进行拼接，再进行urlencode
 //
 //urlencode(posttime=xx& mobile =xx&deviceid=xx)
 
 $wx_red_pack->setRiskInfo('');
 
 $wx_red_pack->sendRedPack();
 
``` 
 ### <a id="微信裂变红包">微信裂变红包</a>

```php 
 /******裂变红包*****/
 $wx_fission_red_pack = new WxRedPackService();
 // 随机字符串，不长于32位
 $wx_fission_red_pack->setNonceStr('');
 // 商户订单号（每个订单号必须唯一。取值范围：0~9，a~z，A~Z）
 // 接口根据商户订单号支持重入，如出现超时可再调用。
 $wx_fission_red_pack->setMchBillNo('');
 // 微信支付分配的商户号
 $wx_fission_red_pack->setMchId('');
 // 微信分配的公众账号ID（企业号corpid即为此appId）。
 // 在微信开放平台（open.weixin.qq.com）申请的移动应用appid无法使用该接口。
 $wx_fission_red_pack->setWxAppId('');
 // 红包发送者名称 注意：敏感词会被转义成字符*
 $wx_fission_red_pack->setSendName('');
 // 接受红包的用户openid openid为用户在wxappid下的唯一标识
 $wx_fission_red_pack->setReOpenId('');
 // 付款金额，单位分
 $wx_fission_red_pack->setTotalAmount('');
 // 红包发放总人数
 $wx_fission_red_pack->setTotalNum('');
 // 红包祝福语
 $wx_fission_red_pack->setWishing('');
 // 调用接口的机器Ip地址
 $wx_fission_red_pack->setClientIp('');
 // 活动名称 注意：敏感词会被转义成字符*
 $wx_fission_red_pack->setActName('');
 
 $wx_fission_red_pack->sendFissionRedPack();

``` 
 ### <a id="微信红包信息查询">微信红包信息查询</a>
```php 
 /*****红包查询信息********/
 
 $wx_query_red_pack = new WxRedPackService();
 // 随机字符串，不长于32位
 $wx_query_red_pack->setNonceStr('');
 // 商户订单号（每个订单号必须唯一。取值范围：0~9，a~z，A~Z）
 // 接口根据商户订单号支持重入，如出现超时可再调用。
 $wx_query_red_pack->setMchBillNo('');
 // 微信支付分配的商户号
 $wx_query_red_pack->setMchId('');
 // 微信分配的公众账号ID（企业号corpid即为此appId）。
 // 在微信开放平台（open.weixin.qq.com）申请的移动应用appid无法使用该接口。
 $wx_query_red_pack->setWxAppId('');
 // 红包订单类型（查询红包信息参数 当前只有个值：MCHT）
 // MCHT:通过商户订单号获取红包信息。
 $wx_query_red_pack->setBillType('');
 
 $wx_query_red_pack->queryRedPackInfo();
 
```

## 百度钱包示例

### <a id="百度钱包网页版支付">百度钱包网页版支付</a>

```php

use EasyPayment\payment\BdpayService;
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

```

### <a id="百度钱包支付查询">百度钱包支付查询</a>

```php

/**********百度钱包支付查询***********/
$bd_query_pay = new BdpayService();
$bd_query_pay->setSpNo('9000100005');// 合作商户ID
$bd_query_pay->setSpKey('pSAw3bzfMKYAXML53dgQ3R4LsKp758Ss');// 合作商户的支付秘钥
// $out_trade_no 商户网站订单系统中唯一订单号，必填
$bd_query_res = $bd_query_pay->queryOrder($out_trade_no);

```

### <a id="百度钱包退款">百度钱包退款</a>

```php

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

```

### <a id="百度钱包查询退款结果">百度钱包查询退款结果</a>

```php

/**************百度钱包查询退款结果***************/
$bd_query_refund_pay = new BdpayService();
$bd_query_refund_pay->setSpNo('9000100005');
$bd_query_refund_pay->setOrderSn('20140814173437256936'); // 百度钱包订单号
$bd_query_refund_pay->setOutTradeNo('2014081417354462'); // 百度钱包退款流水号
// 根据商户交易流水号查询
$order_sn_query_res = $bd_query_refund_pay->queryRefundByOrderOn();
// 根据退款流水号查询
$out_trade_no_res = $bd_query_refund_pay->queryRefundBySpRefundOn();

```