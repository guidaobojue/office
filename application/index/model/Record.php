<?php
namespace app\index\model;
use think\Model;
class Record extends Model
{

	protected $table = 'vp_company';
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
			$data[] = $v->data;
		}
		return $data;
	}

	public function addCompany($data){
		$temp = [];
		$datas = [];
		foreach($data as $k => $v){
			$rs = $this->where("company_name = '$v[0]'")->find();
			if($rs){
				unset($data[$k]);
				continue;
			}

			$temp['company_name'] = $v[0];
			$temp['address'] = $v[1];
			$temp['street'] = $v[2];
			$datas[] = $temp;
		}

		
		return $this->saveAll($datas);


	}






}
