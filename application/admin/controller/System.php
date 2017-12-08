<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 8:49
 */

namespace app\admin\controller;


class System extends Common
{
    public function index() {
        return $this->fetch('system-base');
    }
    public function category() {
        return $this->fetch('system-category');
    }
    public function categoryAdd() {
        return $this->fetch('system-category-add');
    }
    public function data() {
        return $this->fetch('system-data');
    }
    public function shielding() {
        return $this->fetch('system-shielding');
    }
    public function log() {
        return $this->fetch('system-log');
    }
}