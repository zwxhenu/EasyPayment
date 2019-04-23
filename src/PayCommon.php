<?php
/**
 *
 * Created by PhpStorm.
 * User: user
 * Date: 2019/4/23
 * Time: 14:22
 */

namespace EasyPayment\payment;


class PayCommon
{
    /**
     * 打印数据结果
     *
     * @param $code
     * @param $msg
     * @param array $data
     *
     * @return array
     */
    public function alertInfo($code, $msg, $data = array())
    {
        return array('code' => $code, 'msg' => $msg, 'data' => $data);
    }
}