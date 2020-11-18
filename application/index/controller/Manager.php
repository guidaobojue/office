<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use app\extra\pri;
use \think\Config;

class Manager extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}

	/*
	 * user 用户列表 
	 * editUser 修改用户
	 * addUser 增加用户
	 * delUser 删除用户
	 * level 级别列表
	 * chgPosition 级别修改
	 * search 搜索用户
	 * register 增加群组
	 * delGroup 删除群组
	 * editGroup 编缉群组 
	 * group  群组列表
	 * update 更新群组缓存 
	 * pri 查看群组权限 
	 *
	 */

	public function changePri(){
		$selected = isset($_POST['selected']) ? $_POST['selected'] : false;
		if(empty($selected)){
			$group_id = input("group_id");
			$groupModel = model("group");
			$groupInfo = $groupModel->getOne($group_id);
			$priModel = model("pri");

			$groupPriModel = model("grouppri");
			$pris = $priModel->getAll();

			$selectedPris = $groupPriModel->getSelectedPri($group_id);
			$temp = [];
			foreach($selectedPris as $k => $v){
				$temp[] = $v->pri_id;
			}

			$data = [];
			$top = reset($pris);
			$top_name = $top->controller_name;
			$top->isnew = true;
			foreach($pris as $k => &$v){
				if($v->controller_name != $top_name){
					$v->isnew = true;
					$top_name = $v->controller_name;
				}
				if(in_array($v->pri_id,$temp)){
					$v->selected = true;
				}
				else{
					$v->selected = false;
				}
			}



			$this->assign("pris",$pris);
			$this->assign("info",$groupInfo);
			return $this->fetch("changePri");
		}
		else{
			$group_id = input("group_id");
			if(empty($group_id))
				$this->redirect("/index/manager/changePri");
			$groupPriModel = model("grouppri");
			$groupPriModel->delByGroupId($group_id);
			$groupPriModel->addUserGroup($group_id,$selected);
			$this->success("修改成功","/index/manager/group");
		}
	}


	public function level(){
		$model = model("position");
		$rs = $model->getAll();
		$this->assign("list",$rs);

		return $this->fetch("level_list");
	}

	public function chgPosition(){
		$position_id = input("position_id");
		$model = model("position");

		if(!empty($_POST)){
			$level = input("level");
			$position_name = input("position_name");
			$model->updatePos($position_id,$level,$position_name);
			$this->redirect("/index/manager/level");

		}

		$rs = $model->getOne($position_id);
		$this->assign("position",$rs);
		$this->assign("position_id",$position_id);
		return $this->fetch("editPosition");

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
			if(empty($v['position'])){
				$v['position'] = null;
			}
		}
		unset($v);

		$this->assign("groups",$groups);



		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("user_list");

	}


	public function login(){
		die("fuck");
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


	public function addUser(){
		$error = [];
		$groupModel = model("group");
		$groups = $groupModel->getAll();
		$this->assign("groups",$groups);

		$levelModel = model("level");
		$levels = $levelModel->getAll();
		$this->assign("levels",$levels);

		if(input("sub")){
			$uname = input("username");
			$group_id = input("group_id");
			$level_id = input("level_id");
			$passwd = input("passwd");
			if(empty($uname))
				$error['user_name'] = "不可为空";
			$model = model("user");
			$rs = $model->isExist($uname);

			if($rs)
				$error['user_name'] = "用户名已经存在";

			if(empty($error)){
				$this->assign("status","增加成功");
				$data['uname'] = $uname;
				$data['pwd']  = md5($passwd);
				$data['level_id'] = $level_id;
				$user_id = $model->register($data);
				$model = model("UserGroup");
				$model->addUserGroup($user_id,$group_id);
			}

			else{
				$this->assign("error",$error);
			}



		}
		return $this->fetch("addUser");
	}


	public function editUser(){
		$user_id = input("user_id");
		$userModel = model("user");
		$levelModel = model("level");
		$groupModel = model("group");
		$positionModel = model("position");


		if(input("sub")){

			$passwd = input("passwd");
			$position_id = input("position_id");
			$group_id = input("group_id");

			#$data['position_id'] = $position_id;
			if(!empty($passwd)){
				$data['pwd']  = md5($passwd);
				$userModel->editInfo($user_id,$data);
			}

			$groupModel->editUserGroup($user_id,$group_id);
			$positionModel->updateUser($user_id,$position_id);


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


		$userGroupModel = model("UserGroup");
		$userGroup = $userGroupModel->getGroup($user_id);
		if(!empty($userGroup))
			$this->assign("group_id",$userGroup->group_id);

		$userInfo = $userModel->getOne($user_id);
		$level_id = $userInfo['level_id'];
		$levelInfo = $levelModel->getOne($level_id);


		$positionModel = model("Position");
		$positionRs = $positionModel->getAll();
		$userPosition = $positionModel->getOneByUserId($userInfo->user_id);
		$userInfo->position_id = $userPosition['position_id'];


		$this->assign("user",$userInfo);
		$this->assign("groups",$groups);
		$this->assign("positions",$positionRs);

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

	public function delGroup(){
		$group_id = input("group_id");
		$model = model("group");
		$model->delGroup($group_id);
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
		$priObj = new pri();
		$model = model("group");
		$rs = $model->getAll();
		foreach($rs as $k => $v){
			$priObj->update($v->group_id);
		}
		$this->redirect("/index/manager/group");

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




	private function build($n,$cates,$layer = 0,$selected){
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

	public function updatePri(){
		$priObj = new pri();
		$priObj->updatePri();
		$this->success("修改成功","/index/manager/group");
	}





	public function installs(){
		$configs = Config::get("pri");
		$temp = [];
		foreach($configs as $k => $v){
			if(!in_array($v['c'],$temp))
				$temp[] = $v['c'];
		}
		$modules = $temp;



		$model = model("pri");
		$actModules = $model->getModules();
		$temp = [];
		foreach($actModules as $k => $v){
			if(!in_array(strtolower($v->controller_name),$temp))
				$temp[] = strtolower($v->controller_name);
		}

		$this->assign("list",$modules);
		$this->assign("active",$temp);
		return $this->fetch("installs");
	}

	public function unstall(){
		$name = input("name");
		$model = model("Pri");
		#$model->delByModule($name);
		#$this->success("卸载成功");
		$this->error("严禁删除");
	}

	public function active(){
		$name = input("name");
		$model = model("Pri");
		$configs = Config::get("pri");
		$obj = [];
		foreach($configs as $k => $v){
			if($v['c'] == trim($name))
				$obj = $v;
		}
		$model->addModule($obj);
		$this->success("安装成功");
	}

}
