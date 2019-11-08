<?php
namespace app\index\model;
use think\Model;
class Select extends Model
{
	protected $table = 'vp_question_select';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function addSelect($table_id,$name,$type,$sort){
		$this->save(["name"=>$name,"table_id"=>$table_id,"type"=>$type,"sort"=>$sort]);
		return $this->select_id;
	}

	public function getById($question_table_id){
		return $this->where(['table_id'=>$question_table_id])->order("sort","asc")->select();
	}



}
