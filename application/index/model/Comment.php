<?php
namespace app\index\model;
use think\Model;
class comment extends Model
{
	protected $table = 'vp_comments';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}



	public function getOne($id){
		return $this->find(['comment_id'=>$id]);
	}


	public function list($pageSize = 10){
		$list = $this->order("comment_id desc")->paginate($pageSize);
		return $list;
	}

	public function getList($start_time,$end_time){
		$rs = $this->where("$start_time <= create_time and $end_time >= create_time")->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[] = $v->data;
		}
		return $data;
	}



	public function read($id){
		return 	$this->save(['status'=>1],['comment_id'=>$id]);
	}


	public function add($data){
		return 	$this->save($data);
	}

	


}

