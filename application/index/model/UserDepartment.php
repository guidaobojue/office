<?php
namespace app\index\model;
use think\Model;
class UserDepartment extends Model
{
	protected $table = 'vp_user_department';

	/*
	 * @param origin 1本地增加
	 * 	 	 2从外网获取 
	 */


	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();

		//TODO:自定义的初始化
	}


	public function list($pageSize = 10){
		$list = $this->paginate($pageSize);
		return $list;
	}

	public function add($user_id,$dep_id,$pos_id){
		$this->save(['user_id'=>$user_id,'department_id'=>$dep_id,"position_id"=>$pos_id]);
		return $this->user_department_id;
	}

	public function isExist($user_id,$department_id){
		$rs = $this->where(['user_id'=>$user_id,'department_id'=>$department_id])->find();
		if(is_null($rs))
			return false;
		else 
			return $rs->data;
	}

}
