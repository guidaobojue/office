<?php
namespace app\index\model;
use think\Model;
class GroupCategory extends Model
{
	protected $table = 'vp_group_category';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function getAllByGid($group_id){
		$rs = $this->where(['group_id'=>$group_id])->select();
		$temp = [];
		foreach($rs as $k => $v){
			$temp[] = $v->data;
		}
		return $temp;
	}

	public function add($group_id,$category_ids){
		$this->where(['group_id'=>$group_id])->delete();
		$data = [];
		foreach($category_ids as $k => $v){
			$data[] = [
				'category_id' => $v,
				'group_id' => $group_id,
			];
		}
		$rs = $this->saveAll($data);
		return $rs;

		
	}



}
