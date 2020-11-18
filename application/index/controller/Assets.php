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

	/*
	 * addItem 增加物品
	 * myassets 个人资产
	 * hasFinish 己完结流转
	 * checkList 正在进行中流转
	 * verify  审核列表
	 * asset 当事人资产
	 * apply_confirm 流转确认
	 * applys 申请流转
	 * allow 审核通过
	 * deny 审核不通过
	 * roam 人员列表明
	 * detail 资产详情
	 * print 打印
	 *
	 *
	 */

	public function addItem(){
		$this->assign("list_num",21);
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
		$this->assign("list_num",21);
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

	public function hasFinish(){
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];

		$model = model("UserRoam");
		$lists = $model->getFinishLists($user_id);
		$model = model("roam");
		$data = [];
		$temp = [];



		$itemModel = model("item");
		$userModel = model("user");
		foreach($lists as $k => &$v){
			$item = $itemModel->getOne($v['item_id']);
			$users = $userModel->getRoamUsers($v);
			$v->users = $users;
			$v->item = $item;
		}

		$this->assign("list",$lists);
		$this->assign("page",$lists->render());
		$this->assign("tip",3);
		return $this->fetch("apply_list");
	}
	public function checkList(){
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];

		$model = model("UserRoam");
		$lists = $model->getCheckLists($user_id);
		$model = model("roam");
		$data = [];
		$temp = [];



		$itemModel = model("item");
		$userModel = model("user");
		foreach($lists as $k => &$v){
			$item = $itemModel->getOne($v['item_id']);
			$users = $userModel->getRoamUsers($v);
			$v->users = $users;
			$v->item = $item;
		}



		$this->assign("tip",2);
		$this->assign("list",$lists);
		$this->assign("page",$lists->render());
		return $this->fetch("apply_list");
	}

	public function verify(){
		$this->assign("list_num",21);
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];

		$model = model("UserRoam");
		$lists = $model->getLists($user_id);
		$model = model("roam");
		$data = [];
		$temp = [];



		$itemModel = model("item");
		$userModel = model("user");
		$model = model("roam");
		foreach($lists as $k => &$v){
			$item = $itemModel->getOne($v['item_id']);
			$users = $userModel->getRoamUsers($v);
			$v->users = $users;
			$v->item = $item;
		}

		$this->assign("list",$lists);
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


		$roamModel = model("roam");

		$page = Request::instance()->get("page",1);



		$data = [];
		$temp = [];



		foreach($list as $k => $v){
			$temp =  array_merge($v->getData(),$models[$v['model_id']]);
			$isRoamIng = $roamModel->checkRoamIng($temp['item_id']);
			if($isRoamIng){
				$temp['isRoam'] = 1;
			}
			else{
				$temp['isRoam'] = 0;
			}
			$data[] = $temp;
		}
		$this->assign("list",$data);
		$this->assign("tip",1);
		return $this->fetch("asset_list");

	}

	public function apply_confirm(){
		#$item_ids= Request::instance()->post('ids',[]);
		$item_ids = $_POST["ids"];
		foreach($item_ids as $k => $v){
			if(!is_numeric($v)){
				return ;
			}
		}
		$itemModel = model("item");
		$items = [];
		foreach($item_ids as $k => $v){
			$items[] = $itemModel->getOne($v);
		}

		$depModel = model("department");
		$deps = $depModel->getAll();

		#$list = $model->list();
		#$this->assign("list",$list);
		#$this->assign("page",$list->render());

		$this->assign("item_ids",implode(",",$item_ids));
		$this->assign("deps",$deps);
		$this->assign("items",$items);
		return $this->fetch("apply_confirm");

	}

	public function applys(){
		$reason = input("reason");
		$item_ids = input("item_ids");
		$send_to_user_id = input("use_user_id");
		$user = $_SESSION['user'];
		$apply_user_id = $user['user_id'];

		$item_ids = explode(",",$item_ids);
		foreach($item_ids as $k => $v){
			if(!is_numeric($v))
				return false;
		}

		foreach($item_ids as $k => $v){
			$rs = 	$this->apply($apply_user_id,$send_to_user_id,$v,$reason);
		}
		return $this->success("流转申请成功","/index/assets/roam");

		
	}

	private function apply($apply_user_id,$send_to_user_id,$item_id,$reason){
		$model = model("item");
		$item = $model->getOne($item_id);
		$user = $_SESSION['user'];
		if(!$item)
			return false;

		$model = model("user");

		$top = $model->getTop($send_to_user_id);
		if(!$top)
			return false;
		$apply_approval_user_id = $top['user_id'];
		$use_user_id = $item['user_id'];

		$top = $model->getTop($use_user_id);
		if(!$top)
			return false;
		$use_approval_user_id = $top['user_id'];

		$office_approval_user_id = 8;

		$model = new \app\index\model\Roam();

		$checkIng = $model->checkRoamIng($item_id);
		if($checkIng){
		//	die(json_encode(false));
			return ;
		}

		$roam_id = $model->apply($item_id,$apply_user_id,$apply_approval_user_id,$use_user_id,$use_approval_user_id,$office_approval_user_id,$reason);

		$model = model("message");

		$title = "固定资产流转";
		$message = "申请人:".$user['uname']." 申请资产:".$item['model_name'];
		$url = "/index/assets/verify";
		$model->notify([$apply_user_id,$apply_approval_user_id,$use_user_id,$use_approval_user_id,$office_approval_user_id],$title,$message."@原因:".$reason,$url);
		$rs = false;
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
		$model = model("user");
		$depModel = model("department");
		$deps = $depModel->getAll();

		#$list = $model->list();
		#$this->assign("list",$list);
		#$this->assign("page",$list->render());

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



		$roamModel = model("roam");
		$depModel = model("user_department");
		$roamRs = $roamModel->getEndByItemId($item_id);
		$roamRs = array_reverse($roamRs);
		$userModel = model("user");
		$logs = [];
		foreach($roamRs as $k => $v){
			$temp = $v;
			$temp['apply_user'] = $userModel->getOne($v['apply_user_id']);
			$temp['use_user'] = $userModel->getOne($v['use_user_id']);
			$temp['apply_approval_user'] = $userModel->getOne($v['apply_approval_user_id']);
			$temp['use_approval_user'] = $userModel->getOne($v['use_approval_user_id']);
			$temp['office_approval_user'] = $userModel->getOne($v['office_approval_user_id']);

			$temp['use_dep'] = $depModel->getOneByUid($temp['use_user_id']);
			$temp['apply_dep'] = $depModel->getOneByUid($temp['apply_user_id']);


			$logs[] = $temp;
		}

		$this->assign("item",$item);
		$user_id = $item['user_id'];
		$user = $userModel->getDetail($user_id);
		$this->assign("user",$user);
		$this->assign("logs",$logs);
		$this->assign("item_id",$item_id);

		return $this->fetch("item_detail");
	}

	public function print(){
		$item_id = input("item_id");
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



		$roamModel = model("roam");
		$roamRs = $roamModel->getByItemId($item_id);
		$roamRs = array_reverse($roamRs);
		$userModel = model("user");
		$depModel = model("user_department");
		$logs = [];
		foreach($roamRs as $k => $v){
			$temp = $v;
			$temp['apply_user'] = $userModel->getOne($v['apply_user_id']);
			$temp['use_user'] = $userModel->getOne($v['use_user_id']);
			$temp['apply_approval_user'] = $userModel->getOne($v['apply_approval_user_id']);
			$temp['use_approval_user'] = $userModel->getOne($v['use_approval_user_id']);
			$temp['office_approval_user'] = $userModel->getOne($v['office_approval_user_id']);
			$temp['use_dep'] = $depModel->getOneByUid($temp['use_user_id']);
			$temp['apply_dep'] = $depModel->getOneByUid($temp['apply_user_id']);
			$logs[] = $temp;
		}

		$this->assign("item",$item);
		$user_id = $item['user_id'];
		$user = $userModel->getDetail($user_id);
		$this->assign("user",$user);
		$this->assign("logs",$logs);
		$this->assign("item_id",$item_id);

		return $this->fetch("item_print");
	}


}


