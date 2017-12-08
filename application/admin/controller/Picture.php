<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/28
 * Time: 17:16
 */

namespace app\admin\controller;


class Picture extends Common
{
    public function index() {
        return $this->fetch('picture-list');
    }
    public function add() {
        return $this->fetch('picture-add');
    }
    public function show() {
        return $this->fetch('picture-show',[],['temp'=>'/huiAdmin/public/temp']);
    }
}