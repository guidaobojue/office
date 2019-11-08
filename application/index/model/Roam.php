<?php
namespace app\index\model;
use think\Model;
class Roam extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	/*
	 * status  1:流入部门同意 2:流入部门领导同意 3:流出部门同意  4:办公室审批同意   5:流入部门领导不同意 6:流出部门领导不同意 7:办公室不同意
	 */
	public function apply($item_id,$user_id,$apply_approval_user_id,$use_user_id,$use_approval_user_id,$office_approval_user_id){
		$rs = $this->select(['item_id'=>$item_id]);
		foreach($rs as $k => $v){
			if($v['status'] != 5)
				return false;
		}
		$time = time();
		$this->save( [
			'item_id'=>$item_id,
			'apply_user_id'=>$user_id,
			'use_user_id'=>$use_user_id,
			'create_time'=>$time,
			'status'=>1,
			'apply_approval_user_id' => $apply_approval_user_id,
			'use_approval_user_id' => $use_approval_user_id,
			'office_approval_user_id' =>$office_approval_user_id,
		]);

		$roam_id = $this->roam_id;

		$ids = [$user_id,$apply_approval_user_id,$use_user_id,$use_approval_user_id,$office_approval_user_id];
		$ids = array_unique($ids);
		$time = time();
		foreach($ids as $k => $v){
			$sqls[]= "($v,$roam_id,$time)";

		}
		$sql = implode(",",$sqls);
		$this->query("insert into vp_user_roam (user_id,roam_id,create_time) values $sql");
		return $this->roam_id;


	}

	public function getRoamIng(){
		$rs = $this->where("status <>5 ")->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[$v->data['item_id']] = $v->data;
		}
		return $data;

	}

	public function getOne($roam_id){
		$rs = $this->find(['roam_id'=>$roam_id]);
		return $rs->data;
	}



	public function check(){
	}


	public function allow($roam_id){
		$rs = $this->find(['roam_id'=>$roam_id]);
		if(empty($rs))
			return false;
		$status= $rs->data['status']+1;
		$this->where(['roam_id'=>$roam_id])->update(['status'=>$status]);
	}

	public function deny($roam_id){
		$rs = $this->find(['roam_id'=>$roam_id]);
		if(empty($rs))
			return false;
		$status= $rs->data['status']+3;
		$this->where(['roam_id'=>$roam_id])->update(['status'=>$status]);
	}

	public function getByItemId($item_id){
		$rs = $this->where(['item_id'=>$item_id])->select();
		$temp = [];
		foreach($rs as $k => $v){
			$temp[] = $v->data;
		}
		return $temp;
	}

	public function getEndByItemId($item_id){
		$rs = $this->where(["status"=>4,'item_id'=>$item_id])->select();
		$temp = [];
		foreach($rs as $k => $v){
			$temp[] = $v->data;
		}
		return $temp;
	}
}


