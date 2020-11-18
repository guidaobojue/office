<?php
namespace app\index\model;
use think\Model;
class Temp extends Model
{
	protected $table = 'vp_temp';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}

	public function getAll(){
		$rs = $this->select();
		$temp = [];
		foreach($rs as $k => $v){
			$temp[] = $v->data;
		}
		return $temp;
	}

	public function getBackupAll(){
		$rs = $this->table("vp_temp_backup")->select();
		$temp = [];
		foreach($rs as $k => $v){
			$temp[$v->backup_id] = $v->data;
		}
		return $temp;
	}


	public function addFrag($name){
		$this->save(['name'=>$name]);
		return $this->frag_id;
	}

	public function saveFrag($frag_id,$content,$code){
	}

	public function adds($data){
		$this->saveAll($data);
	}

	public function getOne($company_name){
		$data = $this->where("company_name like '%$company_name%'")->find();
		if(!empty($data)){
			return $data->data;
		}
		else{
			return false;
		}
	}
	public function checkE($company_name,$user_name){
		$rs = $this->where(['company_name'=>$company_name,'user_name'=>$user_name])->find();
		if(!empty($rs))
			return true;
		else 
			return false;
	}

	public function addNew($data){
		$this->save($data);
		return $this->backup_id;
	}
	public function up($id,$data){
		$this->where("backup_id = '$id'")->update($data);
	}

}

