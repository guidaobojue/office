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
	private $num = 0;

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

		$groupId = array_pop($groupIds);
		$data = $this->data($groupId);
		foreach($data as $k => $v){
			$this->data[$v['id']] = $v;
		}

		$data = $this->filter(1);
		return $data['cNode'];

	}

	public function filter($cid){
		$child = [];
		$temp = [];
		if(!isset($this->data[$cid]))
			return false;
		$child = $this->data[$cid];
		$childs = json_decode($child['childs']);
		if(!empty($childs)){
			foreach($childs as $k => $v){
				$filterRs = $this->filter($v);
				if($filterRs)
					$child['cNode'][] = $filterRs;
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



	public function update($n){
		$rs = cache_get('list');
		$pri = false;
		if(!$rs){
			$model = model("category");
			$rs = $model->getAll();
			cache_set('list',$rs);

		}
		$this->categorys = [];
		if(!$pri){
			$model = model("GroupCategory");
			$categoryIdsRs = $model->getAllByGid($n);
			$categoryIds = [];
			foreach($categoryIdsRs as $k => $v){
				$categoryId = $v['category_id'];

				$this->getParents($categoryId,$rs);
				$this->categorys[] = $categoryId;
			}


			$categorys = array_unique($this->categorys);
			$pri = [];
			foreach ($categorys as $k => $v){
				if(isset($rs[$v])){
					$pri[] = $rs[$v];
				}
			}
			cache_set("pri_$n",$pri);
		}
		return true;
	}

	public function data($n){
		$rs = cache_get('list');
		$pri = cache_get("pri_$n");
		if(!$rs){
			$model = model("category");
			$rs = $model->getAll();
			cache_set('list',$rs);

		}
		if(!$pri){
			$model = model("GroupCategory");
			$categoryIdsRs = $model->getAllByGid($n);
			$categoryIds = [];
			foreach($categoryIdsRs as $k => $v){
				$categoryId = $v['category_id'];

				$this->getParents($categoryId,$rs);
				$this->categorys[] = $categoryId;
			}


			$categorys = array_unique($this->categorys);

			$pri = [];
			foreach ($categorys as $k => $v){
				if(isset($rs[$v])){
					$pri[] = $rs[$v];
				}
			}
			cache_set("pri_$n",$pri);
		}


		foreach($pri as $k => &$v){
			$v['id'] = $v['category_id'];
			$v['name'] = $v['category_name'];
		}
		return $pri;

	}

	private function getParents($cid,$list){
		if(isset($list[$cid])){
			$cat = $list[$cid];
			$parent_id = $cat['parent_id'];
			if(isset($list[$parent_id])){
				if($this->num >=2) die("权限问题，请联系管理员,电话:83326618");
				$this->categorys[] = $parent_id;
				$this->getParents($parent_id,$list);
			}
		}
	}
}
