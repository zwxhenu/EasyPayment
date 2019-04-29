<?php
/**
 * 生成二维码图片操作类
 *
 * Created by PhpStorm.
 * User: user
 * Date: 2019/4/29
 * Time: 10:54
 */
namespace EasyPayment\payment;

use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

class QrCode
{
    /**
     * 生成二维码
     *
     * @param $content
     * @param $dst_dir
     * @param $host_url
     * @param null $size
     * @param null $margin
     * @return bool|mixed
     */
    public static function make($content, $dst_dir, $host_url, $size = null, $margin = null)
    {
        $content = trim($content);
        if (empty($content)) {
            return false;
        }
        $sign = md5($content . $size . $margin);
        $qrcode_file = $dst_dir . DIRECTORY_SEPARATOR . substr($sign, -2) . DIRECTORY_SEPARATOR . substr($sign, 0, 2) . DIRECTORY_SEPARATOR . $sign . '.png';
        if (file_exists($qrcode_file)) {
            return $host_url.$qrcode_file;
        }
        $qrcode = new BaconQrCodeGenerator();
        $qrcode = $qrcode->format('png');
        if (is_numeric($margin)) {
            $qrcode = $qrcode->margin($margin);
        }
        if (is_numeric($size)) {
            $qrcode = $qrcode->size($size);
        }
        if (!is_dir(dirname($qrcode_file))) {
            @mkdir(dirname($qrcode_file), 0700, true);
        }
        $qrcode->generate($content, $qrcode_file);

        return $host_url.$qrcode_file;
    }
}