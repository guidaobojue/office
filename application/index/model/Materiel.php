<?php
namespace app\index\model;
use think\Model;
class Materiel extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}

	public function getByMachineId($machine_id){
		return $this->table("vp_materiel_model")->where(['machine_id'=>$machine_id])->select();
	}


	public function getAll(){
		return $this->table("vp_materiel_model")->select();
	}

	public function list($pageSize = 10){
		$list = $this->table("vp_materiel_model")->paginate($pageSize);
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
			$rs = $this->table("vp_materiel_model")->where(['model_name'=>$v['model_name']])->find();
			if(empty($rs)){
				$return[$v['model_name']] = $this->table("vp_materiel_model")->insert(['model_name'=>$v['model_name'],"total"=>0,'machine_id'=>$v['machine_id']]);
			}
			else{
				$obj = $this->table("vp_materiel_model")->where(['model_name'=>$v['model_name']])->field("materiel_model_id")->find();
				$return[$v['model_name']] = $obj->materiel_model_id;
			}
		}
		return $return;
	}

	public function consume($model_id,$user_id,$num){
		$rs = $this->table("vp_materiel_model")->where(['model_id'=>$model_id])->find();
		$machine_id = $rs->machine_id;
		$total = $rs->total;
		if($total - $num < 0 )
			return false;
		$data = [
			'materiel_model_id' => $model_id,
			'user_id' => $user_id,
			'num' => $num,
			'time' => time(),
		];
		$rs = $this->table("vp_materiel_table")->insert($data);
		$this->table("vp_materiel_model")->where(['model_id'=>$model_id])->update(['total'=>$total-$data['num']]);


		$machineRs = $this->table("vp_machine")->where(['machine_id'=>$machine_id])->find();
		$total = $machineRs->total;

		$this->table("vp_machine")->where(['machine_id'=>$machine_id])->update(['total'=>$total-$num]);
		return true;

	}
	/*
	 * @desc 进货
	 */
	public function addItems($data){
		foreach($data as $k => $v){
			$rs = $this->table("vp_materiel_model")->where(['materiel_model_id'=>$v['id']])->find();
			if(!empty($rs)){
				$total = $rs['total'];
				$this->table("vp_materiel_model")->where(['materiel_model_id'=>$v['id']])->update(['total' =>$total + $v['num']]);
				$this->addMachineTotal($rs['machine_id'],$total);
			}
		}
		return true;
	}

	private function addMachineTotal($machine_id,$num){
		$rs = $this->table("vp_machine")->where(['machine_id'=>$machine_id])->find();
		if(is_null($rs))
			return false;
		$total = (int)$rs['total'] + $num;
		return $this->table("vp_machine")->where(['machine_id'=>$machine_id])->update(['total'=>$total]);
	}



	public function getStat($model_id,$pageSize = 15){
		return  $this->table("vp_materiel_table")->where("model_id = '$model_id'")->paginate($pageSize);

	}
}
