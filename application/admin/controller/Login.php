<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/28
 * Time: 9:43
 */

namespace app\admin\controller;

use think\Request;
use think\Loader;

class Login extends \think\Controller {
    //登录
    public function index() {
        if (Request::instance()->post()) {
            $request = Request::instance()->post();
            $request['ip'] = Request::instance()->ip();
//            调用验证业务逻辑，返回验证数据集$data
            $data = Loader::model('login')->checkLogin($request);
            if ($data['code']!=0000) {
                switch ($data['code']) {
                    case '1004':
                        $this->error($data['errorInfo'],'Index/index','',1);
                        break;
                    default:
                        $this->error($data['errorInfo'],'Login/index','',1);
                        break;
                }
            }else {
                $this->success('登录成功','Index/index','',1);
            }
        }else{
            return $this->fetch('login');
        }
    }
//    退出
    public function logout() {
        $data = Loader::model('login')->drop();
        if ($data['code']==0000) {
            return $this->fetch('Login/login');
        }else {
            $this->error($data['errorInfo'],'Index/index','',1);
        }
    }
}