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


	/*--- 级别设置 ---*/
	public function level(){
		$model = model("position");
		$rs = $model->getAll();
		$this->assign("list",$rs);

		return $this->fetch("level_list");
	}




	/*--- 评论管理 start ---*/
	public function detail(){
		$id = input("id");
		$model = model("Comment");
		$rs = $model->getOne($id);
		if(empty($rs)){
			if(isset($rs['thumb']) &&!empty($rs['thumb'])){
				$rs['thumb'] = $rs['qr_thumb'];
			}



			$this->assign("info",$rs);
			return $this->fetch("details");
		}
		else{
			$this->redirect("/index/manager/commentList");
		}
	}


	public function commentList(){
		$this->assign("list_num",8);
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


	/*--- 评论 end ---*/


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

	/*---员工-- start*/

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

	/*---员工-- end*/

	/*---群组-- start*/
	public function group(){

		$this->assign("list_num",14);
		$groupModel = model("group");
		$list = $groupModel->list();
		$page = $list->render();

		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("group_list");
	}

	public function addGroup(){
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

	public function delGroup(){
		$group_id = input("group_id");
		$model = model("group");
		$model->delGroup($group_id);
		echo json_encode(1);

	}






	/*
	 * @desc 菜单设置
	 */
	public function menu(){

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








	/*
	 * index 菜单列表
	 * update 更新菜单缓存
	 * addCategory 增加菜单
	 * editCategory 修改菜单
	 * delCategory 删除菜单
	 *
	 *
	 */

	public function categorys() {
		if(!isset($_SESSION['user'])){
			$this->redirect("/index/index/login");
			return true;
		}

		$model = model("category");
		$list = $model->getAll();
		$this->nodes = $list;

		$tree = $this->build_tree($list);
		$tree = $this->build_tr($tree,0);
		$this->assign("list",$tree);
		return $this->fetch("categorys");
	}

	private function build_tr($data,$i=0){
		$href = "/index/manager";
		++$i;
		if(!empty($data['nodes'])){
			$str = "";
			$str .="<tr>".$this->genTd($i)."<td>".$data['category_name']." <a class='af' href='".$href."/addCategory?pid=".$data['category_id']."' >增加</a>|<a class='af'   href='".$href."/editCategory?cid=".$data['category_id']."'>修改</a>||<a class='af' href='javascript:confirmdel(".$data['category_id'].")'>删除</a></td>".$this->genTd(4-$i)."</tr>";
			foreach($data['nodes'] as $k => $v){
				$str.=$this->build_tr($v,$i);
			}
			return $str;

		}
		else{
			return "<tr>".$this->genTd($i)."<td>".$data['category_name']." <a class='af' href='".$href."/addCategory?pid=".$data['category_id']."'>增加</a>|<a class='af'  href='".$href."/editCategory?cid=".$data['category_id']."'>修改</a>||<a class='af' href='javascript:confirmdel(".$data['category_id'].")'>删除</a></td>".$this->genTd(3-$i)."</tr>";
		}

	}



	private function genTd($i){
		$str = "";
		for($j=1;$j<$i;$j++){
			$str .= "<td></td>";
		}
		return $str;
	}


	private function build_tree($data){
		$root = $data[1];
		$childs = json_decode($root['childs'],true);
		foreach($childs as $k => $v){
			$root['nodes'][] = $this->build_node($v);
		}
		return $root;
	}

	private function build_node($id){
		$obj = $this->nodes[$id];
		if(!empty($obj['childs'])){
			$childs = json_decode($obj['childs'],true);
			foreach($childs as $K => $v){
				$obj['nodes'][] = $this->build_node($v);
			}
		}
		return $obj;
	}




	public function addCategory(){
		if(isset($_POST['sub'])){
			$pid = input("pid");
			$category_name = input("category_name");
			$url = input("url");

			$model = model("category");

			$model->addCategory([
				'pid' => $pid,
				'category_name' => $category_name,
				'url' => $url,
			]);

			$this->redirect("/index/manager/categorys");


		}
		else{
			$pid = input("get.pid");
			$this->assign("pid",$pid);
			$this->assign("icons",Config::get("icons"));
			$model = model("category");
			$list = $model->getAll();
			$this->assign("category_name",$list[$pid]['category_name']);
			$this->assign("list",$list);
			return $this->fetch("addCategory");
		}
	}


	public function init(){
		exit;
		$config = Config::get("initConfig");
		$data[] = [
			'category_id' => 1,
			'category_name' => '根',
			'url' => '',
			'icon' => 'fa fa-files-o',
			'parent_id' => 0,
			'childs' => [2,5,8,11,14,19],
		];




		foreach($config as $k => $v){
			$v['category_id'] = $v['id']+1;
			$v['category_name'] = $v['name'];
			$v['parent_id'] = $v['parent']+1;
			unset($v['id']);
			unset($v['name']);
			unset($v['parent']);
			foreach($v['childs'] as $ik => &$iv){
				$iv +=1;
			}



			$v['childs'] = json_encode($v['childs']);

			$data[] = $v;
		}

		$model = model("category");
		$model->saveAll($data);
		var_dump($data);
		exit;
	}


	public function editCategory(){
		if(isset($_POST['sub'])){
			$cid = input("post.cid");
			$category_name = input("post.category_name");
			$url = input("post.url");
			$data = [
				'url' => $url,
				'category_name' => $category_name,
			];
			model("category")->update($data,['category_id'=>$cid]);
			$this->redirect("/index/manager/categorys");

		}
		else{
			$cid = input("get.cid");
			$model = model("category");
			$node = $model->getOneByCid($cid);
			$this->assign("cat",$node);
			return $this->fetch("editCategory");
		}
	}

	public function delCategory(){
		$model = model("category");
		$cid = input("cid");
		$model->delCategory($cid);
		return $this->redirect("/index/manager/categorys");
	}


	private function updateCategoryCache(){
		$obj = new \app\extra\pri;

		$groupModel= model("group");
		$groups =  $groupModel->getAll();
		foreach($groups as $k => $v){
			$obj->update($v->group_id);
		}

		$model = model("category");
		$rs = $model->getAll();
		cache_set('list',$rs);
	}



	/*---模块管理 start---*/
	public function modules(){
		$configs = config::get("modules");

		$model = model("modules");
		$modules = $model->getAll();
		$status = [];
		foreach($modules as $k => $v){
			$status[$v['filename']] = $v['status'];
		}

		$temp = [];
		foreach($configs as $k => $v){
			if(isset($status[$v['f']])){
				$v['status'] = $status[$v['f']];
			}
			else{
				$v['status'] = 0 ;
			}
			$temp[] = $v;

		}
		$modules = $temp;
		$this->assign("list",$modules);
		return $this->fetch("modules_list");
	}


	
	public function editGroupPri(){
		$model = model("pri");
		$configs = $model->getAll();
		$group_id = input("group_id");
		if(!is_numeric($group_id))
			return false;



		if(input("pids")){
			$pids = input("pids");
			if(!empty($pids)&& $pids!=","){
				$pidArr = explode(",",$pids);
				$pidArr = array_slice($pidArr,0,count($pidArr)-1);
				$model->editGroupPri($group_id,$pidArr);
			}
		}
		$temp = [];
		$has = [];
		$list = [];

		$hasRs = $model->getByGroupId($group_id);
		foreach($hasRs as $k => $v){
			$has[] = $v->pri_id;
		}



		foreach($configs as $k => $v){
			$d = $v->getData();
			$cur = $k.":".$d['action'];
			$temp = explode(":",$d['action']);
			$d['module'] = $temp[0];
			$d['name'] = $temp[1];
			if(in_array($d['pri_id'],$has)){
				$d['selected'] = true;
			}
			else{
				$d['selected'] = false;

			}
			$list[$temp[0]][] = [
				'desc' => $d['comment'],
				'name' => $temp[1],
				'selected' => $d['selected'],
				'pri_id' => $d['pri_id'],
			];
		}

		$temp = [];
		foreach($has as $k=> $v){
			$temp[] = $v."";
		}

		$this->assign("has",json_encode($temp));
		$this->assign("group_id",$group_id);
		$this->assign("list",$list);
		$this->updateGroupPriCache();
		return $this->fetch("group_pri");
	}
	public function updateCache(){
		$this->updateCategoryCache();
		$this->updateGroupPriCache();
		return $this->redirect("/index/manager/group");
	}

	private function updateGroupPriCache(){
		$model = model("pri");
		$groupPris= $model->getAllGroupPri();
		$pris = $model->getAll();

		$temp = [];
		foreach($pris as $k => $v){
			$temp[$v->pri_id] = $v->action;
		}

		$pris = $temp;
		

		$data = [];
		foreach($groupPris as $k => $v){
			if(isset($pris[$v['pri_id']]))
				$data[$v['group_id']][] = $pris[$v['pri_id']];
			else
				$data[$v['group_id']][] = "";
		}

		cache_set("groupPri",$data);
	}


	public function free(){
		$model = model("pri");
		$configs = $model->getAll();
		$group_id = 0; //游客

		if(input("pids")){
			$pids = input("pids");
			if(!empty($pids)&& $pids!=","){
				$pidArr = explode(",",$pids);
				$pidArr = array_slice($pidArr,0,count($pidArr)-1);
				$model->editGroupPri($group_id,$pidArr);
			}
		}
		$temp = [];
		$has = [];
		$list = [];

		$hasRs = $model->getByGroupId($group_id);
		foreach($hasRs as $k => $v){
			$has[] = $v->pri_id;
		}



		foreach($configs as $k => $v){
			$d = $v->getData();
			$cur = $k.":".$d['action'];
			$temp = explode(":",$d['action']);
			$d['module'] = $temp[0];
			$d['name'] = $temp[1];
			if(in_array($d['pri_id'],$has)){
				$d['selected'] = true;
			}
			else{
				$d['selected'] = false;

			}
			$list[$temp[0]][] = [
				'desc' => $d['comment'],
				'name' => $temp[1],
				'selected' => $d['selected'],
				'pri_id' => $d['pri_id'],
			];
		}

		$temp = [];
		foreach($has as $k=> $v){
			$temp[] = $v."";
		}

		$this->assign("has",json_encode($temp));
		$this->assign("group_id",$group_id);
		$this->assign("list",$list);
		$this->updateGroupPriCache();
		return $this->fetch("free");


	}
	


	public function pris(){
		$model = model("pri");
		$configs = $model->getAll();
		foreach($configs as $k => $v){
			$d = $v->getData();
			$cur = $k.":".$d['action'];
			$temp = explode(":",$d['action']);
			$d['module'] = $temp[0];
			$d['name'] = $temp[1];
			$list[$temp[0]][] = [
				'desc' => $d['comment'],
				'name' => $temp[1],
				'pri_id' => $d['pri_id'],
			];
		}

		$this->assign("list",$list);
		return $this->fetch("pris");
	}

	public function addPri(){
		$model = model("pri");
		if(input("sub")){
			$action = input("action");
			$desc = input("comment");
			if(empty($action) || empty($desc)){
				$this->error("参数不可为空");
			}

			if(strpos($action,":") == false)
				$this->error("参数错误");



			$rs = $model->addPri($action,$desc);
			if(!$rs){
				$this->error("己存在");
			}
			else
				$this->redirect("/index/manager/pris");
		}
		return $this->fetch("addPri");
	}

	public function delPri(){
	}

	public function editPri(){
		$model = model("pri");
		$pid = input("pid");
		$this->assign("pid",$pid);
		$pidInfo = $model->getByPid($pid);
		if(input("sub")){
			$action = input("action");
			$desc = input("comment");
			if(empty($action) || empty($desc)){
				$this->error("参数不可为空");
			}
			if(strpos($action,":") === false)
				$this->error("参数错误");



			$rs = $model->editPri($pid,$action,$desc);
			if(!$rs){
				$this->error("未修改");
			}
			else
				$this->redirect("/index/manager/pris");
		}
		$this->assign("pidInfo",$pidInfo);
		return $this->fetch("editPri");


	}


}
