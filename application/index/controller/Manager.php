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


	public function search(){
		$search = input("search");

		$userModel = model("user");
		$list = $userModel->search($search);
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

		$positionModel = model("position");

		foreach($list as $k => &$v){
		#	$v['levelName'] = isset($levelRs[$v->level_id]) ? $levelRs[$v->level_id] : "";
			$v['groups'] = isset($userGroups[$v->user_id]) ? $userGroups[$v->user_id] :  [];
			$v['position'] = $positionModel->getOneByUserId($v->user_id);
		}
		unset($v);

		$this->assign("levels",$levels);
		$this->assign("groups",$groups);



		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("user_list");

	}

	public function user(){
		$this->assign("list_num",14);
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

		$positionModel = model("position");

		foreach($list as $k => &$v){
		#	$v['levelName'] = isset($levelRs[$v->level_id]) ? $levelRs[$v->level_id] : "";
			$v['groups'] = isset($userGroups[$v->user_id]) ? $userGroups[$v->user_id] :  [];
			$v['position'] = $positionModel->getOneByUserId($v->user_id);
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
		$error = [];
		if(input("sub")){
			$group_name = input("group_name");
			if(empty($group_name))
				$error['group_name'] = "不可为空";
			$model = model("group");
			$rs = $model->isExist($group_name);

			if(!$rs)
				$error['group_name'] = "已经存在";

			if(empty($error)){
				$this->assign("status","增加成功");
				$model->addGroup($group_name);
			}

			else{
				$this->assign("error",$error);
			}



		}
		return $this->fetch("addGroup");
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

		$this->assign("list_num",14);
		$groupModel = model("group");
		$list = $groupModel->list();
		$page = $list->render();

		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("group_list");
	}


	public function update(){
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

		$group_id = input("group_id");
		if(isset($_POST['cate'])){
			$cate = input("cate");
			$model = model("GroupCategory");

			$cates = explode(",",$cate);
			$temp = [];
			foreach($cates as $k => $v){
				if($v > 0)
					$temp[] = $v;
			}



			$model->add($group_id,$temp);
			$this->assign("status","修改成功");

		}
		if(empty($group_id) || !is_numeric($group_id)){
			return false;
		}
		$model = model("group");
		$group = $model->getOne($group_id);


		$cateModel = model("category");
		$cates = $cateModel->getAll();

		$groupCateModel = model("GroupCategory");
		$selectedCate = $groupCateModel->getAllByGid($group_id);


		$selected = [];
		foreach($selectedCate as $k => $v){
			$selected[] = $v['category_id'];
		}
		$table = $this->build(1,$cates,0,$selected);


		$this->assign("group_id",$group_id);
		$this->assign("table",$table);
		$this->assign("cates",$cates);
		$this->assign("cated",json_encode($selected));

		return $this->fetch("category_list");

	}



	public function category(){
	}

	public function build($n,$cates,$layer = 0,$selected){
		$genTd = function($n){
			$str = "";
			for($i =2;$i<=$n;$i++){
				$str .= "<td></td>";
			}
			return $str;
		};


		$str = "";
		$obj = $cates[$n];
		$childs = json_decode($cates[$n]['childs']);
		$layer++;

		foreach($childs as $k => $v){
			$child = $cates[$v];
			if($child['parent_id'] != 1){
				if(in_array($child['category_id'],$selected)){
					$str .="<tr cate_id='".$child['category_id']."' style='background:#19db19' class='select tr_".$child['category_id']."'>";
				}
				else{
					$str .="<tr cate_id='".$child['category_id']."' class='select tr_".$child['category_id']."'>";
				}
			}
			else{
				$str .="<tr cate_id='".$child['category_id']."' class='tr_".$child['category_id']."'>";
			}
			$str .= $genTd($layer)."<td>";
			$str .= $child['category_name'];
			$str .= "</td></tr>";

			if(!empty($child['childs'])){
				$str .= $this->build($v,$cates,$layer,$selected);
			}


		}
		return $str;



	}


}
