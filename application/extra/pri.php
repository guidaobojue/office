<?php
namespace app\extra;
use \think\view;
use \think\Request;
use \think\Model;
use \think\Config;

class pri{

	private $config = [];
	private $temp = [];
	private $categorys = [];
	private $data = [];

	public function __construct(){
		$this->config['root'] = Config::get('root');
	}
	
	public function checkSession(){
		if(!isset($_SESSION['user']))
			return false;
		else 
			return true;
	}


	public function checkPri(){
		return true;
		if(!in_array($action,['login','timing','comment','details','question','qrcode','qrupload','barrage'])){
		}
	}


	public function category(){
		if(!isset($_SESSION['user']))
			return [];
		$groupIds = $_SESSION['user']['group'];


		

		if(in_array($this->config['root'],$groupIds)){
			$data = $this->data(1);
		}

		else{

			$data = $this->data(2);
		}

		foreach($data as $k => $v){
			$this->data[$v['id']] = $v;
		}
		$data = $this->filter(1);
		return $data['cNode'];

	}

	public function filter($cid){
		$child = [];
		$temp = [];
		$child = $this->data[$cid];

		$childs = json_decode($child['childs']);
		if(!empty($childs)){
			foreach($childs as $k => $v){
				$child['cNode'][] = $this->filter($v);
			}

		}
		else{
			$child['cNode'] = [];
			return $child;
		}
		return $child;
	}

	public function detail($data){
	}




	public function data($n){
		$rs = cache_get('list');
		if(!$rs){
			$model = model("category");
			$rs = $model->getAll();
			cache_set('list',$rs);

		}
		foreach($rs as $k => &$v){
			$v['id'] = $v['category_id'];
			$v['name'] = $v['category_name'];
		}
		return $rs;

	}

}
