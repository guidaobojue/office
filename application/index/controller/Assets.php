<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use \think\Config;

class Assets extends \think\Controller
{
	public function __construct(){
		parent::__construct();
	}

	public function addItem(){

		$a = [
			'id' => 3,
			'ids' => 4,
		];
		$b = [
			'ids' => 4,
			'id' => 4,
		];

		var_dump($a == $b);

		exit;
		$uploads_dir = "../upload/";
		if(isset($_FILES['file'])){
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


				$data = array_slice($excel['data'],2);

				$time = time();
				$batch_no = time() .rand(10000,20000);

				$models = [];
				foreach($data as $k => $v){
					$temp = [];
					$temp['name'] = $v[3];
					$temp['value'] = $v[4];
					$temp['category'] = $v[5];
					$temp['model'] = $v[6];
					$temp['account_id'] = $v[7];
					$temp['num'] = 0;
					$temp['methods'] = $v[11];
					$temp['get_time'] = $v[2];
					$temp['comments'] = $v[15];
					$temp['create_time'] = $time;
					$temp['batch_no'] = $batch_no;

					$models[] = $temp;
					$temp = [];

					$temp['user'] = $v[13];
					$temp['department'] = "部门";
					$temp['status'] = $v[12];
					$temp['assets_id'] = $v[1];
					$temp['model'] = $v[6];

					$items[] = $temp;
				}

				$model = model("itemmodel");
				$model->saveAll($models);
				exit;










			}
		}
		else{
			return $this->fetch("add_item");
		}

	}

	public function verify(){

	}

	public function roam(){
	}
}


