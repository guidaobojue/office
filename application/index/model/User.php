<?php
namespace app\index\model;
use think\Model;
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

}
