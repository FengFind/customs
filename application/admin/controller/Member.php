<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/28
 * Time: 10:06
 */

namespace app\admin\controller;


class Member extends Common
{
    public function index() {
        return $this->fetch('member-list');
    }
    public function add() {
        return $this->fetch('member-add');
    }
    public function del() {
        return $this->fetch('member-del');
    }
    public function show() {
        return $this->fetch('member-show');
    }
    public function changePassword() {
        return $this->fetch('change-password');
    }
    public function browse() {
        return $this->fetch('member-record-browse');
    }
    public function download() {
        return $this->fetch('member-record-download');
    }
    public function share() {
        return $this->fetch('member-record-share');
    }
}