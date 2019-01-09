<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Barrage extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();
	}

	public function index()
	{
		return $this->fetch("index");
	}


}
