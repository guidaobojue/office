<?php
namespace app\index\model;
use think\Model;
class Pri extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}

	public function getAll(){
		return $this->select();
	}

	public function addPri($module,$controller_name,$actions){
		$datas = [];
		foreach($actions as $k => $v){
			$datas[] = [
				'pri_name' => $v['pri_name'],
				'module_name' => $module,
				'controller_name' => $controller_name,
				'method' => $v['method'],
			];
		}
		$this->saveAll($datas);
	}

	public function getModules(){
		return $this->field("controller_name")->select();
	}

	public function delByModule($name){
		$this->where(['controller_name'=>$name])->delete();
	}
	public function addModule($arr){
		$temp = [];
		foreach($arr['a'] as $k => $v){
			$temp[] = [
				'pri_name' => $v['pri_name'],
				'module_name'=> $arr['m'],
				'controller_name' => $arr['c'], 
				'method'=>$v['method'],
			];
		}
		$save = [];
		foreach($temp as $k => $v){
			$rs = $this->where($v)->find();
			if(!$rs){
				$save[] = $v;
			}
		}
		$this->saveAll($save);

	}

}
