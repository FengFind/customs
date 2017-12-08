<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/30
 * Time: 16:38
 */

namespace app\admin\model;


use think\Db;
use think\Loader;
use think\Model;
use traits\model\SoftDelete;

class OrderHead extends Model
{
    protected $autoWriteTimestamp = 'datetime';
    // 关闭自动写入update_time字段
    protected $updateTime = false;

    //新增订单头记录
    public function saveDatas($datas,$harr=null,$garr=null,$batch=null) {
        $orderHead = new OrderHead();
        $ordergoods = Loader::model('OrderGoods');
        //excel表中不存在，数据库中不能为空的字段
//        $datas_fill = ["batch_time"=>$batch];
        //相同订单中的订单编号$records['diff']的数组下标
//        $orderIndex = 0;
//        foreach ($datas as $records) {
//            //处理相同订单的数据
//            if (array_key_exists('same',$records)) {
//                $datas = null;
//                foreach ($harr as $k => $val) {
//                    if (array_key_exists($k,$records['same'])&&$val) {
//                        $datas[$val] = $records['same'][$k];
//                    }
//                    if (array_key_exists($k,$records['diff'][0])&&$val&&$k!=$orderIndex) {
//                        //将该字段相加
//                        $sum = 0;
//                        foreach ($records['diff'] as $diff_row) {
//                            $sum+= $diff_row[$k];
//                        }
//                        $datas[$val] = $sum;
//                    }
//                }
//                $datas = array_merge($datas,$datas_fill);
//                try {
//                    $orderHead->startTrans();
//                    $orderHead->data($datas);
//                    $orderHead->isUpdate(false)->save();
//                    $res = $ordergoods->saveMoreDatas($records['same'], $records['diff'], $garr,$orderIndex);
//                    if ($res['code']!="0000") {
//                        return $res;
//                    }
//                    $orderHead->commit();
//                }catch (\Exception $e) {
//                    $orderHead->rollback();
//                    return [
//                        "code" => "0001",
//                        "error" => $e->getMessage(),
//                    ];
//                }
//            }else{
//                $datas = null;
//                foreach ($harr as $k => $val) {
//                    if ($val) {
//                        $datas[$val] = $records[$k];
//                    }
//                }
//                try {
//                    $orderHead->startTrans();
//                    $datas = array_merge($datas, $datas_fill);
//                    $orderHead->data($datas);
//                    $orderHead->isUpdate(false)->save($datas);
//                    $res = $ordergoods->saveSingleData($records, $garr,$orderIndex);
//                    if ($res['code']!="0000") {
//                        return $res;
//                    }
//                    $orderHead->commit();
//                }catch (\Exception $e) {
//                    $orderHead->rollback();
//                    return [
//                        "code" => "0001",
//                        "error" => $e->getMessage(),
//                    ];
//                }
//            }
//        }
        try {
            $orderHead->startTrans();
//            $orderHead->data($datas);
            $orderHead->isUpdate(false)->allowField(true)->saveAll($datas);
            $res = $ordergoods->saveGoodsData($datas);
            if ($res['code']!="0000") {
                return $res;
            }
            $orderHead->commit();
        }catch (\Exception $e) {
            $orderHead->rollback();
            return [
                "code" => "0001",
                "error" => $e->getMessage(),
            ];
        }
        if (empty($datas)) {
            return [
                "code" => "0100",
                "error" => "表非法"
            ];
        }else{
            return [
                "code" => "0000",
                "msg" => "success"
            ];
        }
    }

}