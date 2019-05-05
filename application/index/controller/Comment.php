<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use \app\index\model\company;
use \app\index\model\job;

class Comment extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();
	}


	public function thanks(){
		return $this->fetch("thanks");
	}




	public function details(){
		$id = input("id");
		$model = model("Comment");
		$rs = $model->getOne($id);
		if(empty($rs['thumb'])){
			$rs['thumb'] = $rs['qr_thumb'];
		}
		$this->assign("info",$rs);
		return $this->fetch("details");
	}


	public function commentList(){
		$obj = model("comment");
		$data = [];
		$list= $obj->list();
		$page = $list->render();
		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("comment_list");
	}



	public function auditing(){
		$id = input("id");
		$model = model("Comment");
		$model->read($id);
		echo json_encode(1);

	}

}

