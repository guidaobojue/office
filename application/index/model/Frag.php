<?php
namespace app\index\model;
use think\Model;
class Frag extends Model
{
	protected $table = 'vp_question_frag';
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}



	public function addFrag($name){
		$this->save(['name'=>$name]);
		return $this->frag_id;
	}

	public function saveFrag($frag_id,$content,$code){
	}

	public function getOne($frag_id){
		return $this->where(['frag_id'=>$frag_id])->find();
	}


}

