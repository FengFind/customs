<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 9:05
 */

namespace app\admin\controller;


class Charts extends Common
{
    public function index() {
        $type = $this->request->param('type');
        switch ($type) {
            case '1':
                return $this->fetch('charts-1');
                break;
            case '2':
                return $this->fetch('charts-2');
                break;
            case '3':
                return $this->fetch('charts-3');
                break;
            case '4':
                return $this->fetch('charts-4');
                break;
            case '5':
                return $this->fetch('charts-5');
                break;
            case '6':
                return $this->fetch('charts-6');
                break;
            case '7':
                return $this->fetch('charts-7');
                break;
            default:
                break;
        }
    }
}