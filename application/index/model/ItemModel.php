<?php
namespace app\index\model;
use think\Model;
class ItemModel extends Model
{

	protected $table = 'vp_model';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function addModel($data){
		$temp = $data;
		unset($temp['num']);
		$rs = $this->where($temp)->find();
		if(is_null($rs)){
			$this->save($data);
			return $this->model_id;
		}
		else
			return $rs->data['model_id'];

	}

	public function getAll(){
		$rs =  $this->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[$v->data['model_id']] = $v->data;
		}
		return $data;
	}


}
