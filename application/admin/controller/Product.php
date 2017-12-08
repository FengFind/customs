<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/28
 * Time: 17:41
 */

namespace app\admin\controller;


class Product extends Common
{
    public function index() {
        return $this->fetch('product-list');
    }
    public function brand() {
        return $this->fetch('product-brand');
    }
    public function category() {
        return $this->fetch('product-category');
    }
    public function categoryAdd() {
        return $this->fetch('product-category-add');
    }
    public function add() {
        return $this->fetch('product-add');
    }
    public function codeing() {
        return $this->fetch('codeing');
    }
}