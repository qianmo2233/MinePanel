<?php

namespace app\response;

class TokenResponse
{
    public $code;
    public $msg;
    public $token;

    public function build($msg, $token): TokenResponse
    {
        $this->code = 200;
        $this->msg = $msg;
        $this->token = $token;
        return $this;
    }
}