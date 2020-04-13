<?php
/**
 * 支付宝支付操作类
 *
 */
namespace EasyPayment\payment;

use EasyPayment\payment\alipay\lib\AlipaySubmit;
use EasyPayment\payment\AlipayConfigContract;
use EasyPayment\payment\PayContract;
use EasyPayment\payment\PayCommon;
use EasyPayment\payment\alipay\lib\AlipayNotify;
class AlipayService implements PayContract,AlipayConfigContract
{

    /***发起支付配置***/
    private $out_trade_no = '';
    private $pay_money = 0;
    private $subject = '';
    private $body = '';
    private $showUrl = '';
    private $trade_type = 0;
    private $order_sn = '';
    private $success_url = '';
    private $error_url = '';
    private $is_wap = false;
    /****************/
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
    private $partner = '';
    /**
     * 收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
     *
     * @var string
     */
    private $seller_id = '';
    /**
     * 卖家支付账号
     *
     * @var string
     */
    private $seller_email = '';
    /**
     * 退款批次号 退款商家自动生成 （退款日期（8位）+流水号（3～24位））
     *
     * @var string
     */
    private $batch_on = '';
    /**
     * 总笔数 最大支持1000笔
     *
     * @var string
     */
    private $batch_num = '';
    /**
     * 单笔数据集 退款请求的明细数据 例如：2014040311001004370000361525^5.00^协商退款
     *
     * @var string
     */
    private $detail_data = '';
    /**
     * MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
     *
     * @var string
     */
    private $key = '';
    private $pay_common_obj = null;
    public function __construct()
    {
        $this->pay_common_obj = new PayCommon();
    }
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
     *
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

    /**
     * 设置合作者身份
     *  2088421749717068
     *
     * @param $partner
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partner = trim($partner);

        return $this;
    }

    /**
     * 是否为WAP支付
     *
     * @param $is_wap
     * @return $this
     */
    public function setIsWap($is_wap)
    {
        $this->is_wap = (bool)$is_wap;

        return $this;
    }


    /**
     * 支付金额
     *
     * @param int $pay_money
     * @return $this
     */
    public function setPayMoney($pay_money)
    {
        if (!is_numeric($pay_money) || $pay_money <= 0) {
            return false;
        }
        $this->pay_money = $pay_money;

        return $this;
    }

    /**
     * 支付摘要
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $subject = trim($subject);
        if (empty($subject)) {
            return false;
        }
        $this->subject = $subject;

        return $this;
    }

    /**
     * 商品详情
     *
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $body = trim($body);
        $search = array(',', "'", "\r\n", "\n", "\r", "\t");
        $replace = array('，', '’', ' ', ' ', ' ', ' ');
        $body = str_ireplace($search, $replace, $body);
        // body不超过60个字符
        $body = mb_substr($body, 0,  60);
        $this->body = $body;

        return $this;
    }

    /**
     * @param string $showUrl
     * @return $this
     */
    public function setShowUrl($showUrl)
    {
        $this->showUrl = trim($showUrl);

        return $this;
    }

    /**
     * @param int $trade_type
     * @return $this
     */
    public function setTradeType($trade_type)
    {
        if (!is_numeric($trade_type)) {
            return false;
        }
        $this->trade_type = (int)$trade_type;

        return $this;
    }

    /**
     * 商户订单唯一订单号
     *
     * @param string $order_sn
     * @return $this
     */
    public function setOrderSn($order_sn)
    {
        $this->order_sn = $order_sn;

        return $this;
    }

    /**
     * @param string $success_url
     * @return $this
     */
    public function setSuccessUrl($success_url)
    {
        $this->success_url = trim($success_url);

        return $this;
    }

    /**
     * @param string $error_url
     * @return $this
     */
    public function setErrorUrl($error_url)
    {
        $this->error_url = $error_url;

        return $this;
    }

    /**
     * 设置支付宝交易流水号
     *
     * @param $out_trade_no
     * @return $this
     */
    public function setOutTradeNo($out_trade_no)
    {
        $this->out_trade_no = $out_trade_no;

        return $this;
    }

    /**
     * 设置卖家的支付宝账号
     *
     * @param $seller_email
     *
     * @return $this
     */
    public function setSellerEmail($seller_email)
    {
        $this->seller_email = $seller_email;
        return $this;
    }

    /**
     * 设置退款批次号
     *
     * @param $batch_on
     * @return $this
     */
    public function setBatchOn($batch_on)
    {
        $this->batch_on = $batch_on;
        return $this;
    }
    /**
     * 设置退款总笔数
     *
     * @param $batch_num
     * @return $this
     */
    public function setBatchNum($batch_num)
    {
        $this->batch_num = $batch_num;
        return $this;
    }

    /**
     * 设置单笔数据集
     *
     * @param $detail_data
     *
     * @return $this
     */
    public function setDetailData($detail_data)
    {
        $this->detail_data = $detail_data;
        return $this;
    }
    /**
     * 发起支付
     *
     * @return array
     */
    public function directPay()
    {
        $service = $this->is_wap == true ? 'alipay.wap.create.direct.pay.by.user' : 'create_direct_pay_by_user';
        $alipay_config = array('partner' => $this->partner,
            'key' => $this->key,
            'seller_id' => $this->seller_id,
            'service' => $service,
            'payment_type' =>$this->payment_type,
            'it_b_pay' => $this->it_b_pay,
            'notify_url' => $this->notify_url,
            'return_url' => $this->return_url,
            'input_charset' => $this->input_charset,
            'anti_phishing_key' => $this->anti_phishing_key,
            'exter_invoke_ip' => $this->exter_invoke_ip,
            'sign_type' => $this->sign_type,
            'cacert' => __DIR__ . '/lib/cacert.pem');
        /* -----------------------请求参数--------------------------- */
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        if (empty($this->order_sn)) {
            return $this->pay_common_obj->alertInfo(1, '商家订单号不能为空');
        }
        $this->out_trade_no = $this->order_sn;
        // 订单摘要信息，必填
        if (empty($this->subject)) {
            return $this->pay_common_obj->alertInfo(1, '订单摘要信息错误');
        }
        // 付款金额，必填
        $total_fee = $this->pay_money;
        if (!is_numeric($total_fee) || $total_fee <= 0) {
            return $this->pay_common_obj->alertInfo(1, '支付金额错误');
        }
        /* ------------------ 构造要请求的参数数组，无需改动---------------- */
        // 其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
        // 如"参数名" => "参数值" 注：上一个参数末尾需要“,”逗号。
        $params = array(
            "service" => $alipay_config['service'],
            "partner" => $alipay_config['partner'],
            "seller_id" => $alipay_config['seller_id'],
            "payment_type" => $alipay_config['payment_type'],
            "it_b_pay" => $alipay_config['it_b_pay'],
            "notify_url" => $alipay_config['notify_url'],
            "return_url" => $alipay_config['return_url'],
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            "out_trade_no" => $this->out_trade_no,
            "subject" => $this->subject,
            "total_fee" => $total_fee,
            "show_url" => $this->showUrl,
            "extra_common_param" => $this->order_sn,
            "body" => $this->body
        );
        if ($this->is_wap === false) {
            $params['anti_phishing_key'] = $alipay_config['anti_phishing_key'];
            $params['exter_invoke_ip'] = $alipay_config['exter_invoke_ip'];
        }
        // 建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($params, "get", "确认");

        return $this->pay_common_obj->alertInfo(0, '成功！', array('content' =>$html_text));
    }
     /**
     * 即时到账有密退款
     * 
     * @return array
     */
    public function fastPayRefundByPlatformPwd()
    {
        $service = 'refund_fastpay_by_platform_pwd';
        $alipay_config = array(
            'service' => $service,
            'partner' => $this->partner,
            'input_charset' => $this->input_charset,
            'sign_type' => $this->sign_type,
            'notify_url' => $this->notify_url,
            
            'seller_email' => $this->seller_email, // 卖家支付宝账号
            'seller_user_id' => $this->partner, // 卖家用户ID同商户ID
            'refund_date' => date('Y-m-d H:i:s'),
            'batch_no' =>  $this->batch_on, //退款批次号  格式为：退款日期（8位）+流水号（3～24位）
            'batch_num' => $this->batch_num, // 退款总笔数 最大支持1000笔
            );
            $alipay_submit = new AlipaySubmit($alipay_config);
            $html_text = $alipay_submit->buildRequestHttp($alipay_config, "get", "success");

            return $this->pay_common_obj->alertInfo(0, 'success', array('content' =>$html_text));
    }
    /**
     * 查询订单支付状态
     * @return array
     */
    public function queryOrder()
    {
        //查询支付信息
        $pay_config = $this->getPayQueryOrderConfig();
        $parameter = array(
            "service" => "single_trade_query",
            "partner" => $pay_config['partner'],
            "trade_no" => $this->out_trade_no,  // 支付宝交易流水号
            "out_trade_no" => $this->order_sn,  // 商户网站订单系统中唯一订单号，必填
            "_input_charset" => trim(strtolower($pay_config['input_charset']))
        );
        // 建立请求
        $alipaySubmit = new AlipaySubmit($pay_config);
        $html_text = $alipaySubmit->buildRequestHttp($parameter);
        $trade_info = json_decode(json_encode(simplexml_load_string($html_text)), true);
        $data['trade_info'] = $trade_info;
        if (empty($trade_info) || !is_array($trade_info) || $trade_info['is_success'] != 'T') {
            return $this->pay_common_obj->alertInfo(1, '查询失败，查询结果为空' . $trade_info['error'], $data);
        }
        $dk_total_money = round($trade_info['total_fee'], 2);
        $data['dk_total_money'] = $dk_total_money;
        $data['trade_no'] = $trade_info['trade_no'];

        return $this->pay_common_obj->alertInfo(0, '成功！', $data);
    }
    /**
     * 支付同步回调
     */
    public function payReturn()
    {
        $res = $this->notify(false);
        $jump_url = '';
        if (isset($res['data']['error_url'])) {
            if (isset($res['code']) && $res['code'] === 0) {
                //拼接参数
                $trade_no = $res['data']['trade_no'];
                $success_url = $res['data']['success_url'];
                $jump_url = strstr($success_url, '?') ? $success_url . '&trade_no=' . $trade_no : $success_url . '?trade_no=' . $trade_no;
            } else {
                $jump_url = $res['data']['error_url'];
            }
        }

        return $this->pay_common_obj->alertInfo(0, 'success', array('url' => $jump_url));
    }

    /**
     * 支付异步回调
     */
    public function payNotify()
    {
        $res = $this->notify(true);
        if (!isset($res['code']) || $res['code'] !== 0) {
            return $this->pay_common_obj->alertInfo(1, '回调失败！');
        }

        return $this->pay_common_obj->alertInfo(0, '回调成功！');
    }

    /**
     * 支付回调处理
     *
     * @param bool $is_notify 是否为异步回调
     * @return array
     */
    private function notify($is_notify = false)
    {
        // 商户订单号
        $out_trade_no = trim($_GET['out_trade_no']);
        // 支付宝交易号
        $trade_no = trim($_GET['trade_no']);
        // 交易状态
        $pay_result = $_GET['trade_status'];
        // 支付金额
        $pay_money = (float)$_GET['total_fee'];
        // 系统单号
        $out_trade_no_t = explode('-', $out_trade_no);
        $order_sn = $out_trade_no_t[0];
        $data = array();
        $data['trade_no'] = $trade_no;
        if (empty($out_trade_no) || empty($trade_no) || $pay_money <= 0 || empty($order_sn)) {
            return $this->pay_common_obj->alertInfo(1, '回传数据异常', $data);
        }
        $alipay_config = $this->getPayCallbackConfig();
        $data['error_url'] = $alipay_config['error_url'];
        $data['success_url'] = $alipay_config['success_url'];
        // 验证签名
        $alipayNotify = new AlipayNotify($alipay_config);
        if ($is_notify == true) {
            $verify_result = $alipayNotify->verifyNotify();
        } else {
            $verify_result = $alipayNotify->verifyReturn();
        }
        if (!$verify_result) {
            return $this->pay_common_obj->alertInfo(1, '支付校验失败!', $data);
        }
        // 支付失败处理
        if ($pay_result != 'TRADE_SUCCESS') {
            return $this->pay_common_obj->alertInfo(1, '支付失败！', $data);
        }

        return $this->pay_common_obj->alertInfo(0, '支付成功！', $data);
    }

    /**
     * 支付回调详细配置信息
     *
     * @return array
     */
    private function getPayCallbackConfig()
    {

        $pay_config = array('error_url' => $this->error_url,
            'success_url' => $this->success_url,
            'partner' => $this->partner,
            'key' => $this->key,
            'seller_id' => $this->seller_id,
            'transport' => $this->transport,
            'sign_type' => $this->sign_type);

        return $pay_config;
    }

    /**
     * 支付查询详细配置信息
     *
     * @return array
     */
    private function getPayQueryOrderConfig()
    {

        $pay_config = array('partner' => $this->partner,
            'key' => $this->key,
            'seller_id' => $this->seller_id,
            'transport' => $this->transport,
            'sign_type' => $this->sign_type,
            'cacert' => $this->cacert);

        return $pay_config;
    }
}