<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Materiel extends \think\Controller
{

	public function __construct(){
		parent::__construct();
	}



	public function index(){
		$this->assign("list_num",14);
		$model = model("Printer");
		$list = $model->list();
		$page = $list->render();

		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("list");


	}

	public function add(){
		if(isset($_POST['sub'])){
			$model_ids = $_POST['materiel_model_id'];
			$nums = $_POST['num'];
			$data = [];
			for($i=0;$i<count($model_ids);$i++){
				$data[] = [
					'id' => $model_ids[$i],
					'num' => (int)$nums[$i],
				];
			}

			$model = model("materiel");
			$model->addItems($data);
			return $this->redirect("/index/materiel/index");



		}
		$model = model("materiel");
		$list = $model->getAll();
		$this->assign("models",$list);
		return $this->fetch("add");
	}

	public function addItem(){
		$printerModel = model("printer");
		$printerList = $printerModel->getAll();
		if(input("?post.sub")){
			$model_names = $_POST['model_name'];
			$machine_ids = $_POST['machine_ids'];
			$matModel = model("Materiel");
			$temp = [];
			for($i=0;$i<count($model_names);$i++){
				$temp[] = 
					[
						'model_name' => $model_names[$i],
						'machine_id' => $machine_ids[$i],
					];
					
			}


			if(empty($model_names)){
				return $this->redirect("/index/materiel/index");
			}
			$rs = $matModel->checkModel($temp);
			return $this->redirect("/index/materiel/index");
		}
		$this->assign("printlist",$printerList);
		return $this->fetch("addItem");
	}


	public function materiel_list(){
		$num = input("num",0);
		$machine_id = input("machine_id");
		$model = model("materiel");

		$materielList = $model->getByMachineId($machine_id);
		$this->assign("materiels",$materielList);
		return $this->fetch("materiel_list");

	}



	public function consume(){
		$user_id = input("user_id");
		$materiel_model_id = input("materiel_model_id");
		$num = input("num");
		if(empty($user_id) || empty($materiel_model_id) || empty($num)){
			$this->error("参数错误");
		}

		$model = model("materiel");
		$rs = $model->consume($materiel_model_id,$user_id,$num);
		if($rs){
			return $this->success("成功");
		}
		else{
			return $this->error("失败");
		}
	}



	public function  addModel(){
	}

	public function delModel(){
	}

	public function changeModel(){
	}

	public function stat(){
		$model_id = input("model_id");
		$model = model("Materiel");
		$dbrs = $model->getStat($model_id,5);
		$rs = [];
		foreach($dbrs as $k => $v){
			$rs[$v->table_id] = [
				'table_id' => $v->table_id,
				'model_id' => $v->model_id,
				'create_time' => $v->create_time,
				'user_id' => $v->user_id,
				'num' => $v->num,
			];
		}

		$userModel = model("user");
		$data = [];


		$userIds = [];
		foreach($rs as $k => $v){
			if(!in_array($v['user_id'],$userIds)){
				$userIds[] = $v['user_id'];
			}
		}
		$userInfo = [];
		foreach($userIds as $k => $v){
			$tmp  = $userModel->getOne($v);
			$userInfo[$v] = $tmp['uname'];
		}

		$data = [];
		foreach($rs as $k => $v){
			$tmp = $v;
			$tmp['uname'] = $userInfo[$v['user_id']];
			$data[$k] = $tmp;
		}
		$page = $dbrs->render();
		$this->assign("list",$data);
		$this->assign("page",$page);
		return $this->fetch("stat");

	}


}

