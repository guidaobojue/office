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
	public function apply($item_id,$user_id,$apply_approval_user_id,$use_user_id,$use_approval_user_id,$office_approval_user_id,$reason){
		$rs = $this->select(['item_id'=>$item_id]);
		foreach($rs as $k => $v){
			if((int)$v['status'] != 4)
				return false;
		}
		$time = time();
		$this->save( [
			'item_id'=>$item_id,
			'apply_user_id'=>$user_id,
			'use_user_id'=>$use_user_id,
			'apply_time'=>$time,
			'apply_approval_user_id' => $apply_approval_user_id,
			'use_approval_user_id' => $use_approval_user_id,
			'office_approval_user_id' =>$office_approval_user_id,
			'status' => 1,
			'reason' => $reason,
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


	public function getOne($roam_id){
		#$rs = $this->join("vp_user_roam b","vp_roam.roam_id = b.roam_id")->where("vp_roam.roam_id='$roam_id'")->find();
		#return $rs->data;
		$rs = $this->where("roam_id = '$roam_id'")->find();
		return $rs->data;
	}



	public function check(){
	}


	public function allow($roam_id){
		$rs = $this->find(['roam_id'=>$roam_id]);
		if(empty($rs))
			return false;

		$orders =  ['use_approval_time','apply_approval_time','office_approval_time'];
		$timeArg = "";
		foreach($orders as $k => $v){
			if(empty($rs->$v)){
				$timeArg = $v;
				break;
			}
		}

		$status= $rs->data['status']+1;
		if(empty($timeArg))
			$this->where(['roam_id'=>$roam_id])->update(['status'=>$status]);
		else
			$this->where(['roam_id'=>$roam_id])->update(['status'=>$status,$timeArg => time()]);
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

	public function getRoamIng($user_id = null){
		if(is_null($user_id))
			$rs = $this->where("status <>5")->select();
		else
			$rs = $this->where("status <>5 and user_id = $user_id")->select();
		return $rs;
	}

	public function checkRoamIng($item_id){
		$rs = $this->where("item_id = '$item_id' and status <> 4")->find();
		if(!$rs)
			return false;
		else 
			return true;

	}

}

