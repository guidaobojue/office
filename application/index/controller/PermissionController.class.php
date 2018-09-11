<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Per;
class PermissionController extends Controller {
	private $ps = null;

	public function __construct(){
		parent::__construct();
		$this->ps = M("permission")->select();
	}


	public function index(){
		A("Permission")->check();
		$this->lists= $this->ps;
		$this->display("index");
	}



	public function check(){
		if(!isset($_SESSION['user']))
			$this->redirect("index/index");


		$uid = $_SESSION['user']['id'];
		$rs = $this->hasPis($uid);

		if(!$rs && $this->hasRule())
			$this->redirect("index/index");
	}



	public function hasRule(){
		$action = strtolower(__ACTION__);
		$ps = $this->ps;
		$data = [];
		$rs = false;
		foreach($ps as $k => $v){
			$url = strtolower($v['modules'])  .'/'. strtolower($v['action']);
			if(strpos($action,$url)!== false){
				$rs = true;
			}
		}
		return $rs;


	}


	public function hasPis($uid){
		$user = M("manager")->where("mid = '$uid'")->find();


		$gid = $user['gid'];
		if(empty($gid))
			return false;

		$group = M("group")->where("gid = '$gid'")->find();

		$ps = $this->ps;

		$pids = explode(",",$group['pids']);


		$has = [];
		foreach($ps as $k => $v){
			if(in_array($v['pid'],$pids)){
				$has[] = $v['modules'] .'/' . $v['action'];
			}
		}


		$action = strtolower(__ACTION__);
		foreach($has as $k => $v){
			if(strpos($action,strtolower($v))!==false){
				return true;
			}
		}

		return false;
	}



	public function getPis($uid =null ){
		if(is_null($uid))
			$uid = $_SESSION['user']['id'];
		$user = M("manager")->where("mid = '$uid'")->find();


		$gid = $user['gid'];
		if(empty($gid))
			return false;

		$group = M("group")->where("gid = '$gid'")->find();

		$ps = M("permission")->select();

		$pids = explode(",",$group['pids']);

		return $pids;

	}




	public function add(){
		A("Permission")->check();
		if(I("sub")){
			$name = I("name");
			$modules = I("modules");
			$action = I("action");

			$data['name'] = $name;
			$data['modules'] = $modules;
			$data['action'] = $action;
			M("permission")->add($data);
			$this->ok = 1;
		}

		$this->display("add");
	}

	public function edit(){
		A("Permission")->check();
		$pid = I("get.pid");
		if(I("sub")){
			$name = I("name");
			$modules = I("modules");
			$action = I("action");
			$data['name'] = $name;
			$data['modules'] = $modules;
			$data['action'] = $action;
			M("permission")->where("pid = '$pid'")->save($data);

			$this->redirect("Permission/index");
		}
		else{
			if(empty($pid))
				return false;
			$this->pid = $pid;
			$this->data = M("permission")->where("pid = '$pid'")->find();
			$this->display("edit");
		}
	}

	public function del(){
		A("Permission")->check();
		$pid = I("pid");
		M("permission")->where("pid='$pid'")->del();
		echo 1;
	}



}
