<?php
namespace Shark\Library\Service\pay\wxpay\lib;
class WxPayException extends \Exception
{

    public function errorMessage()
    {
        return $this->getMessage();
    }
}
