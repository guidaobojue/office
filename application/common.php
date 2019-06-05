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

function filename($filename){
	if(strpos($filename,".") !== false){
		$arr = explode(".",$filename);
		if(count($arr) < 2)
			return false;

		$exp = array_pop($arr);
		$filename = str_replace(PO.$exp,"",$filename);
		return ['name'=>$filename,"exp"=>$exp];
	}
	else
		return false;
}

function readExcel($file){
	require_once "../extend/PHPExcel/PHPExcel.php";
	require_once "../extend/PHPExcel/PHPExcel/IOFactory.php";
	require_once "../extend/PHPExcel/PHPExcel/Settings.php";

	$objPHPExcel = \PHPExcel_IOFactory::load($file);

	\PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
	$height = $rowCnt = $objPHPExcel->getSheet(0)->getHighestRow(); 


	if($height < 2){
		return false;
	}

	$width = $objPHPExcel->getSheet(0)->getHighestColumn();
	$width = \PHPExcel_Cell::columnIndexFromString($width);
	if($width > 26)
		$width = 26;
	for($i=0;$i<=$width-1;$i++){
		$key[] = chr(ord('A') + $i);
	}

	$data= [];

	foreach($key as $k => $v){
		$title[]=  $objPHPExcel->getSheet(0) ->getCell($v."1")->getFormattedValue();
	}

	for($i=2;$i<=$height;$i++){
		$temp = [];
		foreach($key as $k => $v){
			$temp[]=  $objPHPExcel->getSheet(0) ->getCell($v.$i)->getFormattedValue();
		}
		$data[] = $temp;
	}
	return ['title'=>$title,'data'=>$data];
}



function writeExcel($data,$title = null){
	require_once "../extend/PHPExcel/PHPExcel.php";
	require_once "../extend/PHPExcel/PHPExcel/IOFactory.php";
	require_once "../extend/PHPExcel/PHPExcel/Settings.php";

	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);

	/** Include PHPExcel */

	// Create new PHPExcel object
	$objPHPExcel = new \PHPExcel();

	$keys = [];
	$range = [];
	$width = count($data[0]);
	for($i=0;$i<=$width-1;$i++){
		$keys[] = chr(ord('A') + $i)."1";
		$range[] = chr(ord('A') + $i);
	}

	#var_dump($keys,$title);
	#exit;
	$list = array_combine($keys,$title);

	//卡号 	时间 	金额 	接待人员 	接待部门 	员工工号

	$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);

	foreach($range as $k => $v){
		$objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(20);
	}





	$i = 2;

	$tmp = [];
	foreach($data as $k => $v){
		$tmpKeys = [];
		foreach($range as $ik => $iv){
			$tmpKeys[]  = $iv.$i;
		}
		$tmp =  array_combine($tmpKeys,$v);
		$list = array_merge($tmp,$list);
		$i++;
	}

	foreach($list as $k => $v){
		$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($k, $v);
	}




	// Miscellaneous glyphs, UTF-8

			/*
	$objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
	$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
	$objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
			 */


	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('南院科技');


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	//$objPHPExcel->setActiveSheetIndex(0);


	// Save Excel 2007 file



	$file_name = "compare".time().rand(1,1000) .".xlsx";
	$file = WEB_DIR."/xls/" .  $file_name;
	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($file);

		/*
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		 */
	return $file_name;

}


function combine($data,$dbs,$columns){
	$width = count($data[0]);
	for($i=0;$i<count($data);$i++){
		$street = searchAddress($data[$i][$columns],$dbs);
		if(empty($street))
			$street = searchName($data[$i][$columns],$dbs);
		$data[$i][$width] = $street;
	}

	return $data;
}

function searchName($name,$list){
	$rs = '';
	foreach($list as $k => $v){
		if(trim($name) == trim($v["company_name"])){
			return $v['street'];
		}
	}
	return $rs;
}
function searchAddress($name,$list){
	$rs = '';
	foreach($list as $k => $v){
		if(trim($name) == trim($v["address"])){
			return $v['street'];
		}
	}
	return $rs;
}


function upload($name){
	if(!isset($_FILES[$name])){
		return false;
	}
	$file = $_FILES[$name];
	$tmp_name =  $file['tmp_name'];
	$names = filename($file['name']);
	$suf = $names['name'];
	$ex = $names['exp'];
	$suf = "up_".md5($suf.time()).".".$ex;
	$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);
	return $suf;
}

function sc($cid){
	if(!isset($_SESSION['user']))
		return false;

	return true;

}

function cache_set($name,$data){
	$rs = file_get_contents(CACHE_FILE);
	$rs = json_decode($rs,true);
	if(empty($rs))
		$rs = [];
	$rs[$name] = $data;
	return file_put_contents(CACHE_FILE,json_encode($rs));



}
function cache_get($name){
	$rs = file_get_contents(CACHE_FILE);
	$rs = json_decode($rs,true);
	if(isset($rs[$name]))
		return $rs[$name];
	else
		false;
}


function record($title,$data){
	$rs = ['a'=>123];
	$rs = var_export($rs,true);

	$date = date("Y-m-d H:i:s",time());
	$content = "=========" . $title . "=====".$date."=====\n";
	$content .= $rs ."\n";
	$content .= "============================================\n\n\n";
	file_put_contents("../record.log",$content,FILE_APPEND);
}

function span($stand,$val){
	$str = "";
	if($val == 4){
		$str = span_html(0);
	}
	else if($val > 4){
		if($stand > $val -3){
			$str = span_html(1);
		}
		else if($stand == $val -3){
			$str = span_html(2);
		}
		else{
			$str = span_html(0);
		}
	}
	else{
		if($stand <= $val){
			$str = span_html(0);

		}
		else{
			$str = span_html(3);
		}

	}

	return $str;

}

function span_html($num){
	$datas = ["<span style='color:green'>同意</span>","<span style='color:blue'>未审核</span>","<span style='color:red'>不同意</span>","<span style='color:blue'>审核中</span>"];
	return $datas[$num];
}

function operation($users,$user,$status){
	$str = "";
	$user_id = $user['user_id'];
	$user_id = 68;
	$users_id = [
		1 => $users['apply_user']['user_id'],
		2 => $users['apply_approval_user']['user_id'],
		3 => $users['use_approval_user']['user_id'],
		4 => $users['office_approval_user']['user_id'],
	];

	$score = 1;
	foreach($users_id as $k => $v){
		if($user_id == $v){
			$score = $k;
			if($k > $status){
				break;
			}
		}
	}


	if($status > 4){
		if($status -3 > $score){
			$str = operation_html(0);
		}
		else if($status -3 == $score)
			$str = operation_html(1);
		else{
			$str = operation_html(2);
		}

	}
	else if($status == 4){
		$str = operation_html(3);
	}
	else{
		if($status == $score-1){
			$str = operation_html(4);
		}
		else if($status > $score -1){
			$str = operation_html(5);
		}
		else{
		$str = operation_html(6);
		}
	}
	return $str;
}
function operation_html($num){
	$datas=[ 
		"<span style='color:green'>已同意</span>",
		"<span style='color:red'>未通过</span>", 
		"<span style='color:red'>中止</span>",
	       	"<span style='color:green'>完结</span>",
		'<span style="color:green;cursor:pointer" onclick="allow()">同意</span>  | <span style="color:red;cursor:pointer" onclick="deny()">驳回</span>',
		"<span style='color:green'>已同意</span>",
		"<span style='color:blue'>未到达</span>",
	];
	return $datas[$num];
}

function back_color($num){
	if($num >= 5){
		return "background:#f58080";
	}
	else if($num == 4){
		return "background:#80e480";
	}
	else{
		return "";
	}
}
