<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/28
 * Time: 9:11
 */

namespace app\admin\controller;


use app\common\controller\Excel;
use think\Loader;

class Index extends Common
{
    public function index() {
        return $this->fetch("index");
    }
    public function welcome() {
        return $this->fetch('welcome');
    }

    /**
     * 设置excel表字段
     * @return mixed
     */
//    public function setFields() {
//        $excel = new Excel();
//        $excel->setFilePath(ROOT_PATH."demo.xls");
//        $datas = $excel->getSheetsContent();
//        $orderHead = Loader::model('OrderHead');
//        $headFields = $orderHead->getTableFields();
//        $orderList = Loader::model('OrderGoods');
//        $listFields = $orderList->getTableFields();
//        return $this->fetch('excel-design',['data_arr'=>$datas,'headFields'=>$headFields,'listFields'=>$listFields]);
//    }

//    public function saveDatas() {
//        $fields = $this->request->post();
//        foreach ($fields as $k => $val) {
//            if (strpos($k,'head')) {
//                $headarr[] = $val;
//            }else{
//                $listarr[] = $val;
//            }
//        }
//        $excel = new Excel();
//        $excel->setFilePath(ROOT_PATH."demo.xls");
//        $datas = $excel->getSameOrder($excel->getSheetsContent(),0);
//        $orderHead = Loader::model('OrderHead');
//        $orderHead->saveDatas($datas,$headarr,$listarr);
//    }
//
//    public function exportDatas() {
//        $excel = new Excel();
//        $orderHead = Loader::model("OrderHead");
//        $orderList = Loader::model("OrderGoods");
//        $datas = $orderHead->alias('a')->join("{$orderList->getTable()} b","a.order_no = b.order_no")->field('*,b.note as listnote')->select();
//        $excel->exportExcel($datas);
//    }
}