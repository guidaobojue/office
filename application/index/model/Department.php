<?php
namespace app\index\model;
use think\Model;
class department extends Model
{
	protected $table = 'vp_department';

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


	public function list($pageSize = 10){
		$list = $this->paginate($pageSize);
		return $list;
	}

	public function add($dep_name){
		$rs = $this->find(["dep_name"=>$dep_name]);
		if(is_null($rs)){
			$this->save(['dep_name'=>$dep_name]);
			return $this->department_id;
		}
		else 
			return false;
	}



	public function getOne($company_id){
		$rs  = $this->find(['department_id'=>$company_id]);
		return $rs;
	}

}

