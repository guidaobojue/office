<?php
namespace app\index\model;
use think\Model;
class Grouppri extends Model
{
	protected $table = 'vp_group_pri';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function getSelectedPri($group_id){
		return $this->where(['group_id'=>$group_id])->select();
	}

	public function delByGroupId($group_id){
		return $this->where(['group_id'=>$group_id])->delete();
	}

	public function addUserGroup($group_id,$selected){
		$data = [];
		foreach($selected as $k => $v){
			$data[] = [
				'group_id' => $group_id,
				'pri_id' => $v,
			];
		}
		return $this->saveAll($data);

	}

	public function getAllInfo(){
		$rs = $this->table("vp_group_pri a")->join("vp_pri b","a.pri_id = b.pri_id")->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[] = $v->data;
		}
		return $data;
	}

}
