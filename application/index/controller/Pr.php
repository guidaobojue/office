<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use app\extra\pri;

class Pr extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();




	}

	public function index()
	{


		$this->assign("list_num",14);

		return $this->fetch("index");
	}






}
