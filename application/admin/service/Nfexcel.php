<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/26
 * Time: 11:56
 */

namespace app\admin\service;

use think\Db;
use app\common\controller\Excel;

class Nfexcel {
	private $val = [
		'logistics_code'=>'4407986010',
		'logistics_name'=>'鹤山市南方国际速递有限公司',
		'assure_code'=>'4407986010',
		'customs_code'=>'6861',
		'port_code'=>'6861',
	];

	public function exportExcel($name,$batch,$data,$path = null) {
	    $this->$name($batch,$data,$path);
    }

	private function billExcel($batch, $data,$path = null){

		$col_name = ['报送类型','业务状态','订单编号','电商平台代码','电商平台名称','电商企业代码','电商企业名称','物流运单编号','物流企业代码','物流企业名称','企业内部编号',
		'预录入编号','担保企业编号','账册编号','清单编号','进出口标记','申报日期','申报海关代码','口岸海关代码','进口日期','订购人证件类型','订购人证件号码','订购人姓名',
		'订购人电话','收件地址','申报企业代码','申报企业名称','区内企业代码','区内企业名称','贸易方式','运输方式','运输工具编号','航班航次号','提运单号','监管场所代码',
		'许可证件号','起运国（地区）','运费','保费','币制','包装种类代码','件数','毛重（公斤）','净重（公斤）','备注','序号','账册备案料号','企业商品货号','企业商品品名',
		'商品编码','商品名称','商品规格型号','条码','原产国（地区）','币制','数量','法定数量','第二数量','计量单位','法定计量单位','第二计量单位','单价','总价','备注',
		'提单号'];

		$objPHPExcel = $this->CreatePHPExcel($col_name);
		//行计数器
		$j = 3;
		foreach ($data as $item){
			$objPHPExcel->getActiveSheet()
				->setCellValue("A{$j}", '1')    //报送类型
				->setCellValue("B{$j}", '2')    //业务状态
				->setCellValueExplicit("C{$j}", $item['order_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //订单编号

				->setCellValueExplicit("D{$j}", $item['ebp_code'], \PHPExcel_Cell_DataType::TYPE_STRING)    //电商平台代码
				->setCellValue("E{$j}", $item['ebp_name'])    //电商平台名称
				->setCellValueExplicit("F{$j}", $item['ebc_code'], \PHPExcel_Cell_DataType::TYPE_STRING)    //电商企业代码
				->setCellValue("G{$j}", $item['ebc_name'])    //电商企业名称

				->setCellValueExplicit("H{$j}", $item['logistics_no'],\PHPExcel_Cell_DataType::TYPE_STRING) //物流运单编号
				->setCellValueExplicit("I{$j}", $this->val['logistics_code'], \PHPExcel_Cell_DataType::TYPE_STRING)    //物流企业代码
				->setCellValue("J{$j}", $this->val['logistics_name']) //物流企业名称
				->setCellValueExplicit("K{$j}", $item['logistics_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //企业内部编号
				->setCellValue("L{$j}", '') //预录入编号
				->setCellValueExplicit("M{$j}", $this->val['assure_code'], \PHPExcel_Cell_DataType::TYPE_STRING)    //担保企业编号
				->setCellValue("N{$j}", '') //账册编号
				->setCellValue("O{$j}", '') //清单编号
				->setCellValue("P{$j}", 'I') //进出口标记
				->setCellValue("Q{$j}", date("Ymd")) //申报日期
				->setCellValueExplicit("R{$j}", $this->val['customs_code'], \PHPExcel_Cell_DataType::TYPE_STRING)//申报海关代码
				->setCellValueExplicit("S{$j}", $this->val['port_code'], \PHPExcel_Cell_DataType::TYPE_STRING)//口岸海关代码
				->setCellValue("T{$j}", date("Ymd",strtotime('-3 day'))) //进口日期

				->setCellValue("U{$j}", $item['buyer_id_type']) //订购人证件类型
				->setCellValueExplicit("V{$j}", $item['buyer_id_number'], \PHPExcel_Cell_DataType::TYPE_STRING)//订购人证件号码
				->setCellValue("W{$j}", $item['buyer_name']) //订购人姓名
				->setCellValueExplicit("X{$j}", $item['buyer_telephone'], \PHPExcel_Cell_DataType::TYPE_STRING)//订购人电话

				->setCellValue("Y{$j}", $item['consignee_address']) //收件地址

				->setCellValueExplicit("Z{$j}", $this->val['logistics_code'], \PHPExcel_Cell_DataType::TYPE_STRING)    //申报企业代码
				->setCellValue("AA{$j}", $this->val['logistics_name']) //申报企业名称

				->setCellValue("AB{$j}", '') //区内企业代码
				->setCellValue("AC{$j}", '') //区内企业名称

				->setCellValueExplicit("AD{$j}", $item['trade_mode'], \PHPExcel_Cell_DataType::TYPE_STRING)    //贸易方式
				->setCellValueExplicit("AE{$j}", '4', \PHPExcel_Cell_DataType::TYPE_STRING)    //运输方式
				->setCellValueExplicit("AF{$j}", '01', \PHPExcel_Cell_DataType::TYPE_STRING)    //运输工具编号

				->setCellValue("AG{$j}", $item['voyage_no']) //航班航次号
				->setCellValueExplicit("AH{$j}", $item['bill_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //提运单号
				->setCellValueExplicit("AI{$j}", $this->val['assure_code'],\PHPExcel_Cell_DataType::TYPE_STRING) //监管场所代码
				->setCellValue("AJ{$j}", '') //许可证件号
				->setCellValueExplicit("AK{$j}", $item['a_country'], \PHPExcel_Cell_DataType::TYPE_STRING)    //起运国（地区）

				->setCellValue("AL{$j}", $item['freight2']) //运费
				->setCellValue("AM{$j}", $item['insured_fee']) //保费
				->setCellValue("AN{$j}", $item['currency']) //币制

				->setCellValue("AO{$j}", $item['wrap_type']) //包装种类代码
				->setCellValue("AP{$j}", $item['pack_no']) //件数
				->setCellValue("AQ{$j}", $item['gross_weight']) //毛重（公斤）
				->setCellValue("AR{$j}", $item['net_weight']) //净重（公斤）
				->setCellValue("AS{$j}", '') //备注
				->setCellValue("AT{$j}", $item['gnum']) //序号
				->setCellValue("AU{$j}", '') //账册备案料号

				->setCellValue("AV{$j}", rand(1000,9999)) //企业商品货号
				->setCellValue("AW{$j}", $item['item_name']) //企业商品品名
				->setCellValueExplicit("AX{$j}", $item['hscode'],\PHPExcel_Cell_DataType::TYPE_STRING) //商品编码
				->setCellValue("AY{$j}", $item['item_name']) //商品名称
				->setCellValue("AZ{$j}",  $item['gtype']) //商品规格型号

				->setCellValue("BA{$j}",  '') //条码
				->setCellValue("BB{$j}",  $item['country']) //原产国（地区）
				->setCellValue("BC{$j}",  $item['item_currency']) //币制
				->setCellValue("BD{$j}",  $item['qty']) //数量
				->setCellValue("BE{$j}",  $item['qty1']) //法定数量
				->setCellValue("BF{$j}",  '') //第二数量
				->setCellValue("BG{$j}",  $item['unit']) //计量单位
				->setCellValue("BH{$j}",  $item['unit1']) //法定计量单位
				->setCellValue("BI{$j}",  '') //第二计量单位
				->setCellValue("BJ{$j}",  $item['price']) //单价
				->setCellValue("BK{$j}",  $item['total_price']) //总价
				->setCellValue("BL{$j}",  '') //备注
				->setCellValueExplicit("BM{$j}", $item['bill_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //提运单号
			;
			$j++;
		}
        $rows = count($data)+2;
        $cols = count($col_name);
        $this->setSheetStyles($objPHPExcel,"A2:BM2","A2:BM".$rows);
        $excel = new Excel();
        $spec_row_arr = [1=>"25"];
        $spec_col_arr = ["A"=>10,"B"=>10,"C"=>25,"E"=>35,"G"=>35,"J"=>35,"P"=>15,"V"=>25,"Y"=>70,"AA"=>35,"AW"=>50,"AY"=>50];
        $excel->setRowsHeight($objPHPExcel->getActiveSheet(),$rows,$spec_row_arr,"22.5");
        $excel->setColsWidth($objPHPExcel->getActiveSheet(),$cols,$spec_col_arr,20);
		$this->saveExcel($objPHPExcel, $batch, 'QingDan', '清单',$path);
	}

    private function storageExcel($batch, $data,$path = null){

		$col_name = ['报送类型','业务状态','申报海关代码','企业内部编号','预录入编号','入库单编号','监管场所经营人代码','监管场所经营人名称','进出口标记','运输方式',
		'运输工具编号','航班航次号','提运单号','物流企业代码','物流企业名称','卸货库位','入库明细备注','序号','物流运单编号','入库明细单表体备注'];

		$objPHPExcel = $this->CreatePHPExcel($col_name);
		//行计数器
		$j = 3;
		foreach ($data as $item){
			$objPHPExcel->getActiveSheet()
				->setCellValue("A{$j}", '1')    //报送类型
				->setCellValue("B{$j}", '2')    //业务状态
				->setCellValue("C{$j}",  $this->val['customs_code']) //申报海关代码
				->setCellValueExplicit("D{$j}", $item['logistics_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //企业内部编号
				->setCellValue("E{$j}",  '') //预录入编号
				->setCellValue("F{$j}",  '') //入库单编号
				->setCellValueExplicit("G{$j}", $this->val['logistics_code'], \PHPExcel_Cell_DataType::TYPE_STRING)    //物流企业代码
				->setCellValue("H{$j}", $this->val['logistics_name']) //物流企业名称
				->setCellValue("I{$j}",  'I') //进出口标记
				->setCellValueExplicit("J{$j}", '4', \PHPExcel_Cell_DataType::TYPE_STRING)    //运输方式
				->setCellValueExplicit("K{$j}", '01', \PHPExcel_Cell_DataType::TYPE_STRING)    //运输工具编号
				->setCellValue("L{$j}", $item['voyage_no']) //航班航次号
				->setCellValueExplicit("M{$j}", $item['bill_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //提运单号
				->setCellValueExplicit("N{$j}", $this->val['logistics_code'], \PHPExcel_Cell_DataType::TYPE_STRING)    //物流企业代码
				->setCellValue("O{$j}", $this->val['logistics_name']) //物流企业名称
				->setCellValue("P{$j}",  '') //卸货库位
				->setCellValue("Q{$j}",  '') //入库明细备注
				->setCellValue("R{$j}",  $item['gnum']) //序号
				->setCellValueExplicit("S{$j}",  $item['logistics_no'], \PHPExcel_Cell_DataType::TYPE_STRING) //物流运单编号
				->setCellValue("T{$j}",  '') //入库明细单表体备注
			;
			$j++;
		}
        $rows = count($data)+2;
        $cols = count($col_name);
        $this->setSheetStyles($objPHPExcel,"A2:T2","A2:T".(count($data)+2));
        $excel = new Excel();
        $spec_row_arr = [1=>"25"];
        $spec_col_arr = ["A"=>10,"B"=>10,"G"=>25,"H"=>35,"O"=>35,"T"=>50];
        $excel->setRowsHeight($objPHPExcel->getActiveSheet(),$rows,$spec_row_arr,"22.5");
        $excel->setColsWidth($objPHPExcel->getActiveSheet(),$cols,$spec_col_arr,20);
		$this->saveExcel($objPHPExcel, $batch, 'RuKuDan', '入库单',$path);
	}

    private function waybillExcel($batch, $data,$path = null){

		$col_name = ['报送类型','业务状态','物流企业代码','物流企业名称','物流运单编号','提运单号','运费','保价费','币制','毛重','件数','主要货物信息','收货人姓名',
		'收货地址','收货人电话','运单备注','提单号'];

		$objPHPExcel = $this->CreatePHPExcel($col_name);
		//行计数器
		$j = 3;
		foreach ($data as $item){
			$objPHPExcel->getActiveSheet()
				->setCellValue("A{$j}", '1')    //报送类型
				->setCellValue("B{$j}", '2')    //业务状态
				->setCellValueExplicit("C{$j}", $this->val['logistics_code'], \PHPExcel_Cell_DataType::TYPE_STRING)    //物流企业代码
				->setCellValue("D{$j}", $this->val['logistics_name']) //物流企业名称
				->setCellValueExplicit("E{$j}", $item['logistics_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //物流运单编号
				->setCellValueExplicit("F{$j}", $item['bill_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //提运单号
				->setCellValue("G{$j}", $item['freight2']) //运费
				->setCellValue("H{$j}", $item['insured_fee']) //保费
				->setCellValue("I{$j}", $item['currency']) //币制
				->setCellValue("J{$j}", $item['gross_weight']) //毛重（公斤）
				->setCellValue("K{$j}", $item['pack_no']) //件数
				->setCellValue("L{$j}", $item['item_name']) //商品名称
				->setCellValue("M{$j}", $item['consignee']) //收货人姓名
				->setCellValue("N{$j}", $item['consignee_address']) //收货地址
				->setCellValueExplicit("O{$j}", $item['consignee_telephone'],\PHPExcel_Cell_DataType::TYPE_STRING) //收货人电话
				->setCellValue("P{$j}",  '') //运单备注
				->setCellValueExplicit("Q{$j}", $item['bill_no'], \PHPExcel_Cell_DataType::TYPE_STRING)    //提运单号
			;
			$j++;
		}
        $rows = count($data)+2;
        $cols = count($col_name);
        $this->setSheetStyles($objPHPExcel,"A2:Q2","A2:Q".(count($data)+2));
        $excel = new Excel();
        $spec_row_arr = [1=>"25"];
        $spec_col_arr = ["A"=>10,"B"=>10,"D"=>35,"L"=>50,"N"=>70];
        $excel->setRowsHeight($objPHPExcel->getActiveSheet(),$rows,$spec_row_arr,"22.5");
        $excel->setColsWidth($objPHPExcel->getActiveSheet(),$cols,$spec_col_arr,20);
		$this->saveExcel($objPHPExcel, $batch, 'YunDan', '运单',$path);
	}

	private function setSheetStyles($objPHPExcel,$TitleRange,$CellRange) {
        $objStyle = $objPHPExcel->getActiveSheet();
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
        $objStyle->getStyle($TitleRange)->applyFromArray($title_style);
        $objStyle->getStyle($CellRange)->applyFromArray($all_style);
    }

	public function dowloadZip($batch=null,$both=false){
		if ($batch!==null) {
            $data = Db::name('order_head')
                ->alias('a')
                ->join([["order_goods b","a.order_no = b.order_no"]])
                ->field(['a.order_no order_no','a.currency currency', 'ebp_code', 'ebp_name', 'ebc_code', 'ebc_name', 'logistics_no', 'trade_mode', 'buyer_id_type', 'buyer_id_number',
                    'buyer_name', 'buyer_telephone', 'consignee', 'consignee_telephone', 'consignee_address', 'trade_mode', 'bill_no', 'voyage_no', 'license_no',
                    'a_country', 'freight2', 'insured_fee', 'b.currency item_currency','wrap_type', 'gross_weight', 'net_weight', 'gnum', 'hscode', 'item_name', 'gtype',
                    'country', 'qty', 'qty1', 'unit', 'unit1', 'price', 'total_price','pack_no'])
                ->where(['a.batch_time' => $batch])
                ->select();
            if(!$data) return;
            if ($both === true) {
                $b_path = Excel.'both'.DIRECTORY_SEPARATOR.$batch;
                $this->billExcel($batch, $data,$b_path);
                $this->storageExcel($batch, $data,$b_path);
                $this->waybillExcel($batch, $data,$b_path);
                return;
            }else{
                $this->billExcel($batch, $data);
                $this->storageExcel($batch, $data);
                $this->waybillExcel($batch, $data);
            }
            $path = $both ? Excel.'both' : Excel.$batch;
        }else if ($batch === null && $both === false){
            $path = Excel.'batch';
        }else if ($batch === null && $both === true) {
            $path = Excel.'both';
        }

        $zip=new \ZipArchive();
        if($zip->open($path.'.zip', \ZipArchive::OVERWRITE)=== TRUE){
			$this->addFileToZip($path, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
			$zip->close(); //关闭处理的zip文件
		}else if ($zip->open($path.'.zip', \ZipArchive::CREATE)=== TRUE) {
            $this->addFileToZip($path, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
            $zip->close(); //关闭处理的zip文件
        }
	}

	/**
	 * @param $col_name
	 *
	 * @return \PHPExcel
	 */
	private function CreatePHPExcel($col_name) {
//		import('Vendor.PHPExcel.Classes.PHPExcel', '', '.php');
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getProperties()->setCreator("JiuShou")
			->setLastModifiedBy("JiuShou")
			->setTitle("JiuShou")
			->setSubject("JiuShou")
			->setDescription("JiuShou")
			->setKeywords("JiuShou")
			->setCategory("JiuShou");
		$objPHPExcel->setActiveSheetIndex(0)
			->fromArray($col_name, null, 'A2');
		return $objPHPExcel;
}

	/**
	 * @param $objPHPExcel
	 * @param $batch
	 * @param $title
	 * @param $filename
	 */
	private function saveExcel($objPHPExcel, $batch, $title, $filename,$path = null) {
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle($title);
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$path = $path ? $path : Excel.$batch;
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
        if ($path == Excel.$batch) {
            $filename  = $path .DIRECTORY_SEPARATOR. $filename.'.xls';
        }else{
            $filename = $path .DIRECTORY_SEPARATOR. $filename.'_'.$batch.'.xls';
        }
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $filename = iconv("utf-8", "gb2312", $filename);
		$objWriter->save($filename);
		unset($objPHPExcel);
	}


	private function addFileToZip($path,$zip){
		$handler=opendir($path); //打开当前文件夹由$path指定。
        $filename_arr = ["清单","入库单","运单","就手订单"];
		while(($filename=readdir($handler))!==false){
			if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
				if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
					$this->addFileToZip($path."/".$filename, $zip);
				}else{ //将文件加入zip对象
                    $filename = iconv('gb2312','utf-8',$filename);
                    if (preg_match("/[\x7f-\xff]/", $filename)) {
                        if (strstr($filename,'_',true) != false) {
                            $nfilename = array_search(strstr($filename,'_',true),$filename_arr).strstr($filename,'_');
                            rename(iconv('utf-8','gb2312',$path.DIRECTORY_SEPARATOR.$filename),$path.DIRECTORY_SEPARATOR.array_search(strstr($filename,'_',true),$filename_arr).strstr($filename,'_'));
                            $subdir = strstr($path,DIRECTORY_SEPARATOR.'both') != false ? preg_replace("/_|(\.xls|\.xlsx)$/",'',strstr($filename,'_')).DIRECTORY_SEPARATOR : '';
                            $zip->addFile($path.DIRECTORY_SEPARATOR.$nfilename,$subdir.$nfilename);
                            $zip->renameName($subdir.$nfilename,$subdir.$filename_arr[strstr($nfilename,'_',true)].strstr($nfilename,'_'));
                        }else{
                            $nfilename = array_search(str_replace('.xls',"",$filename),$filename_arr).'.xls';
                            rename(iconv('utf-8','gb2312',$path.DIRECTORY_SEPARATOR.$filename),$path.DIRECTORY_SEPARATOR.array_search(str_replace('.xls',"",$filename),$filename_arr).'.xls');
                            $zip->addFile($path.DIRECTORY_SEPARATOR.$nfilename,$nfilename);
                            $zip->renameName($nfilename,$filename_arr[str_replace('.xls','',$nfilename)].'.xls');
                        }
                    }
				}
			}
		}
        @closedir($path);
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
}