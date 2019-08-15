<?php
namespace app\index\model;
use think\Model;
class vpcompany extends Model
{
	protected $table = 'vp_company';

	/*
	 * @param origin 1本地增加
	 * 	 	 2从外网获取 
	 */


	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();

		//TODO:自定义的初始化
	}

	public function getStreet($company_name){
		$rs = $this->where(['company_name'=>$company_name])->find();
		if(!empty($rs))
			return $rs->street;
		else 
			return '';
	}
}

