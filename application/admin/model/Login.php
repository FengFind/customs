<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/17
 * Time: 9:47
 */
namespace app\admin\model;
use think\Validate;
use think\Request;
use think\Loader;
use think\Db;
use think\Session;

class Login extends \think\Model {
    protected $data;
    public function checkLogin($request) {
//        验证数据合法性
        $validate = Loader::validate('User');
        if (!$validate->check($request)) {
            return $this->setData($validate->getError(),1001);
        }
//        核对用户信息
        $admin = new Admin();
        $result = $admin
            ->where('username',$request['username'])
            ->find();

        if ($result) {
            if (md5($request['password']) == $result['password']) {
//                if ($result['status']) {
//                    return $this->setData('用户已登录',1004);
//                }
                Session::set('username',$request['username']);
                Session::set('uid',$result['id']);
                $admin->save(['ip'=>$request['ip']],['username'=>$request['username']]);
                return $this->setData('',0000);
            }else{
                return $this->setData('密码错误',1003);
            }
        }else{
            return $this->setData('用户不存在',1002);
        }
    }

    public function drop() {
        Session::delete('username');
        return $this->setData('',0000);
    }

    public function setData($info,$code) {
        $this->data['errorInfo'] = $info;
        $this->data['code'] = $code;
        return $this->data;
    }
}