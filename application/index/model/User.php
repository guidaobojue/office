<?php
namespace app\index\model;
use think\Model;
use app\index\model\Department;
use app\index\model\Position;

class user extends Model
{
	protected $table = 'vp_user';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}

	public function register($data){
		$uname = $data['uname'];
		$rs = $this->where("uname = '$uname'")->find();
		if(is_null($rs)){
			$data['last_time'] = time();
			$this->save($data);
			return $this->user_id;
		}
		else
			return false;

	}


	public function login($uname,$pwd){
		$rs = $this->where("uname = '$uname'")->find();
		if($rs){
			$rsPwd = $rs['pwd'];
			if(md5($pwd) == $rsPwd){
				$data = $rs->data;

				if(empty($data['nickname']))
					$data['nickname'] = $data['uname'];
				unset($data['pwd']);

				return $data;
			}
			else{
				return false;
			}

		}
		else{
			return false;
		}
	}

	public function editInfo($uid,$data){
		$rs = $this->save($data,['user_id'=>$uid]);
		return $rs;
	}
	public function getOne($uid){
		return $this->find(['user_id'=>$uid]);
	}


	public function list($pageSize = 10){
		$list = $this->paginate($pageSize);
		return $list;
	}

	public function delUser($user_id){
		$this->destroy(['user_id'=>$user_id]);
		$this->table("vp_user_group")->where("user_id",$user_id)->delete();
	}


	public function getUserIdByName($name){

	}

	public function getUserIdByNameDep($user_name,$dep){

	}

	public function addUser($user_name,$department,$position){
		$depModel = new Department();
		$rs = $depModel->where("dep_name = '$department'")->find();
		if(is_null($rs)){
			$department_id = $depModel->add($department);
		}
		else{
			$department_id = $rs->data['department_id'];
		}

		$posModel = new Position();
		$rs = $posModel->where("position_name = '$position'")->find();
		if(is_null($rs)){
			$position_id = $posModel->add($position);
		}
		else{
			$position_id = $rs->data['position_id'];
		}

		$userDepModel = new UserDepartment();
		$user_id = $this->register(['uname'=>$user_name,'pwd'=>md5("123456")]);

		$userDepModel->add($user_id,$department_id,$position_id);








	}






}
