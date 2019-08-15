<?php
namespace app\index\model;
use think\Model;
class UserGroup extends Model
{
	protected $table = 'vp_user_group';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}



	public function addUserGroup($user_id,$group_id){
		return $this->save(['user_id'=>$user_id,'group_id'=>$group_id]);
	}

	public function getGroup($user_id){
		return $this->where(['user_id'=>$user_id])->find();
	}

}
