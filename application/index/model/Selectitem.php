<?php
namespace app\index\model;
use think\Model;
class Selectitem extends Model
{
	protected $table = 'vp_question_select_item';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}

	public function addItem($select_id,$data){
		$temp = [];
		foreach($data as $k => $v){
			$temp[] = [
				'name' => $v,
				'select_id' => $select_id,
			];
		}
		$this->saveAll($temp);
		return true;
	}

	public function getBySelectId($select_id){
		return $this->where(["select_id"=>$select_id])->select();
	}


}
