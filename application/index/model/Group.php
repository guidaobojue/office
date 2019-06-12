<?php
namespace app\index\model;
use think\Model;
class group extends Model
{
	protected $table = 'vp_group';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}



	public function getOneByUserId($user_id){
		return  $this->query("select * from vp_user_group where user_id = '$user_id'");

	}


	public function getAll(){
		return $this->select();
	}

	public function getAllUserGroups(){
		$temp = [];
		$userGroups = $this->table("vp_user_group")->select();
		$groups = $this->select();
		$groupRs = [];

		foreach($groups as $k => $v){
			$groupRs[$v->group_id] = $v->group_name;
		}


		foreach($userGroups as $k => $v){
			if(isset($groupRs[$v->group_id])){
				$temp[$v->user_id][] = ['group_name' =>$groupRs[$v->group_id],'group_id'=>$v->group_id];
			}
		}
		return $temp;

	}


	public function getOne($id){
		return $this->where(['group_id'=>$id])->find();
	}


	public function list($pageSize = 10){
		$list = $this->paginate($pageSize);
		return $list;
	}


	public function rename($group_id , $group_name){
		$rs = $this->where("group_name",$group_name)->find();
		if(!$rs){
			return $this->save(["group_name"=>$group_name],['group_id'=>$group_id]);
		}
		else
			return false;
	}

	public function editUserGroup($user_id,$group_id){
		$rs = $this->table("vp_user_group")->where("user_id = '$user_id'")->find();
		if($rs){
			$rs = $this->table("vp_user_group")->update(["user_group_id"=>$rs->user_group_id,'user_id'=>$user_id,"group_id"=>$group_id]);
		}
		else
			$rs = $this->table("vp_user_group")->insert(['user_id'=>$user_id,"group_id"=>$group_id]);
		return $rs;
	}

	public function isExist($name){
		$rs = $this->where(['group_name'=>$name])->find();
		if(empty($rs))
			return true;
		else
			return false;
	}

	public function addGroup($group_name){
		$this->save(['group_name'=>$group_name]);
	}
}


