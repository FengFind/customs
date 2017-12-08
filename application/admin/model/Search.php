<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/14
 * Time: 11:10
 */

namespace app\admin\model;


use think\Db;

class Search {
    public $where = [];
    public $alias = null;
    public $field = null;
    public $group = null;
    public $join = [];
    public $name = '';
    public $page= '';
    public $limit = '';

    /**
     * 按条件查询
     * @return array
     */
    public function search() {
        $i = 1;
        if ($this->page>1) {
            $i = ($this->page-1)*$this->limit+1;
        }
        $searchObj = Db::name($this->name);
        $result = $searchObj
            ->alias($this->alias)
            ->where($this->where)
            ->field($this->field)
            ->join($this->join)
            ->group($this->group)
            ->page($this->page,$this->limit)
//            ->fetchSql()
            ->select();
//        dump($this->group);exit;
        foreach ($result as $k => $record) {
            $result[$k]['autonum'] = $i++;
        }
        $count = $searchObj
            ->alias($this->alias)
            ->where($this->where)
            ->join($this->join)
            ->group($this->group)
            ->count();
        return $data_format = [
            "code" => 0,
            "msg" => "success",
            "count" => $count,
            "data" => $result,
        ];
    }

}