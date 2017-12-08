<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 11:11
 */

namespace app\admin\controller;


class User extends Common
{
    public function index() {
        return $this->fetch('user-list');
    }
    public function add() {
        return $this->fetch('user-add');
    }
    public function passwordEdit() {
        return $this->fetch('user-password-edit');
    }
    public function show() {
        return $this->fetch('user-show');
    }
}