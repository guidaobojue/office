<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Supplies extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}

	public function create(){
		$name = input("name","");
		$model = model("Supplies");
		if(!empty($name)){
			$user = $_SESSION['user'];
			$user_id = $user['user_id'];
			$supplies_id = $model->addSupplies($user_id,$name);
		}

		$list = $model->list();
		$page = $list->render();
		$this->assign("page",$page);
		$this->assign("list",$list);
		return $this->fetch("create");


	}

	public function end(){
		$supplies_id = input("id");
		$model = model("supplies");
		$model->end($supplies_id);
		echo json_encode(1);
	}


	public function detail(){
		$supplies_id = input("id");
	}



	public function add(){
		$model = model("Supplies");
		$list = $model->listing();
		$page = $list->render();
		$this->assign("page",$page);
		$this->assign("list",$list);
		return $this->fetch("list");
	}

	public function apply(){
		$supplies_id = input("id");
		$model = model("Supplies");
		$supInfo = $model->getOne($supplies_id);
		if(empty($supInfo))
			return false;




		return $this->fetch("apply");

	}

	public function my(){
	}

	public function items(){
		$model = model("suppliesitem");
		$list = $model->itemList();
		$page = $list->render();
		$this->assign("page",$page);
		$this->assign("list",$list);
		return $this->fetch("item_list");
	}


	public function addItem(){
		$dosub = input("sub","");
		if(!empty($dosub)){
			$name = input("name");
			$unit = input("unit");
			$model = model("suppliesitem");

			$error = [];
			$isExist = $model->isExist($name);
			if(!$isExist){
				$model->add($name,$unit);
			}
			else{
				$erorr[] = "己存在";
			}

			


			$this->assign("error",$error);



		}
		else{
			return $this->fetch("add_item");
		}
	}


}
