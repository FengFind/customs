<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/14
 * Time: 11:00
 */

namespace app\admin\controller;


use think\Loader;

class Search extends Common {
    protected $type = [
        'olist_single' => ['name'=>'order_head','alias'=>'a','join'=>[['order_batch b','a.batch_time=b.batch_time']],'whereKey'=>'a.order_no'],
        'oexport_single' => ['name'=>'order_head','alias'=>'a','join'=>[['order_batch b','a.batch_time=b.batch_time']],'whereKey'=>'b.batch_note','field' => "b.batch_time,b.batch_note,a.create_time",'group' => 'b.batch_time,b.batch_note,a.create_time'],
        'olist_multiple' => ['name'=>'order_head','alias'=>'a','join'=>[['order_batch b','a.batch_time=b.batch_time']],'whereKey'=>'a.','whereKey2arr'=>['batch_note'],'whereKey2'=>'b.'],
        'oexport_multiple' => ['name'=>'order_head','alias'=>'a','join'=>[['order_batch b','a.batch_time=b.batch_time']],'whereKey'=>'a.','whereKey2arr'=>['batch_note','batch_time'],'whereKey2'=>'b.','field' => "b.batch_time,b.batch_note,a.create_time,any_value(a.declare_status) as declare_status",'group' => 'b.batch_time,b.batch_note,a.create_time']
    ];

    /**
     * 单个条件查询
     * @return mixed
     */
    public function searchBySingle() {
        $request = $this->request->get();
        $search = Loader::model('Search');
        $type_index = $request['type'];
        $search->name = $this->type[$type_index]['name'];
        $search->join = $this->type[$type_index]['join'];
        $search->alias = $this->type[$type_index]['alias'];
        if (isset($this->type[$type_index]['field'])) {
            $search->field = $this->type[$type_index]['field'];
        }
        if (isset($this->type[$type_index]['group'])) {
            $search->group = $this->type[$type_index]['group'];
        }
        $search->page = $request['page'];
        $search->limit = $request['limit'];
        $search->where = [ $this->type[$type_index]['whereKey'] => ['like','%'.$request['condition'].'%']];
        $res = $search->search();
        return $res;
    }

    /**
     * 多条件查询
     * @return mixed
     */
    public function searchByMultiple() {
        $conditions = $this->request->post('data/a');
        $search = Loader::model('Search');
        $type_index = $this->request->post('type');
        $search->name = $this->type[$type_index]['name'];
        $search->join = $this->type[$type_index]['join'];
        $search->alias = $this->type[$type_index]['alias'];
        $search->page = $this->request->post('page');
        $search->limit = $this->request->post('limit');
        if (isset($this->type[$type_index]['field'])) {
            $search->field = $this->type[$type_index]['field'];
        }
        if (isset($this->type[$type_index]['group'])) {
            $search->group = $this->type[$type_index]['group'];
        }
        $where = [];
        foreach ($conditions as $k => $obj) {
            if (preg_match('/_eq$/i',$obj['name'])) {
                if ($conditions[$k+1]['value'] == "") { continue; }
                $eqType = $this->eqType($obj['value']);
                if (in_array($conditions[$k+1]['name'],$this->type[$type_index]['whereKey2arr'])) {
                    $eqType != 'like' ? $where[$this->type[$type_index]['whereKey2'].$conditions[$k+1]['name']] = [$eqType,$conditions[$k+1]['value']] : $where[$this->type[$type_index]['whereKey2'].$conditions[$k+1]['name']] = [$eqType,'%'.$conditions[$k+1]['value'].'%'];
                }else{
                    $eqType == 'like' ? $where[$this->type[$type_index]['whereKey'].$conditions[$k+1]['name']] = [$eqType,'%'.$conditions[$k+1]['value'].'%'] : $where[$this->type[$type_index]['whereKey'].$conditions[$k+1]['name']] = [$eqType,$conditions[$k+1]['value']] ;
                }
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
                    $where['DATE_FORMAT('.$this->type[$type_index]['whereKey'].$fieldname.',\'%Y-%m-%d\')'] = ['exp','=\''.$obj['value'].'\''];
                }else{
//                    $where[$this->type[$type_index]['whereKey'].$fieldname] = ['between time',[$obj['value'],$conditions[$k+1]['value']]];
                    $where['DATE_FORMAT('.$this->type[$type_index]['whereKey'].$fieldname.',\'%Y-%m-%d\')'] = ['exp','between \''.$obj['value'].'\' and '.'\''.$conditions[$k+1]['value'].'\''];
                }
            }
        }
        $extra = $this->request->param('extra/a');
        isset($extra) ? $where = array_merge($where,$extra) : '';
        $search->where = $where;
        $res = $search->search();
        return $res;

    }

    public function eqType($num) {
        $eqType = "";
        switch ($num) {
            case '0':
               $eqType = "=";
               break;
            case '1':
                $eqType = ">";
                break;
            case '2':
                $eqType = "<";
                break;
            case '3':
                $eqType = "<>";
                break;
            case '4':
                $eqType = "like";
                break;
            default :
                $eqType = false;
                break;
        }
        return $eqType;
    }
}