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
     * 输出过滤
     *
     * @param string|array $subject
     * @return mixed
     */
    public function trimPrint($subject)
    {
        $search = [',', "'", "\r\n", "\n", "\r", "\t"];
        $replace = ['，', '’', ' ', ' ', ' ', ' '];

        return str_ireplace($search, $replace, $subject);
    }
    /**
     * 浮点数比较 1 == 1
     *
     * @param $f1
     * @param $f2
     * @param int $precision
     * @return bool | ==
     */
    function floatEq($f1, $f2, $precision = 4)
    {
        $f1 = number_format($f1, $precision, '.', '');
        $f2 = number_format($f2, $precision, '.', '');
        $e = pow(10, $precision);
        $i1 = intval($f1 * $e);
        $i2 = intval($f2 * $e);

        return ($i1 == $i2);
    }
    /**
     * gbk编码转成utf8编码
     *
     * @param $var
     * @return array|mixed|string
     */
    public function gbkToUtf8($var)
    {
        if (is_array($var)) {
            foreach ($var as $k => $str) {
                $var[$k] = gbkToUtf8($str);
            }
        } else {
            $var = mb_convert_encoding($var, 'utf-8', 'gbk');
        }

        return $var;
    }
    /**
     * utf-8编码转成gbk编码
     *
     * @param $var
     * @return array|mixed|string
     */
    public function utf8ToGbk($var)
    {
        if (is_array($var)) {
            foreach ($var as $k => $str) {
                $var[$k] = gbkToUtf8($str);
            }
        } else {
            $var = mb_convert_encoding($var, 'gbk', 'utf-8');
        }

        return $var;
    }
    /**
     * 生成签名
     *
     * @param array $params
     * @param  string $app_secret
     * @return array|bool
     */
    public function makeSign($params, $app_secret)
    {
        ksort($params);
        $stringToBeSigned = $app_secret;

        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                if (get_magic_quotes_gpc() == 1 && !is_array($v)) {
                    $v = stripslashes($v);
                }
                $stringToBeSigned .= "$k$v";
            }
        }

        unset($k, $v);
        $stringToBeSigned .= $app_secret;

        return strtoupper(md5($stringToBeSigned));
    }

    /**
     * 验证签名
     *
     * @param array $params
     * @param  string $app_secret
     * @return array|bool
     */
    public function checkSign($params, $app_secret)
    {
        $sign = trim($params['sign']);
        unset($params['sign']);
        ksort($params);
        $stringToBeSigned = $app_secret;

        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                if (get_magic_quotes_gpc() == 1 && !is_array($v)) {
                    $v = stripslashes($v);
                }
                $stringToBeSigned .= "$k$v";
            }
        }

        unset($k, $v);
        $stringToBeSigned .= $app_secret;
        $check_sign = strtoupper(md5($stringToBeSigned));

        if (strcmp($check_sign, $sign) !== 0) {
            return false;
        }

        return true;
    }
    /**
     * 获取请求ip
     *
     * @return string
     */
    public function ip()
    {
        $ip = '';
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
    }
    /**
     * 随机字符串
     *
     * @param int $len
     * @param string $format
     * @return string
     */
    public function randomString($len = 8, $format = 'ALL')
    {
        $is_abc = $is_number = 0;
        $string = $tmp = '';
        switch ($format) {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 'CHAR':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        mt_srand((double)microtime() * 1000000 * getmypid());

        while (strlen($string) < $len) {
            $tmp = substr($chars, (mt_rand() % strlen($chars)), 1);
            if (($is_number != 1 && is_numeric($tmp) && $tmp > 0) || $format == 'CHAR') {
                $is_number = 1;
            }
            if (($is_abc != 1 && preg_match('/[a-zA-Z]/', $tmp)) || $format == 'NUMBER') {
                $is_abc = 1;
            }
            $string .= $tmp;
        }

        if ($is_number != 1 || $is_abc != 1 || empty($string)) {
            $string = randomString($len, $format);
        }

        return $string;
    }
    /**
     * 发送POST请求
     *
     * @param string $url 请求路径
     * @param array $postFields POST数据
     * @param int $timeout 超时时间
     * @return bool|mixed
     */
    public function curlPost($url, $postFields = [], $timeout = 20)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        //curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = "";
            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) // 判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else {
                    $postFields[$k] = new CURLFile(trim($v, '@'));
                    $postMultipart = true;
                }
            }
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        } elseif (is_string($postFields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                return false;
            }
        }

        return $response;
    }

    /**
     * 发送GET请求
     *
     * @param string $url 请求路径
     * @param array $getFields 请求数据
     * @param int $timeout 超时时间
     * @return bool|mixed
     */
    public function curlGet($url, $getFields = [], $timeout = 20)
    {
        if (is_array($getFields) && 0 < count($getFields)) {
            $getBodyString = "";
            foreach ($getFields as $k => $v) {
                $getBodyString .= "$k=" . urlencode($v) . "&";
            }
            unset($k, $v);
            $url .= '?' . substr($getBodyString, 0, -1);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return false;
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                return false;
            }
        }

        return $response;
    }
    /**
     * 获取有效字段
     *
     * @param string $user_fields 逗号分割字符串，用户输入字段
     * @param string $allow_fields 逗号分割字符串，系统允许字段
     * @return string
     */
    public function getValidField($user_fields, $allow_fields)
    {
        $user_fields = trim($user_fields);
        $allow_fields = trim($allow_fields);
        $user_fields_arr = explode(',', $user_fields);
        $user_fields_arr = array_map('trim', $user_fields_arr);
        $user_fields_arr = array_map('strtolower', $user_fields_arr);
        $allow_fields_arr = explode(',', $allow_fields);
        $allow_fields_arr = array_map('trim', $allow_fields_arr);
        $allow_fields_arr = array_map('strtolower', $allow_fields_arr);
        $valid_fields = array_intersect($user_fields_arr, $allow_fields_arr);

        foreach ($valid_fields as $k => $v) {
            if (empty($v)) {
                unset($valid_fields[$k]);
            }
        }

        return implode(',', $valid_fields);
    }
    /**
     * 获取有效数据
     *
     * @param string $user_fields 逗号分割字符串，用户输入字段
     * @param string $allow_fields 逗号分割字符串，系统允许字段
     * @param $data
     * @return array|bool|object
     */
    public function getValidData($user_fields, $allow_fields, $data)
    {
        if (is_object($data)) {
            $is_obj = true;
        } elseif (is_array($data)) {
            $is_obj = false;
        } else {
            return false;
        }

        $valid_fields = getValidField($user_fields, $allow_fields);

        if (empty($valid_fields)) {
            if ($is_obj) {
                return (object)[];
            } else {
                return [];
            }
        }

        $valid_fields_arr = explode(',', $valid_fields);

        foreach ($data as $k => $item) {
            if (!in_array($k, $valid_fields_arr)) {
                if ($is_obj) {
                    unset($data->$k);
                } else {
                    unset($data[$k]);
                }
            }
        }

        return $data;
    }
    /**
     * 打印数据结果
     *
     * @param $code
     * @param $msg
     * @param array $data
     * @return array
     */
    public function alertInfo($code, $msg, $data = array())
    {
        return array('code' => $code, 'msg' => $msg, 'data' => $data);
    }


}