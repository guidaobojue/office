<?php
namespace app\index\model;
use think\Model;
class category extends Model
{
	protected $table = 'vp_category';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}




	public function getAll(){
		$rs = $this->select();
		$data = [];
		foreach($rs as $k => $v){
			$data[$v->data['category_id']] = $v->data;
		}
		return $data;
		
	}



	public function getOne($id){
		return $this->find(['group_id'=>$id]);
	}

	public function getOneByCid($cid){
		return $this->find(['category_id'=>$cid])->data;
	}


	public function addCategory($data){
		$node = [
			'parent_id' => $data['pid'],
			'icon' => 'fa fa-th-large',
			'childs' => '',
			'url' => $data['url'],
			'category_name' => $data['category_name']
		];
		$this->save($node);
		$category_id = $this->category_id;

		$parent = $this->find(['category_id'=>$data['pid']]);
		$parent = $parent->data;

		$childs = $parent['childs'];
		$childs = json_decode($childs,true);
		$childs[] = (int)$category_id;

		$childs = json_encode($childs);

		$rs = $this->update(['childs'=>$childs],['category_id' => $data['pid']]);
		return true;
		
	}


	public function delCategory($cid){
		$obj = $this->find(['category_id'=>$cid]);

		$pid = $obj->data['parent_id'];
		$parent  = $this->find(['category_id',$pid]);

		$childs = $parent->data['childs'];
		$childs = json_decode($childs,true);

		$isExist = array_search($cid,$childs);
		if($isExist !== false){
			unset($childs[$isExist]);
		}
		$childs = json_encode($childs);
		$this->update(['childs'=>$childs],['category_id'=>$cid]);


		$this->delChild($cid);



	}
	private function delChild($cid){
		$rs = $this->find(['category_id',$cid]);
		if(!empty($rs)){
			$data = $rs->data;
			$childs = $data['childs'];
			if(!empty($childs)){
				$childs = json_decode($childs,true);

				foreach($childs as $k => $v){
					$this->delChild($v);
				}
				$this->where("category_id=".$cid)->delete();
			}
			else{
				$this->where("category_id=".$cid)->delete();
			}

		}

	}

}
