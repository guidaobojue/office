<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Category extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}

	public function index()
	{

		if(!isset($_SESSION['user'])){
			$this->redirect("/index/index/login");
			return true;
		}
		return $this->fetch("index");
	}


	public function addCategory(){
		return $this->fetch("addCategory");
	}

	public function editCategory(){
	}

	public function delCategory(){
	}



}

