<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use app\extra\pri;

class Api extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();




	}



	public function rencai(){
		$method = input('method');
		$param = input('param');
		if(empty($method) || empty($param)){
			die("wrong");
		}
		
		$methods = [
			'checkcompany' => "checkcompany",
		];
		//
		if(isset($methods[$method])){
			$rs = $this->$method($param);
		}
		return $rs;

	}

	private function checkcompany($company_name){
		$company_name = input("company_name");
		$model = model("temp");
		$rs = $model->getOne(trim($company_name));
		die(json_encode($rs));

	}


	public function ftzj(){
		$method = input('method');
		$param = input('param');
		if(empty($method) || empty($param)){
			die("wrong");
		}
		
		$methods = [
			'checkId' => "checkId",
		];
		//
		if(isset($methods[$method])){
			$rs = $this->$method($param);
		}
		return $rs;

	}


	private function checkId($company_id){
		$model = model("company");
		$company_id = input("company_id");
		$rs = $model->hasCompanyId($company_id);
		if($rs)
			return json_encode(true);
		else{
			return json_encode(false);
		}
	}


	public function user(){
		$method = input('method');
		$param = input('param');
		if(empty($method) || empty($param)){
			die("wrong");
		}
		
		$methods = [
			"searchname" => "searchname",
			'getUserIdByName' => 'getUserIdByName',
			"getUserByDep" => "getUserByDep",
			'read' => 'read',
		];
		//
		if(isset($methods[$method])){
			$rs = $this->$method($param);
		}

		echo json_encode($rs);



	}

	public function record(){
		$method = input('method');
		$param = input('param');
		if(empty($method)){
			die("wrong");
		}
		
		$methods = [
			"comstreet" => "comstreet",
		];
		//
		if(isset($methods[$method])){
			$rs = $this->$method($param);
		}

		echo json_encode($rs);



	}

	private function comstreet($company_name){
		$company_name = input("company_name");
		$model = model("vpcompany");
		$rs = $model->getStreet($company_name);
		return $rs;

	}





	private function read($message_id){
		if(empty($message_id) || !is_numeric($message_id))
			return false;
		$model = model("UserMessage");
		$rs = $model->read($message_id);
		echo json_encode($rs);
	}

	private function getUserByDep($dep_id){
		$userModel = model("user");
		$depModel = model("department");
		$deps = $depModel->getAll();
		$users = $userModel->getListByDep($dep_id);

		return $users;
		/*
		foreach($users as $k => $v){
			if(!isset($data[$v['department_id']])){
				$data[$v['department_id']]['users'][] =$v;
				$data[$v['department_id']]['name'] =$deps[$v['department_id']];
			}
			else{
				$data[$v['department_id']]['users'][] =$v;
			}

		}
		 */
	}


	public function machine(){
		$method = input('method');
		$param = input('param');
		if(empty($method) || empty($param)){
			die("wrong");
		}
		
		$methods = [
			"searchname" => "searchname",
			'getUserIdByName' => 'getUserIdByName',
		];
		if(isset($methods[$method])){
			$rs = $this->$method($param);
		}

		echo json_encode($rs);

	}




	private function searchname($name){
		$model = model("user");
		$rs = $model->searchName($name);
		return $rs;
	}


	public function test(){
		var_export(get_class_methods($this));

	}

}
