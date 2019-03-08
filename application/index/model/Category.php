<?php
namespace app\index\model;
use think\Model;
class category extends Model
{
	protected $table = 'vp_category';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}




	public function getAll(){
		$rs = $this->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[] = $v->data;
		}
		return $data;
		
	}



	public function getOne($id){
		return $this->find(['group_id'=>$id]);
	}




}

