<?php
namespace app\index\model;
use think\Model;
class Question extends Model
{
	protected $table = 'vp_question_table';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function addQuestion($title){
		$data = [
			'name' => $title,
			'create_time' => time(),
			'status' => 0,
		];
		$this->save($data);
		return $this->question_table_id;
	}

	public function list($n = 10){
		return $this->order("question_table_id","desc")->paginate($n);
	}

	public function getOne($table_id){
		return $this->where(['question_table_id'=>$table_id])->find();
	}
}
