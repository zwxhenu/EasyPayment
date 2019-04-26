<?php

namespace EasyPayment\payment\wxpay\lib;

class WxPayException extends \Exception
{

    public function errorMessage()
    {
        return $this->getMessage();
    }
}
