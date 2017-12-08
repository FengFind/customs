<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/28
 * Time: 15:07
 */

namespace app\admin\controller;


class Article extends Common
{
    public function index() {
        return $this->fetch('article-list');
    }
    public function add() {
        return $this->fetch('article-add');
    }
    public function category() {
        return $this->fetch('article-class');
    }
    public function edit() {
        return $this->fetch('article-class-edit');
    }
}