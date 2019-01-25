<?php
namespace app\index\model;
use think\Model;
class job extends Model
{
	protected $table = 'vp_zj_job';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}



	public function getOne($id){
		return $this->find(['zj_job_id'=>$id]);
	}


	public function list($pageSize = 10){
		$list = $this->paginate($pageSize);
		return $list;
	}

	public function addJob($data){
		return $this->save($data);
	}

	public function editJob($id,$data){
		return $this->save($data,['zj_job_id'=>$id]);
	}

	public function delCompanyJob($company_id){
		$data["zj_company_id"] = $company_id;
		return  $this->destroy($data);
	}


	public function delJob($job_id){
	}


	public function search(){
	}

	public function companyJobList($zj_company_id,$pageSize=10){
		return $this->where(['zj_company_id'=>$zj_company_id])->paginate($pageSize);

	}


	public function getList($start_time,$end_time){
		$rs = $this->where("origin = 1 and $start_time <= create_time and $end_time >= create_time")->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[] = $v->data;
		}
		return $data;

	}

	public function addJobs($data){
		return $this->saveAll($data);
	}


}
