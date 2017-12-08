<?php
namespace app\common\controller;
use think\Controller;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29
 * Time: 14:08
 */
class Excel extends Controller
{
    private $filePath;// 要读取的文件的路径
    public $filename_arr = ["就手订单","清单","入库单","运单"];   //导出时要用到的文件名数组

    /**
     *识别文件是否合法并返回合适的reader对象
     */
    public function index() {
        try{
            $ent_arr = explode(".",$this->filePath);
            $file_ext = end($ent_arr);  //获取文件扩展名
            if ($file_ext === 'xlsx') {
                $readerObj = \PHPExcel_IOFactory::createReader('Excel2007');
            }else{
                //识别旧版excel
                $readerObj = \PHPExcel_IOFactory::createReader('Excel5');
            }
            return $readerObj;
        }catch (\Exception $e) {
            return $this->error($e->getMessage(),"admin/Index/welcome",'',1000);
            //
        }
    }

    /**
     *读取excel表内容
     */
    public function getSheetsContent() {
        $titleCol = 1;
        $columnOrder = "A";
        $readerObj = $this->index();
        $excelObj = $readerObj->load($this->filePath);
        //获取工作表个数
        $sheetCount = $excelObj->getSheetCount();
        $datas = [];
        for ($i=0;$i<$sheetCount;$i++) {
            $data_arr = [];
            //当前sheet
            $sheet = $excelObj->getSheet($i);
//            $data_arr[] = $sheet->toArray();
            //获取行和列总数
//            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $maxKey = $highestColumn;
            foreach ($sheet->getRowIterator() as $key => $row) {
                $cellIterator = $row->getCellIterator();
//                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $k => $cell) {
                    $columnValue = (string)$cell->getValue();//获取数据
                    //当表头某列为空时，该列后边的单元格都不再遍历
                    if ($key == $titleCol && !isset($columnValue)) {
                        $maxKey = $k;
                    }
                    //当指定列的值为空时也不遍历
                    if ($k==$columnOrder && !isset($columnValue)) break;
                    $data_arr[$key-1][] = $columnValue;
                    if ($k == $maxKey) break;
                }
            }
            $datas[$i] = $data_arr;
        }
        //整合所有工作表数据
        $mergeDatas = $datas[0];
        foreach ($datas as $k => $data) {
            if ($k<count($datas)-1) {
                $mergeDatas = array_merge($mergeDatas,$datas[$k+1]);
            }
        }
        return $mergeDatas;   //目前只支持单表
    }

    /**
     *获取Excel表字段信息
     */
    public function getFields() {
        $datas = $this->getSheetsContent();
        $fields = null;
        foreach ($datas as $k => $row) {
            if ($k<count($datas)-1) {
                if (count($row)==count($datas[$k+1])) {
                    $fields = $row;
                    break;
                }
            }
        }
        return $fields;
    }

    /**
     * 整理excel记录订单数据，把相同订单中相同的信息和不同的信息区分开来
     * @param $data_arr 订单数据集
     * @param $order_index 订单编号或能标识订单编号的字段所在下标
     * @param $mode 为假时返回按订单相同来分组的数据集，为真则返回详细区分后的结果
     */
    public function getSameOrder($data_arr, $order_index,$mode=true) {
        foreach ($data_arr as $k => $row) {
            $orderID = $row[$order_index];
            //判断该行记录是否是订单,即不含有中文
            if (!preg_match("/[\x7f-\xff]/", $orderID)&&!empty($orderID)) {
                //获取相同订单的下标
                $data_arr_rev[$orderID][] = $k;
            }
        }
        isset($data_arr_rev) ? "" : die("数据非法");

        //分别组装相同订单记录到新的数组
        $i= 0;
        foreach ($data_arr_rev as $record) {
            foreach ($record as $index) {
                $data_arr_new[$i][] = $data_arr[$index];
            }
            $i++;
        }
        if (!$mode) {
            return $data_arr_new;
        }

        //提取相同订单中相同的信息
        $datas_new = [];
        foreach ($data_arr_new as $x => $records) {
            $arr_same = array();
            foreach ($records as $k => $row) {
                if ($k<count($records)-1){
                    for ($i=$k+1;$i<count($records);$i++) {
                        //获取相同订单的交集
                        $arr_same[] = array_intersect_assoc($records[$k],$records[$i]);
                        if (count($arr_same)>1) {
                            //只取交集最小的项，保证订单信息的准确性
                            count($arr_same[0])<count($arr_same[1]) ? array_pop($arr_same) : array_shift($arr_same);
                        }
                    }
                }else{
                    break;
                }
            }
            if (!empty($arr_same)) {
                $datas_new[$x]['same']  = $arr_same[0];
                //获取相同订单相同信息列项的坐标
                $arr_same_key = null;
                foreach ($arr_same[0] as $k => $val) {
                    $arr_same_key[] = $k;
                }
                //提取相同订单中不同的信息
                $arr_diff = null;
                foreach ($records as $row) {
                    $arr_diff[$order_index] = $arr_same[0][$order_index];
                    foreach ($row as $j => $value) {
                        if (!in_array($j,$arr_same_key)) {
                            $arr_diff[$j] = $value;
                        }
                    }
                    //组装新的订单信息
                    $datas_new[$x]['diff'] []= $arr_diff;
                }
            }else{
                //单条订单信息原封不动放入新数组
                $datas_new[$x] = $records[0];
            }
        }
        return $datas_new;
    }

    /**
     * 导出数据库内容到excel表
     * @param $datas 数据库内容，类型为数组
     */
    public function exportExcel($datas,$ex_type,$ex_batch,$excelWrite=false,$dir=null,$excelType=false) {
        $excelObj = new \PHPExcel();
        /*以下是一些设置，作者  标题之类*/
        $excelObj
            ->getProperties()->setCreator("Admin")
            ->setLastModifiedBy("Admin")
            ->setTitle("JiuShou")
            ->setSubject("JiuShou")
            ->setDescription("JiuShou")
            ->setKeywords("JiuShou")
            ->setCategory("JiuShou");

        //设置sheet标题
        $excelObj->getActiveSheet()->setTitle('JiuShou');
        //创建一个新的工作空间(sheet)
//        $excelObj->createSheet();
        //设置当前活动的sheet
        $excelObj->setActiveSheetIndex(0);
        $execelSheetObj = $excelObj->getActiveSheet(0);
        //单元格样式数组
        $all_style = array(
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, //横向居中
                'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_CENTER, //纵向居中
//                'wrap'       => true  //自动换行
            )
        );
        $title_style = [
            "font" => [
                "bold" => true
            ] ,
            "borders" => [
                "allborders" => [
                    "style" => \PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ];
        $rowNums = count($datas)+1;
        $colNums = count($datas[0]);
        //获取最大列的列坐标
        $maxCols = 65+$colNums;
        if ($maxCols>90) {
            $offset = intval($colNums/26)-1;
            $maxChar = chr(65+$offset).chr(65+$maxCols%90%26-2);
        }else{
            $maxChar = chr($maxCols);
        }
        $execelSheetObj->getStyle("A1:".$maxChar.$rowNums)->applyFromArray($all_style);
        $execelSheetObj->getStyle("A1:".$maxChar."1")->applyFromArray($title_style);
        $titles = [
            [
                '订单编号',
                '电商平台代码',
                '电商平台名称',
                '电商企业代码',
                '电商企业名称',
                '商品价格',
                '运杂费',
                '非现金抵扣金额',
                '代扣税款',
                '实际支付金额',
                '币制',
                '订购人注册号',
                '订购人姓名',
                '订购人电话',
                '订购人证件类型',
                '订购人证件号码',
                '支付企业代码',
                '支付企业名称',
                '支付交易编号',
                '收货人姓名',
                '收货人电话',
                '收货地址',
                '物流运单编号',
                '贸易方式',
                '账册编号',
                '许可证件号',
                '起运国/地区',
                '运费',
                '保费',
                '包装种类',
                '毛重(公斤)',
                '净重(公斤)',
                '提单号',
                '航班航次号',
                '件数',
                '商品序号',
                '企业商品名称',
                '数量',
                '计量单位',
                '法定数量',
                '法定计量单位',
                '原产国',
                '商品编码',
//                '国检商品编号',
                '商品规格型号',
                '单价',
                '总价',
                '币制'
            ],
            [
                '报送类型',
                '业务状态',
                '订单编号',
                '电商平台代码',
                '电商平台名称',
                '电商企业代码',
                '电商企业名称',
                '物流运单编号',
                '物流企业代码',
                '物流企业名称',
                '企业内部编号',
                '预录入编号',
                '担保企业编号',
                '账册编号',
                '清单编号',
                '进出口标记',
                '申报日期',
                '申报海关代码',
                '口岸海关代码',
                '进口日期',
                '订购人证件类型',
                '订购人证件号码',
                '订购人姓名',
                '订购人电话',
                '收件地址',
                '申报企业代码',
                '申报企业名称',
                '区内企业代码',
                '区内企业名称',
                '贸易方式',
                '运输方式',
                '运输工具编号',
                '航班航次号',
                '提运单号',
                '监管场所代码',
                '许可证件号',
                '起运国（地区）',
                '运费',
                '保费',
                '币制',
                '包装种类代码',
                '件数',
                '毛重（公斤）',
                '净重（公斤）',
                '备注',
                '序号',
                '账册备案料号',
                '企业商品货号',
                '企业商品品名',
                '商品编码',
                '商品名称',
                '商品规格型号',
                '条码',
                '原产国（地区）',
                '币制',
                '数量',
                '法定数量',
                '第二数量',
                '计量单位',
                '法定计量单位',
                '第二计量单位',
                '单价',
                '总价',
                '备注',
                '提单号'
            ],
            [
                '报送类型',
                '业务状态',
                '申报海关代码',
                '企业内部编号',
                '预录入编号',
                '入库单编号',
                '监管场所经营人代码',
                '监管场所经营人名称',
                '进出口标记',
                '运输方式',
                '运输工具编号',
                '航班航次号',
                '提运单号',
                '物流企业代码',
                '物流企业名称',
                '卸货库位',
                '入库明细备注',
                '序号',
                '物流运单编号',
                '入库明细单表体备注'
            ],
            [
                '报送类型',
                '业务状态',
                '物流企业代码',
                '物流企业名称',
                '物流运单编号',
                '提运单号',
                '运费',
                '保价费',
                '币制',
                '毛重',
                '件数',
                '主要货物信息',
                '收货人姓名',
                '收货地址',
                '收货人电话',
                '运单备注',
                '提单号'
            ]
        ];
        $execelSheetObj->fromArray($titles[$ex_type],null,"A1");
//      先在sheet生成要新增的数据行数
        $num_rows = count($datas);
        $last_row = $execelSheetObj->getHighestRow();
        $rows = $last_row + 1;
        $execelSheetObj->insertNewRowBefore($rows, $num_rows);

        //填入sheet单元格
        $execelSheetObj->fromArray($datas,null,"A2");

//      设置sheet的行高和列宽
        $spec_row_arr = ["1"=>"25",2=>"25"];
        $this->setRowsHeight($execelSheetObj,$rowNums,$spec_row_arr,"22.5");

        $spec_col_arr = [
            ["C"=>35,"E"=>35,"V"=>70,"Z"=>30,"AK"=>50,],
            ["C"=>35,"E"=>35,"G"=>35,"J"=>35,"V"=>35,"W"=>30,"Y"=>70,"AA"=>35,"AW"=>35,"AY"=>35,"AS"=>35,"BL"=>35,],
            ["G"=>35,"H"=>35,"O"=>35,"M"=>30,"Q"=>30,"T"=>30],
            ["D"=>35,"L"=>35,"N"=>70,"M"=>30,"P"=>35],
        ];
        $this->setColsWidth($execelSheetObj,$colNums,$spec_col_arr[$ex_type],20);

//        保存文件在本地
        // 生成2003excel格式的xls文件或2007的xlsx文件
        $excelType == true ? $type = "Excel2007" : $type = "Excel5";
        $excelType == true ? $type_exts = "xlsx" : $type_exts = "xls";

        //文件名处理，要在压缩到zip包后，才能正常更改为中文命名
        foreach (array_keys($this->filename_arr) as $key) {
            $filenames[] = $key." at ".$ex_batch;
        }
        $filename = preg_replace("/:/",".",$filenames[$ex_type]);

        if ($excelWrite) {  //在本地目录生成文件，以便压缩和导出
//            $dir!=null ? $dir = "downloads/".$dir : $dir = "downloads";
//            $dir = preg_replace("/:/",".",$dir);
            if (!is_dir($dir)) {
                mkdir("./".$dir,0777,true);
            }
            $filename = $this->filename_arr[0].'_'.$ex_batch;
            $outputFileName= "./".$dir."/".$filename.'.'.$type_exts."x";
            $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');
            $objWriter->save($outputFileName);
        }else{      //直接把excel输出到网页端下载
//            $filename = $this->filenameCh($filename,$this->filename_arr);
            if (!is_dir($dir)) {
                mkdir("./".$dir,0777,true);
            }
            $filename = $this->filename_arr[0].'_'.$ex_batch;
            $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, $type);
            $objWriter->save("php://output");
//            $objWriter->save($dir.DIRECTORY_SEPARATOR.iconv('utf-8','gb2312',$filename).'.xls');
            $this->download($filename,$type_exts);
        }
    }

    /**
     * 获取中文文件名
     * @param $filename
     * @param $filename_arr
     * @return mixed
     */
    public function filenameCh($filename, $filename_arr) {
        $name_key = substr($filename,0,1);
        $filename = substr_replace($filename,$filename_arr[$name_key],0,1);
        return $filename;
    }

    /**
     * 删除目录或文件
     * @param $dir
     * @param int $type
     */
    public function del_dir($dir, $type=0) {
        if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $str = "del /s/f/q " . $dir.'\\*.*';
            if ($type>0) {
                $str = "rd /s/q " . $dir;
            }
        } else {
            $str = "rm -Rf " . $dir;
        }
        system($str);
    }

    /**
     * 文件下载
     * @param $filename
     * @param $type_exts
     */
    public function download($filename, $type_exts) {
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment;filename="'.$filename.'.'.$type_exts.'"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
    }

    /**
     * 压缩文件夹
     * @param $path
     * @param $zip
     */
    public function addFileToZip($path, $zip,$dir=""){
    $handler=opendir($path); //打开当前文件夹由$path指定。
    while(($filename=readdir($handler))!==false){
        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if (mb_detect_encoding($filename) == "UTF-8") {
                $filename = iconv("utf-8","GB2312//IGNORE",$filename);
            }
            if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                $dir = $filename."/";
                $this->addFileToZip($path."/".$filename, $zip,$dir);
            }else{ //将文件加入zip对象
                $zip->addFile($path.'/'.$filename,$dir.$filename);
                //修改文件名为中文名
                $zip->renameName($dir.$filename,$dir.$this->filenameCh($filename,$this->filename_arr));
            }
        }
    }
    @closedir($handler);
}


    /**
     * 设置sheet的各列宽度
     * @param $execelSheetObj           当前sheet对象
     * @param $colNums                  总列数(目前只支持到A-ZZ列)
     * @param $spec_arr                 个别列宽设置，格式为["A"=>30,"B"=>30,...]
     * @param string $defaultWidth      设置默认宽度，默认为"true"，否则按传入的参数来设置宽度
     */
    public function setColsWidth($execelSheetObj, $colNums, $spec_arr, $defaultWidth="true") {
        //按十进制对应的ascii值进行列值循环
        $defaultWidth === "true" ? $set_way = "setAutoSize" : $set_way = "setWidth" ;
        $spec_keys_arr = array_keys($spec_arr);
        $k = 64;
        for ($i=65;$i<65+$colNums;$i++) {
            if ($i>90) {
                $i = 65;
                $colNums-=26;
                $k++;
                if ($k>90) {
                    return false;
                }
                $colKey = chr($k).chr($i);
            }elseif ($k>64) {
                $colKey = chr($k).chr($i);
            }else{
                $colKey = chr($i);
            }
            if(!in_array($colKey,$spec_keys_arr)){
                $execelSheetObj->getColumnDimension($colKey)->$set_way($defaultWidth);
            }
        }
        //重新定义个别列的列宽
        if (count($spec_arr)>1) {
            foreach ($spec_arr as $k => $val) {
                $execelSheetObj->getColumnDimension($k)->setWidth($val);
            }
        }
    }

    /**
     * 设置sheet的各行高度
     * @param $execelSheetObj           当前sheet对象
     * @param $rowNums                  总行数
     * @param $spec_arr                 个别行高设置，格式为["1"=>30,"5"=>30,...]
     * @param string $defaultHeight      设置默认高度，默认为"-1"，否则按传入的参数来设置高度
     **/
    public function setRowsHeight($execelSheetObj, $rowNums, $spec_arr, $defaultHeight="-1") {
        $spec_keys_arr = array_keys($spec_arr);
        for ($i=1;$i<=$rowNums;$i++) {
            if(!in_array($i,$spec_keys_arr)){
                $execelSheetObj->getRowDimension($i)->setRowHeight($defaultHeight);
            }
        }
        //重新定义个别行高
       if (count($spec_arr)>1) {
           foreach ($spec_arr as $k => $val) {
               $execelSheetObj->getRowDimension($k)->setRowHeight($val);
           }
       }
    }

    /**
     * 获取文件路径
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * 设置文件路径
     * @param mixed $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }
}