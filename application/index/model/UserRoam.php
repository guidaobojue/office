<?php
namespace app\index\model;
use think\Model;
class UserRoam extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function getLists($user_id){
		$rs = $this->where(['user_id'=>$user_id,])->order("create_time desc")->paginate();
		return $rs;
	}

}
