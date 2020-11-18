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
		#$rs = $this->table("vp_user_roam a")->join("vp_roam b","a.roam_id=b.roam_id","left")->where("a.user_id = '$user_id'")->select();
		$rs = $this->table("vp_user_roam a")->join("vp_roam b","a.roam_id=b.roam_id","left")->where("a.user_id = '$user_id'")->order("b.apply_time desc")->paginate(10);
		return $rs;

	}

	public function getCheckLists($user_id){
		$rs = $this->table("vp_user_roam a")->join("vp_roam b","a.roam_id=b.roam_id","left")->where("a.user_id = '$user_id' and b.status <> 4")->order("b.apply_time desc")->paginate(10);
		return $rs;
	}

	public function getFinishLists($user_id){
		$rs = $this->table("vp_user_roam a")->join("vp_roam b","a.roam_id=b.roam_id","left")->where("a.user_id = '$user_id' and b.status=4")->order("b.apply_time desc")->paginate(10);
		return $rs;
	}

}
