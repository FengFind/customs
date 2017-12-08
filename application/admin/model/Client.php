<?php
/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 2017/8/17
 * Time: 15:48
 */
namespace app\admin\model;

use think\Model;
use think\Validate;
use think\Db;
use think\Loader;
use think\Session;

class client extends Model {
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime  = false;
    public $request;

    /**
     *设置创建时间字段值为不自动创建
     */
    public function noCreate() {
        $this->updateTime = false;
    }

    /******************************************************客户列表*******************************************************************/

    /**
     * 获取客户列表
     * @return array
     */
    public function getClients($del=false) {
        $limit = $this->request->get('limit');
        $page = $this->request->get('page');
        $where = $del ? ['delete_status'=>1] : ['delete_status'=>0];
//        $field = "*,a.status as status,c.status as group_status,a.id as id";
        $field = "";
        $client = new \app\admin\model\Client();
        $data = $client->where($where)->page($page,$limit)->field($field)->select();
        $count = $client->where($where)->count();
        if ($count>0) {
            createNums($data,$page,$limit);
            $data_format = [
                "code" => 0,
                "msg" => "success",
                "count" => $count,
                "data" => $data,
            ];
            return $data_format;
        }else{
            return feedback('0001','无数据！');
        }
    }

    /**
     * 添加客户
     * @return array
     */
    public function addClient() {
        $clientname = $this->request->post('clientname');
        $phone = $this->request->post('phone');
        $email = $this->request->post('email');
        $note = $this->request->post('note');
        $validate = new Validate([
            'clientname' => 'require',
            'phone' => 'require',
            'email' => 'email'
        ]);
        if (!$validate->batch()->check(['clientname'=>$clientname,'phone'=>$phone])) {
            return [
                'code' => '0003',
                'msg' => $validate->getError()
            ];
        }
        $client = new \app\admin\model\Client();
        Db::startTrans();
        try{
            $client->data(['client_name'=>$clientname,'client_telephone'=>$phone,'client_email'=>$email,'client_note'=>$note]);
            $res = $client->isUpdate(false)->save();
            if ($res) {
                Db::commit();
                return [
                    'code' => '0000',
                    'msg' => '客户添加成功！'
                ];
            }
        }catch (\Exception $e) {
            Db::rollback();
            return [
                'code' => '0002',
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 修改客户信息
     * @return array
     */
    public function editclient() {
        $uid = $this->request->post('uid');
        $clientname = $this->request->post('clientname');
        $phone = $this->request->post('phone');
        $email = $this->request->post('email');
        $note = $this->request->post('note');
        $validate = new Validate([
            'clientname' => 'require',
            'phone' => 'require',
            'email' => 'email'
        ]);
        if (!$validate->batch()->check(['clientname'=>$clientname,'phone'=>$phone])) {
            return [
                'code' => '0003',
                'msg' => $validate->getError()
            ];
        }
        $client = new \app\admin\model\Client();
        Db::startTrans();
        try{
            $res = $client->isUpdate(true)->save(['client_telephone'=>$phone,'client_name'=>$clientname,'client_email'=>$email,'client_note'=>$note],['id'=>$uid]);
            if ($res) {
                Db::commit();
                return [
                    'code' => '0000',
                    'msg' => '修改成功！'
                ];
            }else{
                return [
                    'code' => '0000',
                    'msg' => '修改成功！'
                ];
            }
        }catch (\Exception $e) {
            Db::rollback();
            return [
                'code' => '0002',
                'msg' => $e->getMessage()
            ];
        }
    }

    /**
     * 删除客户
     * @return array
     */
    public function delClients($recover=false) {
        $id = $this->request->post('id');
        $data = $this->request->post('data');
        $client = Loader::model('client');
        $dstatus = $recover ? 0 : 1;
        $msg_prex = $recover ? '恢复' : '删除';
        $res = false;
        if ($data===null) {
            $res = $client::update(['id'=>$id,'delete_status'=>$dstatus]) or 0;
        }else if($data!==null) {
            foreach (explode('|',$data) as $val) {
                $res = $client::update(['id'=>$val,'delete_status'=>$dstatus]) or 0;
            }
        }
        if ($res) {
            return feedback('0000',$msg_prex.'成功！');
        }else{
            return feedback('0001',$msg_prex.'失败！');
        }
    }

    /**
     * 搜索客户
     * @return array
     */
    public function search() {
        $conditions = $this->request->param('data/a');
        $del = $this->request->param('del');
        $page =  $this->request->post('page');
        $limit =  $this->request->post('limit');
        $where = $del === null ? ['delete_status'=>0] : ['delete_status'=>1];
        $join = [];
//        $join = [
//            ['ceb_auth_group_access b','a.id=b.uid']
//        ];
        foreach ($conditions as $k => $obj) {
            if (preg_match('/^clientname$/i',$obj['name'])) {
                if ($obj['value'] == "") { continue; }
                $where['client_name'] = ['like','%'.$obj['value'].'%'];
            }elseif (preg_match('/^phone$/i',$obj['name'])) {
                if ($obj['value'] == "") { continue; }
                $where['client_telephone'] = ['like','%'.$obj['value'].'%'];
            }elseif (preg_match('/time_begin$/i',$obj['name'])) {
                if ($obj['value']==''&&$conditions[$k+1]['value']=='') {
                    continue;
                }elseif ($obj['value']==''&&$conditions[$k+1]['value']!='') {
                    $obj['value'] = $conditions[$k+1]['value'];
                }elseif($obj['value']!=''&&$conditions[$k+1]['value']=='') {
                    $conditions[$k+1]['value'] = $obj['value'];
                }
                $fieldname = substr($obj['name'],0,-6);
                if ($obj['value'] == $conditions[$k+1]['value']) {
                    $where['DATE_FORMAT('.$fieldname.',\'%Y-%m-%d\')'] = ['exp','=\''.$obj['value'].'\''];
                }else{
//                    $where[$this->type[$type_index]['whereKey'].$fieldname] = ['between time',[$obj['value'],$conditions[$k+1]['value']]];
                    $where['DATE_FORMAT('.$fieldname.',\'%Y-%m-%d\')'] = ['exp','between \''.$obj['value'].'\' and '.'\''.$conditions[$k+1]['value'].'\''];
                }
            }
        }
        $field = "";
        $client = new \app\admin\model\Client();
        $data = $client->alias('a')->join($join)->where($where)->page($page,$limit)->field($field)->select();
        $count = $client->alias('a')->join($join)->where($where)->count();
        if ($count>0) {
            createNums($data,$page,$limit);
            $data_format = [
                "code" => 0,
                "msg" => "success",
                "count" => $count,
                "data" => $data,
            ];
            return $data_format;
        }else{
            return [
                'code' => '0001',
                'msg' => '无数据！'
            ];
        }
    }

    public function delBatch() {
        $batchNo = $this->request->param('batch');
        $data = $this->request->post('data');
        $sql = 'DELETE a,b,c FROM `ceb_order_batch` `a` INNER JOIN `ceb_order_head` `b` ON `a`.`batch_time`=`b`.`batch_time` INNER JOIN `ceb_order_goods` `c` ON `b`.`order_no`=`c`.`order_no` WHERE `a`.`batch_time` = ';
        $orderBatch = new OrderBatch();
        $res = false;
        if ($data===null) {
            $res = $orderBatch->query($sql.$batchNo);
        }else if($data!==null) {
            foreach (explode('|',$data) as $val) {
                $res = $orderBatch->query($sql.$val);
            }
        }
        if (is_array($res)) {
            return feedback('0000','删除成功！');
        }else{
            return feedback('0001','删除失败！');
        }
    }
}