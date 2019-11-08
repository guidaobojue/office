<?php
namespace app\index\model;
use think\Model;
class Supplies extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}

	public function list(){
		return $this->order("supplies_id desc")->paginate(10);

	}

	public function listing(){
		return $this->where("status=1")->order("supplies_id desc")->paginate(10);

	}

	public function getOne($id){
		return $this->where(["supplies_id"=>$id])->find();
	}



	public function addSupplies($user_id,$name){
		return $this->save(['user_id'=>$user_id,"name"=>$name,"status"=>1,"time"=>time()]);
	}
	public function end($id){
		return $this->where(["supplies_id"=>$id])->update(['status'=>2]);
	}




}
