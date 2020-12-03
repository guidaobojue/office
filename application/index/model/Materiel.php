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

	public function getAllTable($date = null){
		if(!is_null($date)){
			$time = strtotime($date);
			$rs = $this->table("vp_materiel_table")->where("time >= $time")->select();
		}
		else
			$rs = $this->table("vp_materiel_table")->select();
		return $rs;
	}
	public function getTableList($materiel_id,$pageSize=20){
		$rs = $this->table("vp_materiel_table")->where("materiel_model_id = '$materiel_id'")->order("materiel_table_id desc")->paginate($pageSize);
		return $rs;
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

	public function consume($model_id,$user_id,$user_name,$num){
		$rs = $this->table("vp_materiel_model")->where(['materiel_model_id'=>$model_id])->find();
		$machine_id = $rs->machine_id;
		$total = $rs->total;
		if($total - $num < 0 )
			return false;
		$data = [
			'materiel_model_id' => $model_id,
			'user_id' => $user_id,
			'user_name' => $user_name,
			'num' => $num,
			'time' => time(),
		];
		$rs = $this->table("vp_materiel_table")->insert($data);
		$this->table("vp_materiel_model")->where(['materiel_model_id'=>$model_id])->update(['total'=>$total-$data['num']]);


		$machineRs = $this->table("vp_machine")->where(['machine_id'=>$machine_id])->find();
		$total = $machineRs->total;

		$this->table("vp_machine")->where(['machine_id'=>$machine_id])->update(['total'=>$total-$num]);
		return true;

	}
	public function getOne($model_id){
		return $this->table("vp_materiel_model")->where(['materiel_model_id'=> $model_id])->find();
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
				$this->addMachineTotal($rs['machine_id'],$v['num']);
			}
		}
		return true;
	}

	private function addMachineTotal($machine_id,$num){
		$rs = $this->table("vp_machine")->where(['machine_id'=>$machine_id])->find();
		if(is_null($rs))
			return false;
		$total = (int)$rs['total'] + $num;
		$rs = $this->table("vp_machine")->where(['machine_id'=>$machine_id])->update(['total'=>$total]);
		return $rs;
	}

	public function addMaterielRecord($date,$content){
		$rs = $this->table("vp_materiel_record")->where(['date'=>$date])->find();
		if(empty($rs)){
			$data = ['date'=>$date,'content'=>json_encode($content)];
			$rs = $this->table("vp_materiel_record")->data($data)->insert();
		}
	}


	public function getStat($model_id,$pageSize = 15){
		return  $this->table("vp_materiel_table")->where("materiel_model_id = '$model_id'")->paginate($pageSize);

	}

	public function getMaterielRecord(){
		return $this->table("vp_materiel_record")->select();
	}

	public function getRecordByTime($time){
		return $this->table("vp_materiel_table")->where("time >= '$time'")->select();
	}

	public function getLastRecord(){
		$month = strtotime(date("Ym",time())."01");
		$curMonth = date("m",time());
		$curMonth = (int)$curMonth;
		if($curMonth == 12){
			$nextMonth = 1;
			$nextYear = date("Y",time()) + 1;
		}
		else{
			$nextMonth +=1;
			$nextYear = date("Y",time());
		}
		if($nextMonth < 10){
			$nextMonth = "0".(string)$nextMonth;
		}
		$nextTime = $nextYear.$nextMonth;
		$rs = $this->table("vp_materiel_table")->where("time >= '$month' and time < '$nextTime'")->fetchSql(false)->select();
		$date = [];
		foreach($rs as $k => $v){
			$curDate = date("Ym",$v['time']);
			if(!isset($date[$curDate])){
				$date[$curDate] = [];
			}
			if(!isset($date[$curDate][$v['materiel_model_id']]))
				$date[$curDate][$v['materiel_model_id']] = 0;
			$date[$curDate][$v['materiel_model_id']] += $v['num'];
		}
		return current($date);
	}


	public function editMachineTotal($machine_id,$val){
		$machineRs = $this->table("vp_machine")->where(['machine_id'=>$machine_id])->find();
		if(empty($machineRs))
			return false;
		$total = $machineRs->total + $val;
		if($total < 0 )
			return false;
		else
			return $this->table("vp_machine")->where(['machine_id'=>$machine_id])->update(['total'=>$total]);

	}

	public function editMateriel($model_id,$machine_id,$model_name){
		$rs = $this->table("vp_materiel_model")->where(['materiel_model_id'=>$model_id])->find();
		if(!$rs)
			return false;
		$ototal = (int)$rs->total;
		$oldMachineId = $rs->machine_id;
		$rs = $this->editMachineTotal($oldMachineId,-$ototal);
		if(!empty($rs))
			return false;

		$rs = $this->editMachineTotal($machine_id,$ototal);
		if(!empty($rs))
			return false;
		$this->table("vp_materiel_model")->where(['materiel_model_id'=>$model_id])->update(['model_name'=>$model_name,'machine_id'=>$machine_id]);
		return true;


	}

}
