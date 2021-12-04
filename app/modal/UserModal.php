<?php

namespace app\modal;

use think\Model;

class UserModal extends Model
{
    protected $pk = 'uuid';
    protected $table = 'user';
    protected $schema = [
        'uuid'=>'string',
        'username'=>'string',
        'status'=>'int',
        'admin'=>'int',
        'password'=>'string',
    ];
}