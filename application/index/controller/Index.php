<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use app\extra\pri;

class Index extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();




	}

	public function index()
	{

		 // outputs image directly into browser, as PNG stream




		if(!isset($_SESSION['user'])){
			$this->redirect("/index/index/login");
			return true;
		}








		return $this->fetch("index");
	}




	public function login(){
		if(input("?post.uname")){
			$uname = input("post.uname");
			$pwd = input("post.pwd");

			$userModel = model("User");
			$rs = $userModel->login($uname,$pwd);
			if($rs){
				$groupModel = model("group");
				$groupRs = $groupModel->getOneByUserId($rs['user_id']);

				$temp = [];
				if(!empty($groupRs)){
					foreach($groupRs as $k => $v){
						$temp[] = $v['group_id'];
					}
				}

				$rs['group'] = $temp;
				$_SESSION['user'] = $rs;

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
		if(input('?post.uname')){
			$uname = input("post.uname");
			$pwd = input("post.pwd");

			$pwd = md5($pwd);
			$data['uname'] = $uname;
			$data['pwd'] = $pwd;
			M("user")->add($data);
		}
		else{

			$uname = 'matengfei';
			$pwd = 123456;

			$pwd = md5($pwd);
			$data['uname'] = $uname;
			$data['pwd'] = $pwd;
			M("user")->add($data);
			$this->display("register");
		}
	}


	public function logout(){
		unset($_SESSION['user']);
		$this->redirect("/");
	}


}
