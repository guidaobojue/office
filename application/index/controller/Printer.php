<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Printer extends \think\Controller
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
			$model_names = $_POST['model_names'];

			$model = model("printer");
			$model->addItems($model_names);
			return $this->redirect("/index/printer/index");
		}
		$model = model("printer");
		$list = $model->getAll();
		$this->assign("models",$list);
		return $this->fetch("add");
	}

	public function addItem(){
		if(input("?post.sub")){
			$model_names = $_POST['model_name'];
			$comment = $_POST['comment'];
			$matModel = model("Materiel");
			if(empty($model_names)){
				return $this->redirect("/index/materiel/index");
			}
			$rs = $matModel->checkModel($model_names,$comment);
			return $this->redirect("/index/materiel/index");
		}
		return $this->fetch("addItem");
	}


	public function consume(){
		$user_id = input("user_id",null);
		$user_name = input("user_name",null);
		$num = input("num",0);
		$model_id = input("model_id");
		$model = model("materiel");

		if(empty($user_id) || empty($user_name) || empty($model_id)){
			$this->error("参数错误");
		}

		$data['model_id'] = $model_id;
		$data['num'] = $num;
		$data['user_id'] = $user_id;
		$data['create_time'] = time();
		$rs = $model->consume($data);
		if(!$rs){
			return $this->error("余量不足");
		}
		return $this->redirect("/index/materiel/index");
	}



	public function  addModel(){
	}

	public function del(){
		$model_id = input("model_id");
		$model = model("printer");
		$rs = $model->del($model_id);
		echo json_encode(true);
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

