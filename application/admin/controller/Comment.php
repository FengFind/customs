<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 9:32
 */

namespace app\admin\controller;


class Comment extends Common
{
    public function index() {
        return $this->fetch('feedback-list');
    }
}