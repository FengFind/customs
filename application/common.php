<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//前端ajax请求的返回数据格式
function feedback($code,$msg,$data=null) {
    return [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
}

//创建数据集序号
function createNums($data,$page,$limit) {
    $i = 1;
    if ($page>1) {
        $i = ($page-1)*$limit+1;
    }
    foreach ($data as $k => $record) {
        $data[$k]['autonum'] = $i++;
    }
    return $data;
}

//导出excel文件时，对个别字段进行转义
function exportDecode($data) {
    foreach ($data as $key => $row) {
        $data[$key]['consignee_address'] = html_entity_decode($row['consignee_address']);
        $data[$key]['item_name'] = html_entity_decode($row['item_name']);
    }
    return $data;
}