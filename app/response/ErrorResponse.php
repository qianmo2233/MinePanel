<?php

namespace app\response;

class ErrorResponse
{
    public $code;
    public $msg;

    public function build($code, $msg): ErrorResponse
    {
        $this->code = $code;
        $this->msg = $msg;
        return $this;
    }
}