<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 10:03
 */
namespace app\admin\validate;
use think\Validate;

class User extends Validate {
    protected $rule = [
        'username' => 'require|max:20|token',
        'password' => 'require',
        'captcha' => 'require|captcha'
    ];
    protected $message = [
        'username.token' => '页面已失效，请刷新后重试！'
    ];
}