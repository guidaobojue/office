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


}

