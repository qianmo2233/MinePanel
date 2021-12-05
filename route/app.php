<?php

use thans\jwt\middleware\JWTAuthAndRefresh;
use think\facade\Route;

Route::resource('user', 'User')->middleware(JWTAuthAndRefresh::class);
Route::rule('token/get', 'Token/get');
