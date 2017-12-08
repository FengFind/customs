<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 16:39
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class OrderGoods extends Model
{
//    public $datas_fill = [];
//    public function saveMoreDatas($rsame,$rdiff,$garr,$orderIndex) {
//        $alldatas = null;
//        $datas = null;
//        foreach ($garr as $k => $val) {
//            if (array_key_exists($k,$rsame)&&$val) {
//                $datas[$val] = $rsame[$k];
//            }
//        }
//        foreach ($rdiff as $row) {
//            foreach ($garr as $i => $val) {
//                if ($val&&array_key_exists($i,$row)) {
//                    $datas[$val] = $row[$i];
//                }
//            }
//            $datas['order_no'] = $row[$orderIndex];
//            $datas = array_merge($datas,$this->datas_fill);
//            $alldatas[] = $datas;
//        }
//        try {
//            OrderGoods::startTrans();
//            OrderGoods::saveAll($alldatas);
//            OrderGoods::commit();
//            return [
//                "code" => "0000",
//                "msg" => "success"
//            ];
//        }catch (\Exception $e) {
//            OrderGoods::rollback();
//            return [
//                "code" => "0002",
//                "error" => $e->getMessage()
//            ];
//        }
//    }
//
//    public function saveSingleData($record,$garr,$orderIndex) {
//        foreach ($garr as $k => $val) {
//            if ($val&&array_key_exists($k,$record)) {
//                $datas[$val] = $record[$k];
//            }
//        }
//        $datas['order_no'] = $record[$orderIndex];
//        $datas = array_merge($datas,$this->datas_fill);
//        try {
//            OrderGoods::startTrans();
//            OrderGoods::data($datas);
//            OrderGoods::isUpdate(false)->save($datas);
//            OrderGoods::commit();
//            return [
//                "code" => "0000",
//                "msg" => "success"
//            ];
//        }catch (\Exception $e) {
//            OrderGoods::rollback();
//            return [
//                "code" => "0002",
//                "error" => $e->getMessage()
//            ];
//        }
//    }

    public function saveGoodsData($datas) {
        $orderGoods = new OrderGoods();
        $ndatas = [];
        $i = 0;
        foreach ($datas as $key => $record) {
            $order_no = $record['order_no'];
            foreach ($record['goods_info'] as $goods_datas) {
                $goods_datas['order_no'] = $order_no;
                $goods_datas['currency'] = $goods_datas['item_currency'];
                $ndatas[$i] = $goods_datas;
                $i++;
            }
        }
        try {
            $orderGoods->startTrans();
//            $orderGoods->data($ndatas);
            $orderGoods->isUpdate(false)->allowField(true)->saveAll($ndatas);   //->field('currency as cur,item_currency as currency')
            $orderGoods->commit();
            return [
                "code" => "0000",
                "msg" => "success"
            ];
        }catch (\Exception $e) {
            $orderGoods->rollback();
            return [
                "code" => "0002",
                "error" => $e->getMessage()
            ];
        }
    }
}