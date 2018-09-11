<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\CardModel;
class ManagerController extends Controller {

	private $error= false;

	public function __construct(){
		parent::__construct();
		A("Permission")->check();
	}

	public function index(){
		$name = I("name","");
		$page = I("p",1);
		if(!empty($name)){
			$maps[] = "mname ='$name'";
		}

		$pageSize = 15;
		$start = ($page - 1)  * $pageSize ;

		$this->lists = M("manager")->where($maps)->limit($start,$pageSize)->select();


		$page = new \Think\Page($this->count, $pageSize);
		$this->assign('page', $page->show());
		$this->display("index");

	}


	public function group(){
		$this->lists = M("group")->select();
		$this->display("group");
	}

	public function addGroup(){
		if(I("sub")){
			$pids = I("p");
			$username = I("username");
			if(empty($username))
				$error['username'] = "不能为空";


			$keys = array_keys($pids);
			$keys = implode(",",$keys);



			$rs = M("group")->where("name='$username'")->find();
			if($rs){
				$error['username'] = "已存在";
			}



			if(empty($error)){

				$data['name'] = $username;
				$data['pids'] = $keys;
				M("group")->add($data);
				$this->ok = 1;
			}





		}
		$this->lists= M("permission")->select();
		$this->display("addGroup");

	}
	public function delGroup(){
		$gid = I("gid");
		M("group")->where("gid = '$gid'")->delete();
		
	}
	public function editGroup(){
		$gid = I("get.gid");
		$this->gid = $gid;



		if(I("sub")){
			$pids = I("p");
			$username = I("username");
			if(empty($username))
				$error['username'] = "不能为空";


			$keys = array_keys($pids);
			$keys = implode(",",$keys);


			$data['name'] = $username;
			$data['pids'] = $keys;





			if($error)
				$this->display("Manager/edit");
			else{
				$rs = M("group")->where("gid='$gid'")->save($data);
				$this->redirect("Manager/group");
			}

			
		}
		else{

			$data = M("group")->where("gid = '$gid'")->find();
			$this->data = $data;
			$pids = $data['pids'];
			$this->pids = explode(",",$pids);



			$this->lists= M("permission")->select();

			$this->display("edit");
		}


	}

	public function changePwd(){
		if(I("sub")){
			$pwd = I("pwd");
			$new_pwd = I("new_pwd");
			$uid = $_SESSION['user']['id'];


			$user = M("manager")->where("mid='$uid'")->find();




			if($user['mpwd'] == md5($pwd)){
				$rs = M("manager")->where("mid='$uid'")->save(['mpwd'=>md5($new_pwd)]);
				$this->redirect("index/index");
			}
			else{
				$error['pwd'] = '密码错误';
				$this->assign("error",$error);
				$this->display("changePwd");
			}



		}
		else{
			$this->display("changePwd");
		}
	}


	public function addUser(){
		if(I("sub")){
			$gid = I("p");
			$name = I("username");
			if(empty($name))
				$error['username'] = '不能为空';




			if(!empty($error)){
				$this->assign("error",$error);
				$this->display("Manager/addUser");
			}
			else{


				$data['gid'] = $gid;
				$data['mname'] = $name;
				$data['mpwd'] = md5('123456');
				M("manager")->add($data);


				$this->redirect("Manager/index");
			}


		}
		else{
			$this->lists= M("group")->select();
			$this->display("addUser");
		}
	}
	public function delUser(){
		$mid = I("mid");
		if(empty($mid))
			return false;
		$rs = M("manager")->where("mid = '$mid'")->delete();
		echo json_encode(1);
	}
	public function editUser(){
		$mid = I("get.mid");
		$this->mid = $mid;
		if(I("sub")){
			$gid = I("p");
			$name = I("username");
			if(empty($name))
				$error['username'] = '不能为空';




			if(!empty($error)){
				$this->assign("error",$error);
				$this->display("Manager/addUser");
			}
			else{


				$data['gid'] = $gid;
				$data['mname'] = $name;


				M("manager")->where("mid ='$mid'")->save($data);


				$this->redirect("Manager/index");
			}


		}
		else{
			$user = M("manager")->where("mid ='$mid'")->find();
			$this->assign("user",$user);
			$this->lists= M("group")->select();
			$this->display("editUser");
		}
	}

}
