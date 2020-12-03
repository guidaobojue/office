<?php
namespace app\index\controller;
use app\index\model\ItemModel;
use \think\view;
use \think\Request;
use \think\Model;
use \think\Config;
use app\index\model\User;

class Rencai extends \think\Controller
{
	public function __construct(){
		parent::__construct();
	}

	/*@desc 新增单位*/
	public function addCert(){
		if(isset($_POST['sub'])){
			$data = $_POST;
			unset($data['sub']);
			$model = model("Cert");
			#$rs = $model->check($data['company_name']);
			#if(!$rs)
			$id = $model->add($data);
			$this->assign("new_id",$id);
		}
		return $this->fetch("cert");

	}
	public function chgCert(){
		$model = model("cert");
		if(isset($_POST['sub'])){
			$data = $_POST;
			$cert_id = $data['cert_id'];
			unset($data['sub']);
			$model->chg($cert_id,$data);
			$this->redirect("/index/common/certlist");

		}
		else{
			$cert_id = input("id");
		}
		$rs = $model->getOne($cert_id);
		$this->assign("d",$rs);
		return $this->fetch("chgCert");
	}

	public function certlist(){
		$model = model("cert");
		$search= input("search");
		$rs = $model->getAll($search);


		$this->assign("page",$rs->render());
		$this->assign("list",$rs);
		return $this->fetch("cert_list");

	}


	/*@desc 增加新办数量*/
	public function addNewNum(){
		$model = model("cert");
		$cert_id = input("cert_id");
		$rs = $model->chgNewNum($cert_id,1);
		die(json_encode($rs));

	}
	/*@desc 减少新办数量*/
	public function subNewNum(){
		$model = model("cert");
		$cert_id = input("cert_id");
		$rs = $model->chgNewNum($cert_id,-1);
		die(json_encode($rs));
	}


	/*@desc 增加己办数量*/
	public function addHasNum(){
		$model = model("cert");
		$cert_id = input("cert_id");
		$rs = $model->chgHasNum($cert_id,1);
		die(json_encode($rs));
	}

	/*@desc 减少己办数量*/
	public function subHasNum(){
		$model = model("cert");
		$cert_id = input("cert_id");
		$rs = $model->chgHasNum($cert_id,-1);
		die(json_encode($rs));
	}



}


