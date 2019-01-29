<?php
namespace app\index\model;
use think\Model;
class level extends Model
{
	protected $table = 'vp_level';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function getAll(){
		return $this->select();
	}



	public function getOne($id){
		return $this->find(['job_id'=>$id]);
	}


	public function list($pageSize = 10){
		$list = $this->paginate($pageSize);
		return $list;
	}

	public function addJob($data){
		return $this->save($data);
	}

	public function editJob($id,$data){
		return $this->save($data,['job_id'=>$id]);
	}

	public function delCompanyJob($company_id){
		//return  $this->destroy($data);
	}


	public function delJob($job_id){
	}

}

