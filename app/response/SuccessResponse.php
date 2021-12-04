<?php

namespace app\response;

class SuccessResponse
{
    public $code;
    public $msg;

    public function build($msg): SuccessResponse
    {
        $this->code = 200;
        $this->msg = $msg;
        return $this;
    }
}