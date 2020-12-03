<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class User extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}



	/*
	 * index 个人主页
	 * changePwd 修改密码
	 */

	public function changePwd(){
		$this->assign("list_num",11);

		if(isset($_POST['sub'])){
			$old_passwd = input("old_passwd");
			$new_passwd = input("new_passwd");
			$repeat_passwd = input("repeat_passwd");


			$error = [];
			if($new_passwd != $repeat_passwd){
				$error['repeat'] = "密码不一致";
			}



			$user = $_SESSION['user'];
			$user_id = $user['user_id'];

			$userModel = model("user");
			$userRs = $userModel->getOne($user_id);

			if($userRs->pwd != md5($old_passwd)){
				$error['old_passwd'] = "密码不正确";
			}


			if(empty($error)){
				$userModel->editInfo(['pwd'=>md5($new_passwd)],['user_id'=>$user_id]);
				$this->assign("ok",1);
			}
			else{
				$this->assign("error",$error);
			}





		}
		return $this->fetch("changePwd");
	}

	public function index(){

		$this->assign("list_num",11);


		$userModel = model("user");
		$user = $_SESSION['user'];
		$userRs = $userModel->getOne($user['user_id']);
		if(isset($_POST['sub'])){
			$error = [];
			$nickname = input("post.nickname");

			if(time() - $userRs['last_time'] <= 60 * 30)
				$error['nickname'] = '修改过快';
			else{
				$rs = $userModel->editInfo($user['user_id'],['nickname'=>$nickname]);
				$userRs->nickname=$nickname;
				$_SESSION['user']['nickname'] = $nickname;
			}

			$this->assign("ok",1);

		}
		$this->assign("user",$userRs);
		return $this->fetch("info");

	}



	/*
	 * my 我的消息
	 * read 设为己读
	 */
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

}

