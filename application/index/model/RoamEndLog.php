<?php
namespace app\index\model;
use think\Model;
class RoamEndLog extends Model
{
	#protected $table = 'vp_roam';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	/*
	 * status  1.同意 2.拒绝
	 */

	public function record($user_id,$to_user_id,$item_id){
		$time = time();
		$this->save(['user_id'=>$user_id,'to_user_id'=>$to_user_id,'create_time'=>$time,'item_id'=>$item_id]);
		return $this->roam_end_log_id;
	}
}
