<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Manager extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}

	public function index()
	{

		if(!isset($_SESSION['user'])){
			$this->redirect("/index/index/login");
			return true;
		}
		return $this->fetch("index");
	}


	public function user(){

		$userModel = model("user");
		$list = $userModel->list();
		$page = $list->render();

		$levelModel = model("level");
		$groupModel = model("group");

		$levels = $levelModel->getAll();
		$groups = $groupModel->getAll();
		$userGroups = $groupModel->getAllUserGroups();

		$levelRs = [];
		$groupRs = [];
		foreach($levels as $k => $v){
			$levelRs[$v->level_id] = $v->level_name;
		}

		foreach($groups as $k => $v){
			$groupRs[$v->group_id] = $v->group_name;
		}


		foreach($list as $k => &$v){
			$v['levelName'] = isset($levelRs[$v->level_id]) ? $levelRs[$v->level_id] : "";
			$v['groups'] = isset($userGroups[$v->user_id]) ? $userGroups[$v->user_id] :  [];
		}
		unset($v);

		$this->assign("levels",$levels);
		$this->assign("groups",$groups);



		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("user_list");

	}


	public function login(){
		if(!empty($_SESSION)){
			$this->redirect("/index/index/index");
			return ;
		}

		if(input("?post.uname")){
			$uname = input("post.uname");
			$pwd = input("post.pwd");

			$userModel = model("User");
			$rs = $userModel->login($uname,$pwd);

			if($rs){
				$this->redirect("/index/index/index");
			}
			else{
				$this->error['uname'] = 'username error';
				$this->assign("error",$this->error);
				return $this->fetch("login");
			}

		}
		else{
				return $this->fetch("login");
		}
				return $this->fetch("login");
	}


	public function register(){
		$user_id = input("user_id");
		$userModel = model("user");
		$levelModel = model("level");
		$groupModel = model("group");


		if(input("sub")){

			$passwd = input("passwd");
			$level_id = input("level_id");
			$group_id = input("group_id");

			$data['level_id'] = $level_id;
			if(!empty($passwd))
				$data['pwd']  = md5($passwd);

			$data['level_id'] = $level_id;
			$uname = input("username");
			$data['uname'] = $uname;
			$user_id = $userModel->register($data);


			if(!$user_id){
				$this->assign("error","用户名已存在");
				return $this->fetch("addUser");
			}

			$groupModel->editUserGroup($user_id,$group_id);





			$groupInfo = $groupModel->getOneByUserId($user_id);
			if(!empty($groupInfo)){
				$groupInfo = $groupModel->getOne($group_id);
			}
			else{
				$groupInfo = [];
			}
			$this->assign("group_id",$group_id);
			$this->redirect("/index/manager/user");

		}


		$groups = $groupModel->getAll();
		$levels = $levelModel->getAll();


		$userInfo = $userModel->getOne($user_id);
		$level_id = $userInfo['level_id'];
		$levelInfo = $levelModel->getOne($level_id);


		$this->assign("user",$userInfo);
		$this->assign("groups",$groups);


		$this->assign("user_id",$user_id);
		$this->assign("level_id",$level_id);
		$this->assign("levels",$levels);



		return $this->fetch("addUser");
	}



	public function editUser(){
		$user_id = input("user_id");
		$userModel = model("user");
		$levelModel = model("level");
		$groupModel = model("group");


		if(input("sub")){

			$passwd = input("passwd");
			$level_id = input("level_id");
			$group_id = input("group_id");

			$data['level_id'] = $level_id;
			if(!empty($passwd))
				$data['pwd']  = md5($passwd);

			$userModel->editInfo($user_id,$data);
			$groupModel->editUserGroup($user_id,$group_id);





			$groupInfo = $groupModel->getOneByUserId($user_id);
			if(!empty($groupInfo)){
				$groupInfo = $groupModel->getOne($group_id);
			}
			else{
				$groupInfo = [];
			}
			$this->assign("group_id",$group_id);
			$this->redirect("/index/manager/user");

		}


		$groups = $groupModel->getAll();
		$levels = $levelModel->getAll();


		$userInfo = $userModel->getOne($user_id);
		$level_id = $userInfo['level_id'];
		$levelInfo = $levelModel->getOne($level_id);


		$this->assign("user",$userInfo);
		$this->assign("groups",$groups);


		$this->assign("user_id",$user_id);
		$this->assign("level_id",$level_id);
		$this->assign("levels",$levels);



		return $this->fetch("editUser");
	}

	public function delUser(){
		$user_id = input("user_id");
		$model = model("user");
		$model->delUser($user_id);
		echo json_encode(1);

	}


	public function group(){
		$groupModel = model("group");
		$list = $groupModel->list();
		$page = $list->render();

		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("group_list");
	}


	public function editGroup(){
		$group_id = input("group_id");
		$groupModel = model("group");
		$groupInfo = $groupModel->getOne($group_id);
		$this->assign("group",$groupInfo);

		if(isset($_POST['sub'])){
			$group_name = input("group_name");
			$rs = $groupModel->rename($group_id,$group_name);

			if(!$rs){
				$this->assign("errors",['group_name'=>'已存在']);
				return $this->fetch("editGroup");
			}
			else
				$this->redirect("/index/manager/group");


		}
		return $this->fetch("editGroup");





	}


	public function pri(){
	}


	public function category(){
	}


}
