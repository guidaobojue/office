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

	public function isExist($uname){
		$rs = $this->where("uname = '$uname'")->find();
		if(is_null($rs)){
			return false;
		}
		else
			return true;
	}



	public function getRoamUsers($temp){
		$data = [];
		$data['apply_user'] = $this->where(['user_id'=>$temp['apply_user_id']])->find()->data;
		$data['use_user'] = $this->where(['user_id'=>$temp['use_user_id']])->find()->data;
		$data['apply_approval_user'] = $this->where(['user_id'=>$temp['apply_approval_user_id']])->find()->data;
		$data['use_approval_user'] = $this->where(['user_id'=>$temp['use_approval_user_id']])->find()->data;
		$data['use_approval_user'] = $this->where(['user_id'=>$temp['use_approval_user_id']])->find()->data;
		$data['office_approval_user'] = $this->where(['user_id'=>$temp['office_approval_user_id']])->find()->data;
		return $data;
	}


	public function getByUid($uid){
		return $this->where(['user_id'=>$uid])->find();
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

	public function getDetail($uid){
		$rs = $this->find(['user_id'=>$uid]);
		if($rs)
			$data = $rs->data;
		else 
			return false;
		$rs = $this->query("select a.*,b.* from vp_user_department a left join vp_department b on a.department_id = b.department_id where a.user_id ='$uid'");
		if(empty($rs))
			return false;
		else 
			return array_merge($data,current($rs));
	}

	public function list($pageSize = 10){
		$list = $this->paginate($pageSize);
		return $list;
	}

	public function search($search,$pageSize = 10){
		$list = $this->where("uname like '%$search%'")->paginate($pageSize);
		return $list;
	}

	public function searchName($name){
		return $this->where("uname like '%$name%'")->field(['user_id',"uname","nickname"])->find();
	}

	public function getListByDep($dep_id){
		$rs = $this->query("select a.*,b.* from vp_user_department a left join vp_user b on a.user_id=b.user_id where a.department_id = '$dep_id'");
		return $rs;
	}


	public function delUser($user_id){
		$this->destroy(['user_id'=>$user_id]);
		$this->table("vp_user_group")->where("user_id",$user_id)->delete();
	}


	
	public function getUserIdByName($name){

	}

	public function getUserIdByNameDep($user_name,$department){
		$depModel = new Department();
		$rs = $depModel->where("dep_name = '$department'")->find();
		if(is_null($rs)){
			return false;
		}
		else{
			$department_id = $rs->data['department_id'];
		}

		$userRs = $this->where("uname='$user_name'")->find();
		if(is_null($userRs))
			return false;
		else
			$user_id = $userRs->data['user_id'];
		$userDep = new UserDepartment();
		$rs = $userDep->isExist($user_id,$department_id);
		if($rs)
			return $rs['user_id'];
		else
			return false;

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




	public function getTop($user_id){
		$rs = $this->query("select * from vp_user_department where user_id = '$user_id'");
		if(empty($rs))
			return false;
		$rs = current($rs);
		$depId = $rs['department_id'];

		$users = $this->query("select a.*,b.* from vp_user_department a left join vp_user b on a.user_id = b.user_id where a.department_id='$depId'");

		if(empty($users))
			return false;


		$positionModel = model("position");
		$positionRs = $positionModel->getAll();
		$temp = [];
		foreach($positionRs as $k => $v){
			$temp[$v->data['position_id']] = $v->data['level'];
		}

		$level = $temp;

		$top = [];
		foreach($users as $k => $v){
			if(empty($top)){
				$top = $v;
				if(empty($top['position_id'])){
					$top['position_id'] = 1;
				}
			}
			else{
				$top_level = isset($level[$top['position_id']])  ? $level[$top['position_id']] : 1;
				$current_level = isset($level[$v['position_id']]) ? $level[$v['position_id']] : 1;
			}
		}
		return $top;



	}




}
