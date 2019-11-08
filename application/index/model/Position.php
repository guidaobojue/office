<?php
namespace app\index\model;
use think\Model;
class Position extends Model
{
	protected $table = 'vp_position';

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

	public function add($position_name){
		$rs = $this->where("position_name='$position_name'")->find();
		if(is_null($rs)){
			$this->save(['position_name'=>$position_name]);
			return $this->position_id;
		}
		else 
			return false;
	}

	public function getOneByUserId($user_id){
		$rs = $this->query("select a.*,b.* from vp_user_department a left join vp_position b on a.position_id =b.position_id where a.user_id = $user_id");
		if(!empty($rs))
			return current($rs);
	}

	public function getAll(){
		$rs = $this->order("level asc")->select();
		return $rs;
	}

	public function updateUser($user_id,$position_id){
		$rs = $this->table("vp_user_department")->where(['user_id'=>$user_id])->find();
		if($rs){
			$this->table("vp_user_department")->where(['user_id'=>$user_id])->update(['position_id'=>$position_id]);
			return true;
		}
		else 
			return false;
	}

	public function getOne($position_id){
		return $this->where(['position_id'=>$position_id])->find();
	}

	public function updatePos($position_id,$level,$position_name){
		return $this->where(['position_id'=>$position_id])->update(['level'=>$level,"position_name"=>$position_name]);
	}


}

