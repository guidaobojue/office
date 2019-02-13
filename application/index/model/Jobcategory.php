<?php
namespace app\index\model;
use think\Model;
class jobcategory extends Model
{
	protected $table = 'vp_zj_job_category';

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


	public function adds($data){
		$temp = [];
		foreach($data  as $k => $v){
			$temp[] = ['name'=>$v];
		}
		return $this->saveAll($temp);
	}



	public function list($pageSize = 10){
		$list = $this->paginate($pageSize);
		return $list;
	}


	public function getOne($job_category_id){
		$rs  = $this->find(['job_category_id'=>$job_category_id]);
		return $rs;
	}


	public function addCompany($data){
		$rs = $this->save($data);
		return $this->job_category_id;


	}

	public function editCompany($id,$data){
		return $this->save($data,['job_category_id'=>$id]);

	}

	public function getAll(){
		$rs = $this->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[$v->data['job_category_id']] = $v->data['name'];
		}
		return $data;
	}


	public function delCompany($id){
		$this->destroy($id);
		return true;
	}
}
