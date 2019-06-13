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


	public function getUserByDep(){
		$dep_id = Request::instance()->get("dep_id");
		$userModel = model("user");
		$depModel = model("department");
		$deps = $depModel->getAll();
		$users = $userModel->getListByDep($dep_id);

		echo json_encode($users);
		/*
		foreach($users as $k => $v){
			if(!isset($data[$v['department_id']])){
				$data[$v['department_id']]['users'][] =$v;
				$data[$v['department_id']]['name'] =$deps[$v['department_id']];
			}
			else{
				$data[$v['department_id']]['users'][] =$v;
			}

		}
		 */
	}

}

