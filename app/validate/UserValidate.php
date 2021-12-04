<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class UserValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username'=>'require',
        'password'=>'require',
        'admin'=>'number|max:1|between:0,1',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => 'Username is required',
        'password.require' => 'Password is required',
        'admin.max' => 'admin must be 0 or 1',
        'admin.between' => 'admin must be 0 or 1'
    ];
}
