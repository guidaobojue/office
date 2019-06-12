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

		$error = [];

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



				/*
				foreach($data as $k => $v){
					$userModel = new user();
					$userModel->addUser($v[1],$v[0],$v[2]);
				}
				exit;
				 */



				$models = [];
				$modelIds = [];
				$modelNum = [];
				$modelModel = [];
				$items= [];
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
					$temp['purpose'] = $v[15];
					$temp['status'] = $v[12];
					$temp['get_time'] = $v[2];
					$temp['comments'] = $v[16];
					$temp['create_time'] = $time;
					$temp['model_id'] = $model_id;
					$temp['user_id'] = $userModel->getUserIdByNameDep($v[13],$v[14]);

					if(!$temp['user_id']){
						$error[] = $v;
						continue;
					}
					$items[] = $temp;

				}

				if(!empty($error)){
					record("无此人信息",$error);
				}


				$model = model("item");
				$model->saveAll($items);
				$this->assign("status",true);
				return $this->fetch("add_item");

			}
		}
		else{
			return $this->fetch("add_item");
		}

	}


	public function myassets(){
		$page = Request::instance()->get('page',1);
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];
		$model = model("item");

		$list = $model->list($user_id);
		$this->assign("page",$list->render());

		$model = model("itemModel");
		$models = $model->getAll();


		$data = [];
		foreach($list as $k => $v){
			$data[] = array_merge($v->getData(),$models[$v['model_id']]);
		}
		$this->assign("list",$data);
		return $this->fetch("item_list");

	}


	public function verify(){
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];

		$model = model("UserRoam");
		$lists = $model->getLists($user_id);
		$model = model("roam");
		$data = [];
		$temp = [];



		$itemModel = model("item");
		$userModel = model("user");
		foreach($lists as $k => $v){
			$temp  = $model->getOne($v['roam_id']);
			$temp['roam_status'] = $temp['status'];
			$item = $itemModel->getOne($temp['item_id']);

			$users = $userModel->getRoamUsers($temp);
			$temp['users'] = $users;
			$data[] = array_merge($temp,$item);
		}



		$this->assign("list",$data);
		$this->assign("page",$lists->render());
		return $this->fetch("apply_list");

	}

	public function asset(){
		$user_id= Request::instance()->get('user_id',false);
		if(!$user_id || !is_numeric($user_id))
			return false;

		$model = model("item");

		$list = $model->list($user_id);
		$this->assign("page",$list->render());

		$model = model("user");
		$user = $model->getOne($user_id);
		$this->assign("user",$user);


		$model = model("itemModel");
		$models = $model->getAll();

		$model = model("roam");
		$roams = $model->getRoamIng();

		$data = [];
		$temp = [];
		foreach($list as $k => $v){
			$temp =  array_merge($v->getData(),$models[$v['model_id']]);
			if(isset($roams[$temp['item_id']])){
				$temp['isRoam'] = 1;
			}
			else{
				$temp['isRoam'] = 0;
			}
			$data[] = $temp;
		}
		$this->assign("list",$data);
		return $this->fetch("asset_list");

	}

	public function roam_detail(){

	}	



	public function apply(){
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];
		$item_id = Request::instance()->get("item_id",false);
		if(!is_numeric($item_id) || !$item_id)
			return false;

		$model = model("item");
		$item = $model->getOne($item_id);
		if(!$item)
			return false;



		$model = model("user");


		$top = $model->getTop($user_id);
		if(!$top)
			return false;
		$apply_approval_user_id = $top['user_id'];
		$use_user_id = $item['user_id'];

		$top = $model->getTop($use_user_id);
		if(!$top)
			return false;
		$use_approval_user_id = $top['user_id'];

		$office_approval_user_id = 8;

		$model = model("roam");
		$roam_id = $model->apply($item_id,$user_id,$apply_approval_user_id,$use_user_id,$use_approval_user_id,$office_approval_user_id);

		$model = model("message");

		$title = "固定资产流转";
		$message = "申请人:".$user['uname']." 申请资产:".$item['model_name'];
		$url = "/index/assets/verify";
		$model->notify([$user_id,$apply_approval_user_id,$use_user_id,$use_approval_user_id,$office_approval_user_id],$title,$message,$url);
		$rs = false;
		if($roam_id)
			$rs = true;
		echo json_encode($rs);
	}


	public function allow(){
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];
		$roam_id = Request::instance()->get("roam_id",0);
		if(empty($roam_id) || !is_numeric($roam_id))
			return false;

		$model = model("roam");
		$logModel = model("RoamLog");

		$roam = $model->getOne($roam_id);
		$use_user_id = $roam['use_user_id'];

		$users_id = [
			1 => $roam['apply_user_id'],
			2 => $roam['apply_approval_user_id'],
			3 => $roam['use_approval_user_id'],
			4 => $roam['office_approval_user_id'],
		];


		$user_id = 68;
		$score = 1;
		foreach($users_id as $k => $v){
			if($user_id == $v){
				$score = $k;
				if($k > $roam['status']){
					break;
				}
			}
		}

		if($roam['status']== $score-1){
			if($roam['status'] == 3){
				$itemModel = model("item");
				$itemModel->chgUser($roam['item_id'],$roam['apply_user_id']);
				$logModel->record($user_id,1,$roam['item_id']);
				$endLogModel = model("RoamEndLog");
				$endLogModel->record($user_id,$roam['apply_user_id'],$roam['item_id']);
				$model->allow($roam_id);

			}
			else{
				$model->allow($roam_id);
				$logModel->record($user_id,1,$roam['item_id']);
			}
		}

		echo json_encode(true);
	}

	public function deny(){
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];
		$roam_id = Request::instance()->get("roam_id",0);
		if(empty($roam_id) || !is_numeric($roam_id))
			return false;

		$model = model("roam");
		$logModel = model("RoamLog");

		$roam = $model->getOne($roam_id);
		$use_user_id = $roam['use_user_id'];

		$users_id = [
			1 => $roam['apply_user_id'],
			2 => $roam['apply_approval_user_id'],
			3 => $roam['use_approval_user_id'],
			4 => $roam['office_approval_user_id'],
		];


		$user_id = 68;
		$score = 1;
		foreach($users_id as $k => $v){
			if($user_id == $v){
				$score = $k;
				if($k > $roam['status']){
					break;
				}
			}
		}


		if($roam['status']== $score-1){
			$model->deny($roam_id);
			$logModel->record($user_id,0,$roam['item_id']);
		}

		echo json_encode(true);
	}


	public function roam(){
		$page = Request::instance()->get('page',1);
		$model = model("user");
		$depModel = model("department");
		$deps = $depModel->getAll();

		#$list = $model->list();
		#$this->assign("list",$list);
		#$this->assign("page",$list->render());

		$this->assign("list",['1'=>'123']);
		$this->assign("deps",$deps);
		return $this->fetch("department_list");
	}


	public function detail(){
		$item_id = Request::instance()->get("item_id",false);
		if(!$item_id || !is_numeric($item_id))
			return false;
		$model = model("item");

		$item = $model->getOne($item_id);
		$temp = [];
		$data = [];
		foreach($item as $k => $v){
			if(count($temp)<2){
				$temp[] = $v;
			}
			else{
				$data[] = $temp;
				$temp = [];
			}
		}
		if(!empty($temp))
			$data[] = $temp;


		$endLogModel = model("RoamEndLog");
		$logs = $endLogModel->getByITemId($item_id);

		$userIds = [];
		foreach($logs as $k => $v){
			$userIds[] = $v['user_id'];
			$userIds[] = $v['to_user_id'];
		}
		$userIds = array_unique($userIds);
		$users = [];
		$userModel = model("user");
		foreach($userIds as $k => $v){
			$users[$v] = $userModel->getOne($v);
		}

		foreach($logs as $k => $v){
			if(isset($users[$v['user_id']]))
				$logs[$k]['user'] = $users[$v['user_id']];
			if(isset($users[$v['to_user_id']]))
				$logs[$k]['to_user'] = $users[$v['to_user_id']];
		}



		$this->assign("item",$item);
		$user_id = $item['user_id'];
		$model = model("user");
		$user = $model->getDetail($user_id);
		$this->assign("user",$user);
		$this->assign("logs",$logs);

		return $this->fetch("item_detail");
	}


}


