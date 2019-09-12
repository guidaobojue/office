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

	public function getCAll(){
		$time = time() + 24 * 3600;
		$rs = $this->where("due_time >= $time")->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[] = $v->data;
		}
		return $data;
	}

	public function list($pageSize = 10,$where = null){
		if(!is_null($where) && !empty($where)){
			$sql = "job_name like '%$where%'";
			$list = $this->where($sql)->paginate($pageSize,false,['query'=>['search'=>$where]]);
		}
		else{
			$list = $this->order("zj_job_id desc")->paginate($pageSize);
		}
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
		$data['zj_job_id']=$job_id;
		return  $this->destroy($data);
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



	/*
	public function init_job(){
		$data = [
			[
				'job_name' => '科员',
				'level' => 1,
			],
			[
				'job_name' => '副科长',
				'level' => 2,
			],

			[
				'job_name' => '科长',
				'level' => 3,
			],
			[
				'job_name' => '副主任',
				'level' => 4,
			],
			[
				'job_name' => '主任',
				'level' => 5,
			],
		];
		$this->saveAll($data);
	}
	 */

	public function changeShow($job_id){
		$rs = $this->where(['zj_job_id'=>$job_id])->find();
		if(!empty($rs)){
			$isShow = $rs->data['is_show'];
			$isShow = !$isShow;
			return $this->where(['zj_job_id'=>$job_id])->update(['is_show'=>$isShow]);
		}
		else
			return false;

	}

	public function addTime($zj_job_id,$time){
		$rs = $this->where(['zj_job_id'=>$zj_job_id])->find();
		if(!empty($rs)){
			$due_time = $rs['due_time'];
			$due_time += $time;
			$rs = $this->where(['zj_job_id'=>$zj_job_id])->update(['due_time'=>$due_time]);
		}
	}


}
