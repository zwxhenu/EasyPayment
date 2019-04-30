<?php
/**
 * 初始化配置
 */
namespace EasyPayment\payment\bdpay\lib;

final class BdpayConfig
{
    // 商户在百度钱包的商户ID
    public static $SP_NO = '';
    // 密钥文件路径，该文件中保存了商户的百度钱包合作密钥，该文件需要放在一个安全的地方，切勿让外人知晓或者外网访问
    public static $SP_KEY = '';
    //支付成功同步回调地址
    public static $RETURN_URL = '';
    //支付成功异步回调地址
    public static $NOTIFY_URL = '';
    // 日志文件
    public static $LOG_FILE = '/tmp/bd_sdk.log';

    // 商户订单支付成功
    const SP_PAY_RESULT_SUCCESS = 1;
    // 商户订单等待支付
    const SP_PAY_RESULT_WAITING = 2;
    // 百度钱包退款接口URL
    const BFB_REFUND_URL = "https://www.baifubao.com/api/0/refund";
    // 百度钱包退款查询接口URL
    const BFB_REFUND_QUERY_URL = "https://www.baifubao.com/api/0/refund/0/query";
    // 百度钱包PC端即时到账支付接口URL（需要用户登录百度钱包）
    const BFB_PAY_DIRECT_LOGIN_URL = "https://www.baifubao.com/api/0/pay/0/direct/0";
    // 百度钱包PC端即时到账支付接口URL（免登录百度钱包 xyz增加）
    const BFB_PAY_DIRECT_NOLOGIN_URL = "https://www.baifubao.com/api/0/pay/0/direct";
    // 百度钱包H5端即时到账支付接口（需要用户登录百度钱包）(xyz增加)
    const BFB_PAY_WAP_DIRECT_NEEDLOGIN_URL = "https://www.baifubao.com/api/0/pay/0/wapdirect/0";
    // 百度钱包H5端即时到账支付接口（免登录百度钱包）(xyz增加)
    const BFB_PAY_WAP_DIRECT_NOLOGIN_URL = "https://www.baifubao.com/api/0/pay/0/wapdirect";
    // 百度钱包订单号查询支付结果接口URL
    const BFB_QUERY_ORDER_URL = "https://www.baifubao.com/api/0/query/0/pay_result_by_order_no";
    // 百度钱包订单号查询重试次数
    const BFB_QUERY_RETRY_TIME = 3;
    // 百度钱包支付成功
    const BFB_PAY_RESULT_SUCCESS = 1;
    // 百度钱包支付通知成功后的回执
    const BFB_NOTIFY_META = "<meta name=\"VIP_BFB_PAYMENT\" content=\"BAIFUBAO\">";

    // 签名校验算法
    const SIGN_METHOD_MD5 = 1;
    const SIGN_METHOD_SHA1 = 2;
    // 百度钱包即时到账接口服务ID
    const BFB_PAY_INTERFACE_SERVICE_ID = 1;
    // 百度钱包查询接口服务ID
    const BFB_QUERY_INTERFACE_SERVICE_ID = 11;
    // 百度钱包退款接口服务ID
    const BFB_REFUND_INTERFACE_SERVICE_ID = 2;
    // 百度钱包退款查询接口服务ID
    const BFB_REFUND_QUERY_INTERFACE_SERVICE_ID = 12;
    // 百度钱包接口版本
    const BFB_INTERFACE_VERSION = 2;
    // 百度钱包接口字符编码
    const BFB_INTERFACE_ENCODING = 1;
    // 百度钱包接口返回格式：XML
    const BFB_INTERFACE_OUTPUT_FORMAT = 1;
    // 百度钱包接口货币单位：人民币
    const BFB_INTERFACE_CURRENTCY = 1;
}