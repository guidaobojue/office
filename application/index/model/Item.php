<?php
namespace app\index\model;
use think\Model;
class Item extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}


	public function addItem($data){
		$this->save($data);
		return $this->item_id;

	}

	public function getItemsByUserId($user_id){
		$rs = $this->where(['user_id'=>$user_id])->limit($start,$pageSize)->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[] = $v->data;
		}
		return $data;
	}

	public function list($user_id){
		return  $this->where(['user_id'=>$user_id])->paginate(10,false,['query'=>['user_id'=>$user_id]]);
	}

	public function getOne($item_id){
		$rs = $this->query("select a.*,b.* from vp_item a left join vp_model b on a.model_id = b.model_id where a.item_id = '$item_id'");
		if(!empty($rs))
			return current($rs);
		else 
			return false;
	}


	public function chgUser($item_id,$user_id){
		return 	$this->where(["item_id"=>$item_id])->update(['user_id'=>$user_id]);
	}
}
