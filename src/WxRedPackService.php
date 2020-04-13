<?php
/**
 * 微信红包类
 *
 * Created by PhpStorm.
 * User: zangl
 * Date: 2020/3/29
 * Time: 15:44
 */

namespace EasyPayment\payment;

use EasyPayment\payment\PayContract;
use EasyPayment\payment\PayCommon;
use EasyPayment\payment\wxpay\lib\WxPayConfig;
use EasyPayment\payment\wxpay\lib\WxPayApi;
use EasyPayment\payment\wxpay\lib\WxSendCommonRedPack;

class WxRedPackService implements PayContract
{
    private $wx_app_id = 0;
    private $mch_id = 0;
    private $nonce_str = null;
    private $mch_bill_no = null;
    private $send_name = null;
    private $re_open_id = 0;
    private $total_amount = 1;
    private $total_num = 1;
    private $wishing = null;
    private $client_ip = '127.0.0.1';
    private $act_name = null;
    private $remark = null;
    private $scene_id = 'PRODUCT_8';
    private $risk_info = null;
    private $atm_type = '';
    private $pay_common_obj = null;
    private $bill_type = 'MCHT';
    public function __construct()
    {
        $this->pay_common_obj = new PayCommon();
    }
    /**
     * 公众账号
     *
     * @deprecated 微信分配的公众账号ID（企业号corpid即为此appId）。
     *              在微信开放平台（open.weixin.qq.com）申请的移动应用appid无法使用该接口
     * @param $wx_app_id
     *
     * @return mixed
     */
    public function setWxAppId($wx_app_id)
    {
        $this->wx_app_id = $wx_app_id;

        return $this;
    }

    /**
     * 设置商户ID
     *
     * @deprecated  微信支付分配的商户号
     * @param $mch_id
     *
     * @return mixed
     */
    public function setMchId($mch_id)
    {
        $this->mch_id = $mch_id;

        return $this;
    }

    /**
     * 随机字符串
     *
     * @deprecated 随机字符串，不长于32位
     * @param $nonce_str
     *
     * @return mixed
     */
    public function setNonceStr($nonce_str)
    {
        $this->nonce_str = $nonce_str;

        return $this;
    }

    /**
     * 设置商户发红包订单号
     *
     * @deprecated 商户订单号（每个订单号必须唯一。取值范围：0~9，a~z，A~Z）
     * @deprecated 接口根据商户订单号支持重入，如出现超时可再调用。
     * @param $mch_bill_no
     *
     * @return mixed
     */
    public function setMchBillNo($mch_bill_no)
    {
        $this->mch_bill_no = $mch_bill_no;

        return $this;
    }

    /**
     * 设置商户名称
     *
     * @deprecated 红包发送者名称 注意：敏感词会被转义成字符*
     * @param $send_name
     *
     * @return mixed
     */
    public function setSendName($send_name)
    {
        $this->send_name = $send_name;

        return $this;
    }

    /**
     * 设置接受红包的open_id
     *
     * @deprecated 接受红包的用户openid openid为用户在wxappid下的唯一标识（获取openid参见微信公众平台开发者文档
     * @param $re_open_id
     *
     * @return mixed
     */
    public function setReOpenId($re_open_id)
    {
        $this->re_open_id = $re_open_id;

        return $this;
    }

    /**
     * 付款金额
     *
     * @deprecated 付款金额，单位分
     * @param $total_amount
     *
     * @return mixed
     */
    public function setTotalAmount($total_amount)
    {
        $this->total_amount = $total_amount;

        return $this;
    }

    /**
     * 设置红包发放总人数
     *
     * @deprecated 红包发放总人数
     * @param $total_num
     *
     * @return mixed
     */
    public function setTotalNum($total_num)
    {
        $this->total_num = $total_num;

        return $this;
    }

    /**
     * 设置红包祝福语
     *
     * @deprecated 红包祝福语 注意：敏感词会被转义成字符*
     * @param $wishing
     *
     * @return mixed
     */
    public function setWishing($wishing)
    {
        $this->wishing = $wishing;

        return $this;
    }

    /**
     * 设置Ip地址
     *
     * @deprecated 调用接口的机器Ip地址
     * @param $client_ip
     *
     * @return mixed
     */
    public function setClientIp($client_ip)
    {
        $this->client_ip = $client_ip;

        return $this;
    }

    /**
     * 设置活动名称
     *
     * @deprecated 活动名称 注意：敏感词会被转义成字符*
     * @param $act_name
     *
     * @return mixed
     */
    public function setActName($act_name)
    {
        $this->act_name = $act_name;

        return $this;
    }

    /**
     * 设置备注
     *
     * @deprecated 备注信息
     * @param $remark
     *
     * @return mixed
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * 设置场景(非必传参数)
     *
     * @deprecated 发放红包使用场景，红包金额大于200或者小于1元时必传
     * @deprecated PRODUCT_1:商品促销 PRODUCT_2:抽奖 PRODUCT_3:虚拟物品兑奖 PRODUCT_4:企业内部福利 PRODUCT_5:渠道分润 PRODUCT_6:保险回馈 PRODUCT_7:彩票派奖 PRODUCT_8:税务刮奖
     * @param $scene_id
     *
     * @return mixed
     */
    public function setSceneId($scene_id)
    {
        $this->scene_id = $scene_id;

        return $this;
    }

    /**
     * 设置活动信息（非必传参数）
     *
     * @deprecated posttime:用户操作的时间戳
     * @deprecated mobile:业务系统账号的手机号，国家代码-手机号。不需要+号
     * @deprecated deviceid :mac 地址或者设备唯一标识
     * @deprecated clientversion :用户操作的客户端版本
     * @deprecated 把值为非空的信息用key=value进行拼接，再进行urlencode
     * @deprecated urlencode(posttime=xx& mobile =xx&deviceid=xx)
     * @param $risk_info
     *
     * @return mixed
     */
    public function setRiskInfo($risk_info)
    {
        $this->risk_info = $risk_info;

        return $this;
    }

    /**
     *
     * 发送裂变红包设置红包金额设置方式（裂变红包需要）
     *
     * @param $atm_type
     *
     * @return $this
     */
    public function setAtmType($atm_type)
    {
        $this->atm_type = $atm_type;

        return $this;
    }

    /**
     * 设置红包订单类型（查询红包信息参数 当前只有个值：MCHT）
     *
     * @deprecated MCHT:通过商户订单号获取红包信息。
     * @param $bill_type
     *
     * @return $this
     */
    public function setBillType($bill_type)
    {
        $this->bill_type = $bill_type;

        return $this;
    }
    /**
     * 发送普通红包
     *
     * @return array
     * @throws wxpay\lib\WxPayException
     */
    public function sendRedPack()
    {
        if (empty($this->wx_app_id)) {
            return $this->pay_common_obj->alertInfo(1, '微信公众账号appid未设置！');
        }
        if (empty($this->mch_id)) {
            return $this->pay_common_obj->alertInfo(1, '商户号未设置！');
        }
        if (empty($this->mch_bill_no)) {
            return $this->pay_common_obj->alertInfo(1, '商户订单号未设置！');
        }
        if (empty($this->send_name)) {
            return $this->pay_common_obj->alertInfo(1, '商户名称未设置！');
        }
        if (empty($this->re_open_id)) {
            return $this->pay_common_obj->alertInfo(1, '接受红包的用户openid未设置！');
        }
        if (empty($this->total_amount)) {
            return $this->pay_common_obj->alertInfo(1, '红包付款金额未设置！');
        }
        if (empty($this->total_num)) {
            return $this->pay_common_obj->alertInfo(1, '红包发送总人数未设置！');
        }
        if (empty($this->wishing)) {
            return $this->pay_common_obj->alertInfo(1, '红包祝福语未设置！');
        }
        if (empty($this->act_name)) {
            return $this->pay_common_obj->alertInfo(1, '活动名称未设置！');
        }
        if (empty($this->remark)) {
            return $this->pay_common_obj->alertInfo(1, '红包备注未设置！');
        }
        if (empty($this->client_ip)) {
            $this->client_ip = $this->pay_common_obj->ip();
        }
        if (empty($this->nonce_str)) {
            $this->nonce_str = $this->pay_common_obj->randomString(32);
        }
        $input = new WxSendCommonRedPack();
        $input->SetWx_app_id($this->wx_app_id);
        $input->SetMch_id($this->mch_id);
        $input->SetNonce_str($this->nonce_str);
        $input->SetMch_bill_no($this->mch_bill_no);
        $input->SetSend_name($this->send_name);
        $input->SetRe_Openid($this->re_open_id);
        $input->SetTotal_amount($this->total_amount);
        $input->SetTotal_num($this->total_num);
        $input->SetWishing($this->wishing);
        $input->SetClient_ip($this->client_ip);
        $input->SetAct_name($this->act_name);
        $input->SetRemark($this->remark);
        $input->SetScene_id($this->scene_id);
        $input->SetRisk_info($this->risk_info);
        $send_red_pack_result = WxPayApi::sendRedPack($input);
        if(isset($send_red_pack_result['result_code'])
            && $send_red_pack_result['result_code'] == 'SUCCESS'){
            return $this->pay_common_obj->alertInfo(0, '红包发送成功！',
                                                    array(
                                                        'send_listid' => $send_red_pack_result['send_listid'],
                                                        'mch_billno' => $send_red_pack_result['mch_billno'],
                                                        'mch_id' => $send_red_pack_result['mch_id'],
                                                        'wxappid' => $send_red_pack_result['wxappid'],
                                                        're_openid' => $send_red_pack_result['re_openid'],
                                                        'total_amount' => $send_red_pack_result['total_amount']));
        }
        if(isset($send_red_pack_result['return_code'])
            && $send_red_pack_result['return_code'] !== 'SUCCESS'){
            return $this->pay_common_obj->alertInfo($send_red_pack_result['return_code'], '请求发送红包接口通信失败！');
        }

        return $this->pay_common_obj->alertInfo($send_red_pack_result['err_code'], $send_red_pack_result['err_code_des']);
    }

    /**
     * 发送裂变红包
     *
     * @return array
     * @throws wxpay\lib\WxPayException
     */
    public function sendFissionRedPack()
    {
        if (empty($this->wx_app_id)) {
            return $this->pay_common_obj->alertInfo(1, '微信公众账号appid未设置！');
        }
        if (empty($this->mch_id)) {
            return $this->pay_common_obj->alertInfo(1, '商户号未设置！');
        }
        if (empty($this->mch_bill_no)) {
            return $this->pay_common_obj->alertInfo(1, '商户订单号未设置！');
        }
        if (empty($this->send_name)) {
            return $this->pay_common_obj->alertInfo(1, '商户名称未设置！');
        }
        if (empty($this->re_open_id)) {
            return $this->pay_common_obj->alertInfo(1, '接受红包的用户openid未设置！');
        }
        if (empty($this->total_amount)) {
            return $this->pay_common_obj->alertInfo(1, '红包付款金额未设置！');
        }
        if (empty($this->total_num)) {
            return $this->pay_common_obj->alertInfo(1, '红包发送总人数未设置！');
        }
        if (empty($this->wishing)) {
            return $this->pay_common_obj->alertInfo(1, '红包祝福语未设置！');
        }
        if (empty($this->act_name)) {
            return $this->pay_common_obj->alertInfo(1, '活动名称未设置！');
        }
        if (empty($this->remark)) {
            return $this->pay_common_obj->alertInfo(1, '红包备注未设置！');
        }
        if (empty($this->atm_type)) {
            return $this->pay_common_obj->alertInfo(1, '红包金额设置方式未设置！');
        }
        if (empty($this->client_ip)) {
            $this->client_ip = $this->pay_common_obj->ip();
        }
        if (empty($this->nonce_str)) {
            $this->nonce_str = $this->pay_common_obj->randomString(32);
        }
        $input = new WxSendCommonRedPack();
        $input->SetWx_app_id($this->wx_app_id);
        $input->SetMch_id($this->mch_id);
        $input->SetNonce_str($this->nonce_str);
        $input->SetMch_bill_no($this->mch_bill_no);
        $input->SetSend_name($this->send_name);
        $input->SetRe_Openid($this->re_open_id);
        $input->SetTotal_amount($this->total_amount);
        $input->SetTotal_num($this->total_num);
        $input->SetWishing($this->wishing);
        $input->SetClient_ip($this->client_ip);
        $input->SetAct_name($this->act_name);
        $input->SetRemark($this->remark);
        $input->SetScene_id($this->scene_id);
        $input->SetRisk_info($this->risk_info);
        $send_red_pack_result = WxPayApi::sendRedPack($input);
        if(isset($send_red_pack_result['result_code'])
            && $send_red_pack_result['result_code'] == 'SUCCESS'){
            return $this->pay_common_obj->alertInfo(0, '红包发送成功！',
                array(
                    'send_listid' => $send_red_pack_result['send_listid'],
                    'mch_billno' => $send_red_pack_result['mch_billno'],
                    'mch_id' => $send_red_pack_result['mch_id'],
                    'wxappid' => $send_red_pack_result['wxappid'],
                    're_openid' => $send_red_pack_result['re_openid'],
                    'total_amount' => $send_red_pack_result['total_amount']));
        }
        if(isset($send_red_pack_result['return_code'])
            && $send_red_pack_result['return_code'] !== 'SUCCESS'){
            return $this->pay_common_obj->alertInfo($send_red_pack_result['return_code'], '请求发送红包接口通信失败！');
        }

        return $this->pay_common_obj->alertInfo($send_red_pack_result['err_code'], $send_red_pack_result['err_code_des']);
    }

    /**
     * 商户红包信息查询
     *
     * @return array
     * @throws wxpay\lib\WxPayException
     */
    public function queryRedPackInfo()
    {
        if (empty($this->wx_app_id)) {
            return $this->pay_common_obj->alertInfo(1, '微信公众账号appid未设置！');
        }
        if (empty($this->mch_id)) {
            return $this->pay_common_obj->alertInfo(1, '商户号未设置！');
        }
        if (empty($this->mch_bill_no)) {
            return $this->pay_common_obj->alertInfo(1, '商户订单号未设置！');
        }
        if (empty($this->bill_type)) {
            $this->bill_type = 'MCHT';
        }
        if (empty($this->nonce_str)) {
            $this->nonce_str = $this->pay_common_obj->randomString(32);
        }
        $input = new WxSendCommonRedPack();
        $input->SetWx_app_id($this->wx_app_id);
        $input->SetMch_id($this->mch_id);
        $input->SetNonce_str($this->nonce_str);
        $input->SetMch_bill_no($this->mch_bill_no);
        $input->SetBill_type($this->bill_type);
        $send_red_pack_result = WxPayApi::queryRedPackInfo($input);
        if(isset($send_red_pack_result['result_code'])
            && $send_red_pack_result['result_code'] == 'SUCCESS'){
            return $this->pay_common_obj->alertInfo(0, '红包结果查询成功！',
                array(
                    // 商户订单号
                    'mch_billno' => $send_red_pack_result['mch_billno'],
                    // 商户号
                    'mch_id' => $send_red_pack_result['mch_id'],
                    // 红包单号
                    'detail_id' => $send_red_pack_result['detail_id'],
                    // 红包状态
                    // SENDING:发放中
                    // SENT:已发放待领取
                    // FAILED：发放失败
                    // RECEIVED:已领取
                    // RFUND_ING:退款中
                    // REFUND:已退款
                    'status' => $send_red_pack_result['status'],
                    // 发放类型
                    // API:通过API接口发放
                    // UPLOAD:通过上传文件方式发放
                    // ACTIVITY:通过活动方式发放
                    'send_type' => $send_red_pack_result['send_type'],
                    // 红包类型
                    // GROUP:裂变红包
                    // NORMAL:普通红包
                    'hb_type' => $send_red_pack_result['hb_type'],
                    // 红包个数
                    'total_num' => $send_red_pack_result['total_num'],
                    // 红包总金额（单位分）
                    'total_amount' => $send_red_pack_result['total_amount'],

                    // 红包发送时间
                    'send_time' => $send_red_pack_result['send_time'],
                    // 红包退款时间
                    'refund_time' => $send_red_pack_result['refund_time'],
                    // 领取红包的Openid
                    'openid' => $send_red_pack_result['openid'],
                    // 金额
                    'amount' => $send_red_pack_result['amount'],
                    // 接收时间
                    'rcv_time' => $send_red_pack_result['rcv_time'],
                    // 发送失败原因
                    'reason' => $send_red_pack_result['reason'],
                    'send_type' => $send_red_pack_result['send_type'],
                    'send_type' => $send_red_pack_result['send_type'],
                    'send_type' => $send_red_pack_result['send_type'],
                    'send_type' => $send_red_pack_result['send_type'],));
        }
        if(isset($send_red_pack_result['return_code'])
            && $send_red_pack_result['return_code'] !== 'SUCCESS'){
            return $this->pay_common_obj->alertInfo($send_red_pack_result['return_code'], '请求查询红包接口通信失败！');
        }

        return $this->pay_common_obj->alertInfo($send_red_pack_result['err_code'], $send_red_pack_result['err_code_des']);
    }
}