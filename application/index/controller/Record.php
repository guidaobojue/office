<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Record extends \think\Controller
{

	public function __construct(){
		parent::__construct();
	}

	/*
	 * supplement 文件匹配
	 * status 文档状态 
	 * street 街道分配
	 */

	public function search(){
	}

	public function street(){
		$uploads_dir = "../upload/";
		$this->assign("list_num",2);
		if(!empty($_FILES['file']['name'])){
			$v = $_FILES['file'];
			$tmp_name =  $v['tmp_name'];
			$names = filename($v['name']);
			$suf = $names['name'];
			$ex = $names['exp'];
			$suf = "up_".md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);

			$recordModel = model("Record");
			$excel = readExcel($uploads_dir.$suf);


			$data = $excel['data'];
			$title = $excel['title'];

			$this->assign("filename",$suf);
			$this->assign("title",$title);

			return $this->fetch("street");
		}
		else{
			return $this->fetch("street");
		}
	}

	public function supplement(){
		$uploads_dir = "../upload/";
		$this->assign("list_num",2);
		if(!empty($_FILES['file']['name'])){
			$v = $_FILES['file'];
			$tmp_name =  $v['tmp_name'];
			$names = filename($v['name']);
			$suf = $names['name'];
			$ex = $names['exp'];
			$suf = "up_".md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);

			$recordModel = model("Record");
			$excel = readExcel($uploads_dir.$suf);


			$data = $excel['data'];
			$title = $excel['title'];

			$this->assign("filename",$suf);
			$this->assign("title",$title);

			return $this->fetch("supplement");
		}
		else{
			return $this->fetch("supplement");
		}
	}



	public function status(){
		$this->assign("list_num",2);
		$uploads_dir = "../upload/";

		$file1 = request()->file('file1');
		$file2 = request()->file('file2');
		if(!$file1 || !$file2){
			return $this->fetch("status");
		}
		else{
			if($file1){
				$info = $file1->move(ROOT_PATH . 'public' . DS . 'upload');
				if($info){
					$excel = readExcel(ROOT_PATH . 'public' . DS . 'upload' .DS . $info->getSaveName());
					$this->assign("filename1",$info->getSaveName());
					$data1 = $excel['data'];
					$title1= $excel['title'];

				}
			}


			if($file2){
				$info = $file2->move(ROOT_PATH . 'public' . DS . 'upload');
				if($info){
					$excel = readExcel(ROOT_PATH . 'public' . DS . 'upload' .DS . $info->getSaveName());
					$this->assign("filename2",$info->getSaveName());
					$data2 = $excel['data'];
					$title2= $excel['title'];

				}
			}

			/* 临时用 */
			/*
			$tmp = [];
			$error = [];
			foreach($data1 as $k => $v){
				if(in_array(trim($v[21]),["审核通过","审核中"])){
					if(isset($tmp[$v[0]])){
						$error[] = $v;
					}
					$tmp[$v[0]] = $v;
				}
				else{
				}
			}
			$data1 = $tmp;


			$tmp = [];
			foreach($data2 as $k => $v){
				if(in_array(trim($v[21]),["5.电子版审核通过","3.电子版报市局"]))
					$tmp[$v[1]] = $v;
			}


			$data2 = $tmp;

			$tmp = [];
			$i = 0;
			foreach($data1 as $k => $v){
				if(!isset($data2[$k])){
					$tmp[] = $v;
				}
				else{
					$i++;

				}
			}


			$url= writeExcel($tmp,$title2);
			echo  WEB_DIR."/xls/".$url;
			exit;
			 */
			/* 临时用 */



			$this->assign('title1',$title1);
			$this->assign('title2',$title2);
			return $this->fetch("statusSub");
		}
	}

	public function statusCheck(){
		$akey = "5.电子版审核通过";
		$allowCell = 17; //通过状态所在位置
		$allowKey = '通过';

		$filename1 = input("post.filename1");
		$filename2 = input("post.filename2");

		$col1 = input("post.columns1");
		$col2 = input("post.columns2");

		$excel1 = readExcel(ROOT_PATH . 'public' . DS . 'upload' .DS . $filename1);
		$data1 = $excel1['data'];
		$title1 = $excel1['title'];

		$excel2 = readExcel(ROOT_PATH . 'public' . DS . 'upload' .DS . $filename2);
		$data2 = $excel2['data'];

		$references = [];

		foreach($data2  as $k => $v){
			if(trim($v[$allowCell]) == $allowKey){
				$references[] = $v[0];
			}
		}

		$res = [];
		foreach($data1 as $k => &$v){
			if(in_array($v[1],$references)){
				$v[10] = "5.电子版审核通过";
				$res[] = $v;
			}
		}

		$rs = writeExcel($data1,$title1);
		$this->assign("filePath",$rs);
		return $this->fetch("statusSub");


	}

	/*
	 * 用途不明
	 */
	public function exportStreet(){
		exit;
		$filename = input("post.filename");
		$columns = input("post.columns");
		$uploads_dir = "../upload/";
		$recordModel = model("Record");
		$path = $uploads_dir . $filename;
		$excel = readExcel($path);

		$data = $excel['data'];
		$title = $excel['title'];

		$tmp = [];
		foreach($data as $k => $v){
			$tmp[$v[$columns]][] = $v;
		}



		$rs = [];
		$urls = [];
		foreach($tmp as $k => $v){
			$url = writeExcel($v,$title,$k."_".date("Y-m-d",time()));
			$urls[] = WEB_DIR."/xls/".$url;
			$rs[$k] = $url;
		}


		$zipFile = "街道_".date("Y-m-d"). ".zip ";
		$zipPath = WEB_DIR."/xls/".$zipFile;
		@unlink(trim($zipPath));
		$system = "zip ".$zipPath .implode(" ",$urls);
		exec($system,$rel);
		$this->assign("zipFile",$zipFile);
		$this->assign("filePath",$rs);
		return $this->fetch("street");
	}


	public function exportExcel(){
		$filename = input("post.filename");
		$columns = input("post.columns");
		$uploads_dir = "../upload/";
		$recordModel = model("Record");
		$path = $uploads_dir . $filename;
		$excel = readExcel($path);

		$data = $excel['data'];
		$title = $excel['title'];
		$title[] = "所属街道";
		$companys = model("Record")->getAll();
		$data = combine($data,$companys,$columns);
		$rs = writeExcel($data,$title);


		$this->assign("filePath",$rs);
		return $this->fetch("supplement");
	}


	public function compare(){
		if(!empty($_FILES['file']['name'])){
			$v = $_FILES['file'];
			$tmp_name =  $v['tmp_name'];
			$names = filename($v['name']);
			$suf = $names['name'];
			$ex = $names['exp'];
			$suf = "up_".md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);

			$recordModel = model("Record");
			$excel = readExcel($uploads_dir.$suf);


			$data = $excel['data'];
			$title = $excel['title'];

			$this->assign("filename",$suf);
			$this->assign("title",$title);


			return $this->fetch("supplement");
		}
		else{
			return $this->fetch("compare");
		}
	}



	/*
	 * 导入街道匹配库
	 *  1 公司名  2 地址 3 街道
	 */


	public function export(){
		$uploads_dir = "../upload/";
		if(!empty($_FILES['file']['name'])){
			$v = $_FILES['file'];
			$tmp_name =  $v['tmp_name'];
			$names = filename($v['name']);
			$suf = $names['name'];
			$ex = $names['exp'];
			$suf = "up_".md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);

			$recordModel = model("Record");
			$excel = readExcel($uploads_dir.$suf);
			$data = $excel['data'];
			$title = $excel['title'];
			$rs = $recordModel->addCompany($data);
			$this->ok = 1;
			#if($this->hasCrash !==0){
			#$this->assign("crash",true);
			#}


		}
		$page =input("post.p",1);
		return $this->fetch("export");

	}

	public function comstreet(){
		$company_name = input("company_name");
		$model = model("vpcompany");
		$rs = $model->getStreet($company_name);
		echo json_encode($rs);

	}
	public function lookfor(){
		return $this->fetch("lookfor");
	}


}
