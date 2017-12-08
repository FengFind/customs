<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28
 * Time: 11:01
 */

namespace app\admin\model;


use think\Db;
use think\Loader;
use think\Model;
use app\admin\validate\Order as OrderValidate;

class OrderPreview extends Model {
    public function saveData($datas,$modify=false) {
        $oPreview = new OrderPreview();
        $fields = OrderPreview::getTableFields();
        $fields_no = ["id","oh_note","batch_no","item_no","item_describe","bar_code","qty2","unit2",'goods_note','gjcode']; //"pay_code","pay_name","pay_transaction_id",
        //excel模板末尾新添的订单头字段
        $fields_new = ['bill_no','voyage_no','pack_no'];
         $fields_no = array_merge($fields_no,$fields_new);
        $field_index1 = array_search('qty1',$fields);
        $field_index2 = array_search('unit',$fields);
        $fields[$field_index1] = 'unit';
        $fields[$field_index2] = 'qty1';
        $fields = array_merge(array_diff($fields,$fields_no),$fields_new);
        $batch_no = date('YmdHis').random_int(1000,9999);
//        $order_no = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $error_arr = null;
        if (!$modify) {
            $ndatas = null;
            foreach ($datas as $key => $rows) {
                foreach ($rows as $k => $row) {
                    $ndatas[] = $row;
                }
            }
            foreach ($ndatas as $key => $row) {
                foreach ($fields as $k => $val) {
                    $newdatas[$key][$val] = trim($row[$k]);
                }
                $newdatas[$key]['batch_no'] = $batch_no;
//                $newdatas[$key]['order_no'] = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))).random_int(1000,9999), -8, 8);
            }
            $error_info = $this->checkOrder($newdatas);
            if (count($error_info)>0) {
                $i = 0;
                foreach ($error_info as $key => $row) {
                    $error_arr[$i] = $newdatas[$key];
                    $error_arr[$i]['error_info'] = $error_info[$key];
                    $error_arr[$i]['autonum'] = $i+1;
                    unset($newdatas[$key]);
                    $i++;
                }
            }
        }else{
            if (isset($datas['ebp_name'])) {   //判断是否为一维数组
                $newdatas[] = $datas;
            }else{
                $newdatas = $datas;
            }
            $error_info = $this->checkOrder($newdatas);
            if (count($error_info)>0) {
                $i = 0;
                foreach ($error_info as $key => $row) {
                    $error_arr[$i] = $newdatas[$key];
                    $error_arr[$i]['error_info'] = $error_info[$key];
                    unset($newdatas[$key]);
                    $i++;
                }
            }
            foreach ($newdatas as $k => $row) {
                if (array_key_exists('id',$row))
                unset($newdatas[$k]['id']);
            }
        }
        $oPreview->startTrans();
        try{
            $oPreview->allowField(true)->saveAll($newdatas);
            $oPreview->commit();
            return [
                'code' => '0000',
                'msg' => '更改成功！',
                'data' => ['batch_no'=>$batch_no,'error_arr'=>$error_arr],
            ];
        }catch (\Exception $e) {
            return [
                'code' => '0005',
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 订单信息验证
     * @param $datas
     * @return null
     */
    public function checkOrder($datas) {
//        $res = Loader::model('OrderHead')->field('order_no')->select();
        $check_arr = null;
//        $orderNo_arr = null;
//        foreach ($res as $val) {
//            $orderNo_arr[] = $val['order_no'];
//        }
//        foreach ($datas as $val) {
//            $check_arr[] = $val['order_no'];
//        }
//        $orderNo_arr == null ? $duplicate_key=[] : $duplicate_key = array_keys(array_intersect($check_arr,$orderNo_arr));
        $validate = Loader::validate('Order');
        foreach ($datas as $k => $row) {
            if (!$validate->scene('preview')->batch()->check($row)) {
                $check_arr[$k] = $validate->getError();
            }
        }
        return $check_arr;
    }
}