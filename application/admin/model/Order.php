<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/7
 * Time: 8:50
 */

namespace app\admin\model;

use app\admin\model\OrderPreview;
use app\common\controller\Excel;
use think\Loader;

use think\Model;

class Order extends Model {
    public $request;

    /**
     * 订单列表页获取数据
     * @return array
     */
    public function getOrderDatas($batch=null) {
        $limit = $this->request->param('limit');
        $page = $this->request->param('page');
        $orderModel = Loader::model("OrderHead");
        $order = ['a.batch_time'=>'desc'];
//        $batch = $this->request->param('batch');
//        $orderField = $this->request->get('field');
//        $orderType = $this->request->get('order');
        $alias = "a";
        $join = [
            ["OrderBatch b","a.batch_time = b.batch_time"],
//            ["OrderGoods c","b.order_no = c.order_no"],
        ];
        $where = $batch !== null ? ['b.batch_time'=>$batch] : [];
//        $where = ['b.batch_time'=>$batch];
//        $field = "*,@i:=@i+1 as autonum";
        $field = "*";
//        $orderBy = [$orderField => $orderType];
        //数据集序号设定
        $i = 1;
        if ($page>1) {
            $i = ($page-1)*$limit+1;
        }
//        $orderModel->query("set @i=".$i);
//        $data = $orderHead->alias($alias)->join($join)->field($field)->page($page,$limit)->order($orderBy)->select();
        $data = $orderModel->alias($alias)->join($join)->where($where)->order($order)->field($field)->page($page,$limit)->select();
        foreach ($data as $k => $record) {
            $data[$k]['autonum'] = $i++;
        }
        $dataCount = $orderModel->alias($alias)->where($where)->join($join)->count();
        $data_format = [
            "code" => 0,
            "msg" => "success",
            "count" => $dataCount,
            "data" => $data,
        ];
        return $data_format;
    }

    /**
     * 获取订单详细信息
     * @return array
     */
    public function getOrderDetails() {
        $order_no = $this->request->param("orderNo");
        $limit = $this->request->param("limit");
        $page = $this->request->param("page");
        $join = [["order_goods b","a.order_no = b.order_no"]];
        $where = ['a.order_no'=> $order_no];
        $field = ["*","a.currency"=>"currency","b.currency"=>"item_currency"];
        $orderHead = Loader::model("OrderHead");
        $data = $orderHead->alias('a')->join($join)->field($field)->where($where)->page($page,$limit)->select();
        $dataCount = $orderHead->alias('a')->join($join)->where($where)->count();
        $data_format = [
            "code" => 0,
            "msg" => "success",
            "count" => $dataCount,
            "data" => $data,
        ];
        return $data_format;
    }

    /**
     * 修改订单信息
     * @return array
     */
    public function editOrder() {
        $etype = $this->request->param("etype");
        $orderNo = $this->request->param("orderNo");
        $hres = $this->request->param("res");
        $data = $this->request->param("data/a");
        $validate = Loader::validate('Order');
        if ($etype == "head") {
            $data = $this->request->param();
            $result = $validate->scene('edit_head')->batch()->check($data);
            if (!$result) {
                $msg = '<font color="#DD514C">'.implode(',<br>',$validate->getError()).'</font>';
                return ['code'=>"0010",'msg'=>$msg];
            }
            try{
                $res=Loader::model("OrderHead")->allowField(true)->save($data,"order_no='".$data['order_no']."'");
            }catch (\Exception $e) {
                return ['code'=>"0002",'msg'=>$e];
            }
        }elseif ($etype == "goods") {
            $hres === '0000' ? $hres = 1 : $hres = 0;
            $lastres = '';
            foreach ($data as $k => $record) {
                $record['currency'] = $record['item_currency'];
                $result = $validate->scene('edit_goods')->batch()->check($record);
                if (!$result) {
                    $msg = '<font color="#DD514C">'.implode(',<br>',$validate->getError()).'</font>';
                    return ['code'=>"0010",'msg'=>$msg];
                }
                try{
                    $temp=Loader::model("OrderGoods")->allowField(true)->save($record,"id=".$record['id']);
                    $res = $temp || $lastres;
                }catch (\Exception $e) {
                    return ['code'=>"0002",'msg'=>$e];
                }
                $lastres = $temp;
            }
            $res = $hres || $res;
        }
        if ($res) {
            return ['code'=>"0000",'msg'=>"修改成功！"];
        }else{
            return ['code'=>"0001",'msg'=>"数据没有变更！"];
        }
    }

    /**
     * 修改订单信息（原版）
     * @return array
     */
//    public function editOrder() {
//        $etype = $this->request->param("etype");
//        $orderNo = $this->request->param("orderNo");
//        $data = $this->request->param("data/a");
//        $validate = Loader::validate('Order');
//        if ($etype == "head") {
//            $data = $this->request->param();
//            $result = $validate->scene('edit_head')->batch()->check($data);
//            if (!$result) {
//                $msg = '<font color="#DD514C">'.implode(',<br>',$validate->getError()).'</font>';
//                return ['code'=>"0010",'msg'=>$msg];
//            }
//            try{
//                $res=Loader::model("OrderHead")->allowField(true)->save($data,"order_no='".$data['order_no']."'");
//            }catch (\Exception $e) {
//                return ['code'=>"0002",'msg'=>$e];
//            }
//        }elseif ($etype == "goods") {
//            $data['currency'] = $data['item_currency'];
//            $result = $validate->scene('edit_goods')->batch()->check($data);
//            if (!$result) {
//                $msg = '<font color="#DD514C">'.implode(',<br>',$validate->getError()).'</font>';
//                return ['code'=>"0010",'msg'=>$msg];
//            }
//            try{
//                $res=Loader::model("OrderGoods")->allowField(true)->save($data,"id=".$data['id']);
//            }catch (\Exception $e) {
//                return ['code'=>"0002",'msg'=>$e];
//            }
//        }
//        if ($res) {
//            return ['code'=>"0000",'msg'=>"修改成功！"];
//        }else{
//            return ['code'=>"0001",'msg'=>"数据没有变更！"];
//        }
//    }

    /**
     * 删除订单
     * @return array
     */
    public function delOrder() {
        $orderNo = $this->request->param('order_no');
        $data = $this->request->param('data');
        $og_data_no = $this->request->param('og_data_no');
        $gnum = $this->request->param('gnum');
        $orderHead = Loader::model('OrderHead');
        $orderGoods = Loader::model('OrderGoods');
        $orderBatch = Loader::model('OrderBatch');
        if ($data==null&&$gnum==null) {
            $orderGoods::destroy(['order_no'=>$orderNo]);
            $res = $orderHead::destroy(['order_no'=>$orderNo]);
        }elseif($gnum==null&&isset($data)){
            foreach (explode('|',$data) as $val) {
                $orderGoods::destroy(['order_no'=>$val]);
                $res = $orderHead::destroy(['order_no'=>$val]);
                if (!$orderHead) break;
            }
        }elseif ($gnum!=null&&$data==null&&$og_data_no==null) {
            $res = $orderGoods::destroy(['order_no'=>$orderNo,'gnum'=>$gnum]);
        }elseif ($gnum!=null&&$og_data_no!=null) {
            foreach (explode('|',$gnum) as $val) {
                $res = $orderGoods::destroy(['order_no'=>$og_data_no,'gnum'=>$val]);
                if (!$orderGoods) break;
            }
            if (!$orderGoods::get(['order_no'=>$og_data_no])) {
                $orderHead::destroy(['order_no'=>$og_data_no]);
            }
        }
        if ($res) {
            $batch_results = $orderBatch->field("batch_time")->select();
            foreach ($batch_results as $row) {
                $where = ['b.batch_time'=>$row['batch_time']];
                if ($orderHead->alias('a')->join([["OrderBatch b","a.batch_time = b.batch_time"]])->where($where)->count()==0) {
                    $orderBatch->destroy(['batch_time'=>$row['batch_time']]);
                }
            }
            return ['code'=>'0000','msg'=>'success'];
        }else{
            return ['code'=>'0003','msg'=>'删除失败！'];
        }
    }

    /**
     * 导入excel数据
     * @return \think\response\Json
     */
    public function importData() {
        $file = $this->request->file("excelFile");
        $note = $this->request->post("note");
        $data_batch = $this->request->post('data_batch');
        $rootpath = 'public' . DS . 'uploads';
        $excelObj = new Excel();
        if ($data_batch!=null) {
            $clientname = $this->request->param('clientname');
            $clientId = Loader::model('Client')->field('id')->where(['client_name'=>$clientname,'delete_status'=>0])->find();
            if ($clientId === null) {
                return feedback('0003','客户不存在或已删除！');
            }
            $data = collection(Loader::model('OrderPreview')->all(['batch_no'=>$data_batch]))->toArray();
            foreach ($data as $record) {
//                $record = array_filter($record,function ($val){if ($val !== 'null') return $val;});
                array_shift($record);
                array_pop($record);
//                $ndata[] = array_values($record);
                $ndata[] = $record;
            }
            $ndata = $excelObj->getSameOrder($ndata,'logistics_no',false);
            $result = Loader::model("OrderBatch")->saveBatch($ndata, $note,$clientId['id']);
            if ($result["code"] == "0000") {
                return json($result);
            }else{
                return json([
                    "code" => $result["code"],
                    "msg" => $result["error"],
                ]);
            }
        }else{
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->validate(['ext'=>'xls,xlsx'])->move(ROOT_PATH.$rootpath);
            if($info){
                // 成功上传后 获取文件路径
                $filepath =ROOT_PATH.$rootpath.DS.$info->getSaveName();
                $excelObj->setFilePath($filepath);
                //获得excel表中所有记录
                $datas = $excelObj->getSameOrder($excelObj->getSheetsContent(),22, false);
                $res = $this->savePreviewData($datas,true);
                return $res;
//                $datas = $excelObj->getSameOrder($excelObj->getSheetsContent(),0);
//                array_shift($datas);
            }else{
                // 上传失败获取错误信息
                return json(['error'=>$file->getError()]);
            }
        }
    }

    /**
     * 获取预览数据
     * @return array
     */
    public function getPreviewData() {
        $batch_no = $this->request->param("batch_no");
        $limit = $this->request->param("limit");
        $page = $this->request->param("page");
        $where = ['batch_no'=> $batch_no];
        $field = "*,@i:=@i+1 as autonum";
        $orderPreview = Loader::model("OrderPreview");
        //数据集序号设定
        $i = 0;
        if ($page>1) {
            $i = ($page-1)*$limit;
        }
        $orderPreview->query("set @i=".$i);
        $data = $orderPreview->field($field)->where($where)->page($page,$limit)->select();
        $dataCount = $orderPreview->where($where)->count();
        $data_format = [
            "code" => 0,
            "msg" => "success",
            "count" => $dataCount,
            "data" => $data,
        ];
        return $data_format;
    }

    /**
     * 修改预览数据
     * @return array
     */
    public function editPreviewOrder() {
        $batch_no = $this->request->param("batch_no");
        $logistics_no = $this->request->param("logistics_no");
        $data = $this->request->param("data/a");
        $gnum = $data['gnum'];
        $ndata[] = $data;
        $orderPreview = new OrderPreview();
        $err_info = $orderPreview->checkOrder($ndata);
        if ($err_info!=null) {
            $orderPreview::destroy(['logistics_no'=>$logistics_no,'batch_no'=>$batch_no,'gnum'=>$gnum]);
            $res = $this->savePreviewData($ndata);
            return $res;
        }else{
            try{
                $res=Loader::model("OrderPreview")->allowField(true)->save($data,['id'=>$data['id']]);    //"order_no='".$data['order_no']."'"
                if ($res) {
                    return ['code'=>"0000",'msg'=>"修改成功！"];
                }else{
                    return ['code'=>"0001",'msg'=>"数据没有变更！"];
                }
            }catch (\Exception $e) {
                return ['code'=>"0002",'msg'=>$e];
            }
        }
    }

    /**
     * 保存预览数据
     * @param $datas
     * @param bool $first
     * @return mixed
     */
    public function savePreviewData($datas, $first=false) {
        if (!$first) {
            $res = Loader::model('OrderPreview')->saveData($datas,true);
        }else{
            $res = Loader::model('OrderPreview')->saveData($datas);
        }
        return $res;
    }

    /**
     * 删除预览数据
     * @return array
     */
    public function delPreviewOrder() {
        $logistics_no = $this->request->param('logistics_no');
        $gnum = $this->request->param('gnum');
        $logistics_nos = $this->request->param('logistics_nos');
        $batch_no = $this->request->param('batch_no');
        $delbatch = $this->request->param('delbatch');
        $remove = $this->request->param('remove');
        $orderPreview = Loader::model('OrderPreview');
        if ($logistics_no!=null&&$batch_no!=null) {
            $res = $orderPreview::destroy(['logistics_no'=>$logistics_no,'batch_no'=>$batch_no,'gnum'=>$gnum]);
        }elseif ($logistics_nos!=null&&$batch_no!=null) {
            foreach (explode('|',$logistics_nos) as $val) {
                $res = $orderPreview::destroy(['logistics_no'=>explode('@',$val)[0],'gnum'=>explode('@',$val)[1],'batch_no'=>$batch_no]);
                if (!$orderPreview) break;
            }
        }elseif ($delbatch != null) {
            $res = $orderPreview::destroy(['batch_no'=>$delbatch]);
        }elseif ($remove != null) {
            $outdate = date("Ymd",strtotime("-2day"));
            $res = $orderPreview::where('batch_no','<=',$outdate)->delete();
        }
        if ($res|$res=='0') {
            return ['code'=>'0000','msg'=>'success'];
        }else{
            return ['code'=>'0003','msg'=>'删除失败！'];
        }
    }

    /**
     * 导出时显示的批次信息
     * @return array
     */
    public function getOrderBatch($pid=null) {
        $limit = $this->request->param('limit');
        $page = $this->request->param('page');
        //因create_time字段是OrderHead自动生成，故应用OrderHead为主表作查询，否则会报错
        $orderModel = Loader::model("OrderHead");
        $orderBatch = Loader::model("OrderBatch");
        $alias = 'a';
        $where = $pid === null ? [] : ['com_id'=>$pid];
        $join = [['ceb_order_batch b','a.batch_time=b.batch_time']];
        $field = "b.batch_time,b.batch_note,a.create_time,any_value(a.declare_status) as declare_status";
        $group = 'b.batch_time,b.batch_note,a.create_time';
        //数据集序号设定
        $i = 1;
        if ($page>1) {
            $i = ($page-1)*$limit+1;
        }
        $orderModel->query("set @i=".$i);
        $data = $orderModel->alias($alias)->join($join)->where($where)->group($group)->field($field)->page($page,$limit)->select();
        foreach ($data as $k => $record) {
            $data[$k]['autonum'] = $i++;
        }
        $dataCount = $orderModel->alias($alias)->where($where)->group($group)->join($join)->count();
        $data_format = [
            "code" => 0,
            "msg" => "success",
            "count" => $dataCount,
            "data" => $data,
        ];
        return $data_format;
    }
}