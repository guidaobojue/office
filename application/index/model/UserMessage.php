<?php
namespace app\index\model;
use think\Model;
class UserMessage extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}



	public function getOne($user_id){
	}

	public function list($user_id){
		return $this->where(['user_id'=>$user_id])->order("create_time")->paginate();

	}

	public function read($message_id){
		return $this->where(['message_id'=>$message_id])->update(['status'=>1]);
	}

	public function hasNew($user_id){
		$rs = $this->where(['user_id'=>$user_id,'status'=>0])->find();
		if(!empty($rs))
			return true;
		else
			return false;
	}
}

