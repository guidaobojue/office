<?php
namespace app\index\controller;
use app\index\model\ItemModel;
use \think\view;
use \think\Request;
use \think\Model;
use \think\Config;
use app\index\model\User;

class Assets extends \think\Controller
{
	public function __construct(){
		parent::__construct();
	}


	public function addItem(){







		$userModel = new user();
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




				foreach($data as $k => $v){
					$userModel = new user();
					$userModel->addUser($v[1],$v[0],$v[2]);
				}
				exit;



				$models = [];
				$modelIds = [];
				$modelNum = [];
				$modelModel = [];
				foreach($data as $k => $v){
					$modelModel = new ItemModel();
					$model = [];
					$model['model_name'] = $v[3];
					$model['price'] = $v[4];
					$model['category'] = $v[5];
					$model['model_no'] = $v[6];
					$model['account_id'] = $v[7];
					$model['num'] = 1;

					if(!in_array($model,$models)){
						$models[] = $model;
						$model_id = $modelModel->addModel($model);
						$modelIds[$model_id] = $model;
						$modelNum[$model_id] = 1;
					}
					else{
						foreach($modelIds as $ik => $iv){
							if($model == $iv){
								$Model_id = $ik;
							}
						}
						$modelNum[$model_id] += 1;
					}


					$temp = [];
					$temp['item_no'] = $v[1];
					$temp['account_time'] = $v[2];
					$temp['account_no'] = $v[7];
					$temp['method'] = $v[8];
					$temp['get_time'] = $v[9];
					$temp['mark_time'] = $v[9];
					$temp['forward'] = $v[11];
					$temp['purpose'] = $v[14];
					$temp['status'] = $v[12];
					$temp['get_time'] = $v[2];
					$temp['comments'] = $v[15];
					$temp['create_time'] = $time;
					$temp['model_id'] = $model_id;
					$items[] = $temp;
				}


				$model = model("item");
				$model->saveAll($items);
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


