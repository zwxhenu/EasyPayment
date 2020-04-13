<?php
/**
 * 微信红包接口
 *
 * Created by PhpStorm.
 * User: zangl
 * Date: 2020/3/29
 * Time: 15:50
 */

namespace EasyPayment\payment;


interface WxRedPackContract
{
    /**
     * 公众账号
     *
     * @deprecated 微信分配的公众账号ID（企业号corpid即为此appId）。
     *              在微信开放平台（open.weixin.qq.com）申请的移动应用appid无法使用该接口
     * @param $wx_app_id
     *
     * @return mixed
     */
    public function setWxAppId($wx_app_id);

    /**
     * 设置商户ID
     *
     * @deprecated  微信支付分配的商户号
     * @param $mch_id
     *
     * @return mixed
     */
    public function setMchId($mch_id);

    /**
     * 随机字符串
     *
     * @deprecated 随机字符串，不长于32位
     * @param $nonce_str
     *
     * @return mixed
     */
    public function setNonceStr($nonce_str);

    /**
     * 设置商户发红包订单号
     *
     * @deprecated 商户订单号（每个订单号必须唯一。取值范围：0~9，a~z，A~Z）
                    接口根据商户订单号支持重入，如出现超时可再调用。
     * @param $mch_bill_no
     *
     * @return mixed
     */
    public function setMchBillNo($mch_bill_no);

    /**
     * 设置商户名称
     *
     * @deprecated 红包发送者名称 注意：敏感词会被转义成字符*
     * @param $send_name
     *
     * @return mixed
     */
    public function setSendName($send_name);

    /**
     * 设置接受红包的open_id
     *
     * @deprecated 接受红包的用户openid openid为用户在wxappid下的唯一标识（获取openid参见微信公众平台开发者文档
     * @param $re_open_id
     *
     * @return mixed
     */
    public function setReOpenId($re_open_id);

    /**
     * 付款金额
     *
     * @deprecated 付款金额，单位分
     * @param $total_amount
     *
     * @return mixed
     */
    public function setTotalAmount($total_amount);

    /**
     * 设置红包发放总人数
     *
     * @deprecated 红包发放总人数
     * @param $total_num
     *
     * @return mixed
     */
    public function setTotalNum($total_num);

    /**
     * 设置红包祝福语
     *
     * @deprecated 红包祝福语 注意：敏感词会被转义成字符*
     * @param $wishing
     *
     * @return mixed
     */
    public function setWishing($wishing);

    /**
     * 设置Ip地址
     *
     * @deprecated 调用接口的机器Ip地址
     * @param $client_ip
     *
     * @return mixed
     */
    public function setClientIp($client_ip);

    /**
     * 设置活动名称
     *
     * @deprecated 活动名称 注意：敏感词会被转义成字符*
     * @param $act_name
     *
     * @return mixed
     */
    public function setActName($act_name);

    /**
     * 设置备注
     *
     * @deprecated 备注信息
     * @param $remark
     *
     * @return mixed
     */
    public function setRemark($remark);

    /**
     * 设置场景(非必传参数)
     *
     * @deprecated 发放红包使用场景，红包金额大于200或者小于1元时必传
     * @deprecated PRODUCT_1:商品促销 PRODUCT_2:抽奖 PRODUCT_3:虚拟物品兑奖 PRODUCT_4:企业内部福利 PRODUCT_5:渠道分润 PRODUCT_6:保险回馈 PRODUCT_7:彩票派奖 PRODUCT_8:税务刮奖
     * @param $scene_id
     *
     * @return mixed
     */
    public function setSceneId($scene_id);

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
    public function setRiskInfo($risk_info);

    /**
     * 设置裂变红包发送红包金额设置方式
     *
     * @deprecated ALL_RAND—全部随机,商户指定总金额和红包发放总人数，由微信支付随机计算出各红包金额
     * @param $atm_type
     * @return mixed
     */
    public function setAtmType($atm_type);

    /**
     * 设置订单类型
     *
     * @deprecated MCHT:通过商户订单号获取红包信息。
     * @param $bill_type
     *
     * @return mixed
     */
    public function setBillType($bill_type);
}