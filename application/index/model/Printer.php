<?php
namespace app\index\model;
use think\Model;
class Printer extends Model
{
	protected $table = 'vp_machine';
	//自定义初始化
	protected function initialize()
	{

		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function del($machine_id){
		return $this->where("machine_id = '$machine_id'")->delete();
	}


	public function getAll(){
		return $this->table("vp_machine")->select();
	}

	public function list($pageSize = 10){
		$list = $this->table("vp_machine")->paginate($pageSize);
		return $list;
	}

	public function checkModel($data){
		$return = [];
		foreach($data as $k => $v){
			if(isset($comment[$k])){
				$iv = $comment[$k];
			}
			else
				$iv = '';
			$rs = $this->table("vp_materiel_model")->where(['model_name'=>$v])->find();
			if(empty($rs)){
				$return[$v] = $this->table("vp_materiel_model")->insert(['model_name'=>$v,"total"=>0,'comment'=>$iv]);
			}
			else{
				$obj = $this->table("vp_materiel_model")->where(['model_name'=>$v])->field("model_id")->find();
				$return[$v] = $obj->model_id;
			}
		}
		return $return;
	}

	public function consume($data){
		$rs = $this->table("vp_materiel_model")->where(['model_id'=>$data['model_id']])->find();
		$total = $rs->total;
		if($total - $data['num'] < 0 )
			return false;
		$rs = $this->table("vp_materiel_table")->insert($data);
		$this->table("vp_materiel_model")->where(['model_id'=>$data['model_id']])->update(['total'=>$total-$data['num']]);
		return true;

	}
	/*
	 * @desc 增加打印机
	 */
	public function addItems($data){
		foreach($data as $k => $v){
			$rs = $this->table("vp_machine")->where(['machine_name '=>$v])->find();
			if(empty($rs)){
				$rs = $this->table("vp_machine")->insert(['machine_name'=>$v]);
			}
		}
		return true;
	}

	public function getStat($model_id,$pageSize = 15){
		return  $this->table("vp_materiel_table")->where("model_id = '$model_id'")->paginate($pageSize);

	}
}
