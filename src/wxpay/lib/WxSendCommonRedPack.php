<?php
/**
 * 微信发送普通红包
 *
 * Created by PhpStorm.
 * User: user
 * Date: 2020/3/27
 * Time: 17:49
 */
namespace EasyPayment\payment\wxpay\lib;

use EasyPayment\payment\wxpay\lib\WxPayDataBase;

class WxSendCommonRedPack extends WxPayDataBase
{
    /**
     * 设置微信分配的公众账号ID
     *
     * @param string $value
     *
     */
    public function SetWx_app_id($value)
    {
        $this->values['wxappid'] = $value;
    }

    /**
     * 获取微信分配的公众账号ID的值
     *
     * @return mixed
     *
     */
    public function GetWx_app_id()
    {
        return $this->values['wxappid'];
    }

    /**
     * 判断微信分配的公众账号ID是否存在
     *
     * @return true 或 false
     *
     */
    public function IsWx_app_idSet()
    {
        return array_key_exists('wxappid', $this->values);
    }

    /**
     * 设置微信支付分配的商户号
     *
     * @param string $value
     *
     */
    public function SetMch_id($value)
    {
        $this->values['mch_id'] = $value;
    }

    /**
     * 获取微信支付分配的商户号的值
     *
     * @return mixed
     *
     */
    public function GetMch_id()
    {
        return $this->values['mch_id'];
    }

    /**
     * 判断微信支付分配的商户号是否存在
     *
     * @return true 或 false
     *
     */
    public function IsMch_idSet()
    {
        return array_key_exists('mch_id', $this->values);
    }

    /**
     * 设置随机数
     *
     * @param $value
     */
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }

    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     *
     * @return mixed
     *
     */
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }

    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     *
     * @return true 或 false
     *
     */
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }
    /**
     * 接受红包的用户openid
     *
     * @param string $value
     *
     */
    public function SetRe_Openid($value)
    {
        $this->values['re_openid'] = $value;
    }

    /**
     * 获取接受红包的用户openid
     *
     * @return mixed
     *
     */
    public function GetRe_openid()
    {
        return $this->values['re_openid'];
    }

    /**
     * 判断接受红包的用户openid是否存在
     *
     * @return true 或 false
     *
     */
    public function IsRe_OpenidSet()
    {
        return array_key_exists('re_openid', $this->values);
    }

    /**
     * 设置商户名称
     *
     * @param $value
     */
    public function SetSend_name($value)
    {
        $this->values['send_name'] = $value;
    }

    /**
     * 获取商户名称
     *
     * @return mixed
     */
    public function GetSend_name()
    {
        return $this->values['send_name'];
    }

    /**
     * 检测商户名称是否存在
     *
     * @return bool
     */
    public function IsSend_nameSet()
    {
        return array_key_exists('send_name', $this->values);
    }
    /**
     * 设置商户订单号（每个订单号必须唯一。取值范围：0~9，a~z，A~Z）
     *
     *
     * @param string $value
     *
     */
    public function SetMch_bill_no($value)
    {
        $this->values['mch_billno'] = $value;
    }

    /**
     * 获取置商户订单号（每个订单号必须唯一。取值范围：0~9，a~z，A~Z）
     *
     * @return mixed
     *
     */
    public function GetMch_bill_no()
    {
        return $this->values['mch_billno'];
    }

    /**
     * 判断商户订单号（每个订单号必须唯一。取值范围：0~9，a~z，A~Z）mch_billno是否存在
     *
     * @return true 或 false
     *
     */
    public function IsMch_bill_noSet()
    {
        return array_key_exists('mch_billno', $this->values);
    }

    /**
     * 设置付款金额，单位分
     *
     * @param $value
     */
    public function SetTotal_amount($value)
    {
        $this->values['total_amount'] = $value;
    }

    /**
     * 获取付款金额
     *
     * @return mixed
     */
    public function GetTotal_amount()
    {
        return $this->values['total_amount'];
    }

    /**
     * 判断是否存在付款金额
     *
     * @return bool
     */
    public function IsTotal_amountSet()
    {
        return array_key_exists('total_amount', $this->values);
    }

    /**
     * 红包发放总人数
     *
     * @param $value
     */
    public function SetTotal_num($value)
    {
        $this->values['total_num'] = $value;
    }

    /**
     * 获取发红包总人数
     *
     * @return mixed
     */
    public function GetTotal_num()
    {
        return $this->values['total_num'];
    }

    /**
     * 检测红包总人数是否设置
     *
     * @return bool
     */
    public function IsTotal_numSet()
    {
        return array_key_exists('total_num', $this->values);
    }
    /**
     * 设置红包祝福语wishing
     *
     * @param $value
     */
    public function SetWishing($value)
    {
        $this->values['wishing'] = $value;
    }

    /**
     * 获取红包祝福语
     *
     * @return mixed
     */
    public function GetWishing()
    {
        return $this->values['wishing'];
    }

    /**
     * 检测红包祝福语是否设置
     *
     * @return bool
     */
    public function IsWishingSet()
    {
        return array_key_exists('wishing', $this->values);
    }
    /**
     * 设置客户端IP
     *
     * @param $value
     */
    public function SetClient_ip($value)
    {
        $this->values['client_ip'] = $value;
    }

    /**
     * 获取客户端ip
     *
     * @return mixed
     */
    public function GetClient_ip()
    {
        return $this->values['client_ip'];
    }

    /**
     * 检测客户端IP是否设置
     *
     * @return bool
     */
    public function IsClient_ipSet()
    {
        return array_key_exists('client_ip', $this->values);
    }
    /**
     * 设置活动名称
     *
     * @param $value
     */
    public function SetAct_name($value)
    {
        $this->values['act_name'] = $value;
    }

    /**
     * 获取设置活动名称
     *
     * @return mixed
     */
    public function GetAct_name()
    {
        return $this->values['act_name'];
    }

    /**
     * 检测活动名称是否设置
     *
     * @return bool
     */
    public function IsAction_nameSet()
    {
        return array_key_exists('act_name', $this->values);
    }
    /**
     * 设置备注
     *
     * @param $value
     */
    public function SetRemark($value)
    {
        $this->values['remark'] = $value;
    }

    /**
     * 获取备注信息
     *
     * @return mixed
     */
    public function GetRemark()
    {
        return $this->values['remark'];
    }

    /**
     * 检测备注信息是否设置
     *
     * @return bool
     */
    public function IsRemarkSet()
    {
        return array_key_exists('remark', $this->values);
    }

    /**
     * 设置红包金额设置方式（裂变红包需要）
     *
     * @deprecated ALL_RAND—全部随机,商户指定总金额和红包发放总人数，由微信支付随机计算出各红包金额
     * @param $amt_type
     */
    public function SetAmt_type($amt_type)
    {
        $this->values['amt_type'] = $amt_type;
    }

    /**
     * 获取红包金额设置方式（裂变红包需要）
     *
     * @return mixed
     */
    public function GetAmt_type()
    {
        return $this->values['amt_type'];
    }

    /**
     * 验证红包金额设置方式是否存在（裂变红包需要）
     *
     * @return bool
     */
    public function IsAmt_typeSet()
    {
        return array_key_exists('amt_type', $this->values);
    }

    /**
     * 设置红包订单类型（查询红包信息参数 当前只有个值：MCHT）
     *
     * @deprecated MCHT:通过商户订单号获取红包信息。
     * @param $bill_type
     */
    public function SetBill_type($bill_type)
    {
        $this->values['bill_type'] = $bill_type;
    }

    /**
     * 获取红包订单类型
     *
     * @return mixed
     */
    public function GetBill_type()
    {
        return $this->values['bill_type'];
    }

    /**
     * 检测红包类型是否存在
     *
     * @return bool
     */
    public function IsBill_typeSet()
    {
        return array_key_exists('bill_type', $this->values);
    }
    /**
     * 设置场景 （非必须参数）
     *
     * @desc PRODUCT_1:商品促销 PRODUCT_2:抽奖 PRODUCT_3:虚拟物品兑奖 PRODUCT_4:企业内部福利 PRODUCT_5:渠道分润 PRODUCT_6:保险回馈 PRODUCT_7:彩票派奖 PRODUCT_8:税务刮奖
     * @param $value
     */
    public function SetScene_id($value)
    {
        $this->values['scene_id'] = $value;
    }

    /**
     * 获取设置场景（非必传参数）
     *
     * @return mixed
     */
    public function GetScene_id()
    {
        return $this->values['scene_id'];
    }
    /**
     * 设置活动信息（非必传参数）
     *
     * @param $value
     */
    public function SetRisk_info($value)
    {
        $this->values['risk_info'] = $value;
    }

    /**
     * 获取活动信息（非必传参数）
     * @return mixed
     */
    public function GetRisk_info()
    {
        return $this->values['risk_info'];
    }


}