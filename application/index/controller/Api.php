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

	public function user(){
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


}
