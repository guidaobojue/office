<?php
namespace app\index\model;
use think\Model;
class Answer extends Model
{
	protected $table = 'vp_question_answer';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function addAnswer($data){
		return $this->save($data);

	}
	public function getAllByQid($qid){
		$data = $this->where(["table_id"=>$qid])->select();
		$temp = [];
		$cur = [];
		foreach($data as $k => $v){
			$cur = $v->data;
			$cur['content'] = json_decode($cur['content'],true);
			$temp[] = $cur;
		}
		return $temp;
	}
}
