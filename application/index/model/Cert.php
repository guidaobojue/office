<?php
namespace app\index\model;
use think\Model;
class Cert extends Model
{
	protected $table = 'vp_office_cert';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}
	public function chg($cert_id,$data){
		$this->where(['cert_id'=>$cert_id])->update($data);
	}

	public function getAll($search = null){
		$pageSize = 20;
		if(is_null($search)){
			return  $this->paginate($pageSize);
		}
		else{
			return  $this->where("company_name like '%$search%'")->paginate($pageSize);
		}
	}
	public function getAlls(){
		return  $this->select();
	}

	public function check($company_name){
		$rs = $this->where(['company_name'=>$company_name])->find();
		if(!$rs)
			return false;
		else
			return true;
	}


	public function chgNewNum($cert_id,$n){
		$rs = $this->where(['cert_id'=>$cert_id])->find();
		if(!$rs)
			return 0;
		$num = (int)$rs->new_num + $n;
		$rs = $this->where(['cert_id'=>$cert_id])->update(['new_num'=>$num]);
		return $num;
	}

	public function chgHasNum($cert_id,$n){
		$rs = $this->where(['cert_id'=>$cert_id])->find();
		if(!$rs)
			return 0;
		$num = (int)$rs->has_num + $n;
		$rs = $this->where(['cert_id'=>$cert_id])->update(['has_num'=>$num]);
		return $num;
	}

	public function getOne($cert_id){
		$rs = $this->where(['cert_id'=>$cert_id])->find();
		return $rs->data;
	}



	public function add($data){
		$this->save($data);
		return $this->cert_id;
	}
}
	
