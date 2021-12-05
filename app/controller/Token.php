<?php

namespace app\controller;

use app\BaseController;
use app\modal\UserModal;
use app\response\ErrorResponse;
use app\response\TokenResponse;
use app\util\Encryption;
use app\validate\LoginValidate;
use thans\jwt\facade\JWTAuth;
use think\Request;
use think\Response;

class Token extends BaseController
{
    public function get(Request $request) : Response
    {
        validate(LoginValidate::class)->check($request->param());
        $user = (new UserModal)->where('username', $request['username'])->findOrEmpty();
        if ($user->isEmpty()) return json((new ErrorResponse)->build('203', 'Wrong username or password'));
        if (Encryption::encrypt($user->password) !== $request['password']) return json((new ErrorResponse)->build('203', 'Wrong username or password'));
        $token = JWTAuth::builder(['uuid'=>$user->uuid]);
        return json((new TokenResponse)->build('User token created', $token));
    }
}