<?php
namespace app\index\model;
use think\Model;
class company extends Model
{
	protected $table = 'vp_zj_company';

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


	public function list($pageSize = 10,$where = null){
		if(is_null($where) && empty($where)){
			$list = $this->order("release_date desc")->paginate($pageSize);
		}
		else{
			$sql =  "company_id like '%$where%' or company_name like '%$where%'";
			$list = $this->where($sql)->order("release_date desc")->paginate($pageSize,false,['query'=>['search'=>$where]]);
		}
		return $list;
	}


	public function getOne($company_id){
		$rs  = $this->find(['zj_company_id'=>$company_id]);
		return $rs;
	}


	public function addCompany($data){
		$rs = $this->save($data);
		return $this->zj_company_id;
	}

	public function hasCompanyId($company_id){
		$rs = $this->where(['company_id'=>$company_id])->find();
		if(empty($rs))
			return false;
		else 
			return true;
	}

	public function editCompany($id,$data){
		return $this->save($data,['zj_company_id'=>$id]);

	}

	public function getAll(){
		$rs = $this->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[] = $v->data;
		}
		return $data;
	}


	public function delCompany($id){
		$this->destroy($id);
		return true;
	}

	public function getAllVK(){
		$rs = $this->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[$v->data['company_name']] = $v->data['zj_company_id'];
		}
		return $data;
	}
}
