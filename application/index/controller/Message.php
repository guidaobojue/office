<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Message extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}

	public function my(){
		$this->assign("list_num",26);
		$user = $_SESSION['user'];
		$user_id = $user['user_id'];
		$model = model("UserMessage");
		$list = $model->list($user_id);
		$page = $list->render();


		$data = [];
		$messageModel = model("message");
		foreach($list as $k => $v){
			$data[] = array_merge($messageModel->getOne($v['message_id']),$v->getData());

		}
		$this->assign("list",$data);
		$this->assign("page",$page);
		return $this->fetch("my_list");
	}


	public function read(){
		$message_id = Request::instance()->post('message_id',0);
		if(empty($message_id) || !is_numeric($message_id))
			return false;
		$model = model("UserMessage");
		$rs = $model->read($message_id);
		echo json_encode($rs);
	}



}

