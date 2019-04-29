<?php

/**
 * 接口调用结果类
 *
 */
namespace EasyPayment\payment\wxpay\lib;

use EasyPayment\payment\wxpay\lib\WxPayDataBase;

class WxPayResults extends WxPayDataBase
{

    /**
     * 检测签名
     */
    public function CheckSign()
    {
        if (!$this->IsSignSet()) {
            return true;
        }

        $sign = $this->MakeSign();
        if ($this->GetSign() == $sign) {
            return true;
        }
        throw new WxPayException("签名错误！");
    }

    /**
     * 使用数组初始化
     *
     * @param array $array
     */
    public function FromArray($array)
    {
        $this->values = $array;
    }

    /**
     * 使用数组初始化对象
     * @param $array
     * @param bool $noCheckSign 是否检测签名
     * @return WxPayResults
     * @throws WxPayException
     */
    public static function InitFromArray($array, $noCheckSign = false)
    {
        $obj = new self();
        $obj->FromArray($array);
        if ($noCheckSign == false) {
            $obj->CheckSign();
        }
        return $obj;
    }

    /**
     * 将xml转为array
     *
     * @param string $xml
     * @param bool $check_sign
     * @return array
     * @throws WxPayException
     */
    public static function Init($xml, $check_sign = true)
    {
        $obj = new self();
        $obj->FromXml($xml);
        if ($check_sign === true) {
            $obj->CheckSign();
        }
        return $obj->GetValues();
    }
}