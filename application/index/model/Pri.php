<?php
namespace app\index\model;
use think\Model;
class Pri extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}

	public function getAll(){
		return $this->select();
	}

	public function getByGroupId($group_id){
		return $this->table("vp_group_pri")->where(['group_id'=>$group_id])->select();
	}

	public function savePris($list){
		$this->saveAll($list);
	}


	public function editGroupPri($gid,$pids){
		$rs = $this->table("vp_group_pri")->where(["group_id"=>$gid])->select();
		$temp = [];
		$list = [];
		foreach($rs as $k => $v){
			$list[] = $v->pri_id;
			if(!in_array($v->pri_id,$pids)){
				$this->table("vp_group_pri")->where(['group_id'=>$gid,"pri_id"=>$v->pri_id])->delete();
			}
		}
		
		foreach($pids as $k => $v){
			if(!in_array($v,$list))
				$this->table("vp_group_pri")->insert(['group_id'=>$gid,"pri_id"=>$v]);
		}
		return true;
	}

	public function getAllGroupPri(){
		return $this->table("vp_group_pri")->select();
	}

	public function addPri($action,$desc){
		$rs = $this->where(['action'=>$action])->find();
		if(empty($rs)){
			$this->insert(['action'=>$action,"comment"=>$desc]);
			return true;
		}
		else
			return false;
		
	}

	public function getByPid($pid){
		return $this->where(['pri_id'=>$pid])->find();

	}

	public function editPri($pid,$action,$comment){
		$rs = $this->where(['pri_id'=>$pid])->find();
		if(empty($rs))
			return false;

		$rs = $this->where(['pri_id'=>$pid])->update(['action'=>$action,"comment"=>$comment]);
		return $rs;
	}

}
