<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use \think\Config;

class Category extends \think\Controller
{
	private $error = [];
	private $tree = [];
	private $nodes = [];

	public function __construct(){
		parent::__construct();

	}

	public function index() {
		$this->assign("list_num",14);

		if(!isset($_SESSION['user'])){
			$this->redirect("/index/index/login");
			return true;
		}

		$model = model("category");
		$list = $model->getAll();
		$this->nodes = $list;

		$tree = $this->build_tree($list);
		$tree = $this->build_tr($tree,0);
		$this->assign("list",$tree);
		return $this->fetch("index");
	}

	private function build_tr($data,$i=0){
		$href = "/index/category";
		++$i;
		if(!empty($data['nodes'])){
			$str = "";
			$str .="<tr>".$this->genTd($i)."<td>".$data['category_name']." <a class='af' href='".$href."/addCategory?pid=".$data['category_id']."' target='_blank'>增加</a>|<a class='af'  target='_blank' href='".$href."/editCategory?cid=".$data['category_id']."'>修改</a>||<a class='af'  target='_blank' href='".$href."/delCategory?cid=".$data['category_id']."'>删除</a></td></tr>";
			foreach($data['nodes'] as $k => $v){
				$str.=$this->build_tr($v,$i);
			}
			return $str;

		}
		else{
			return "<tr>".$this->genTd($i)."<td>".$data['category_name']." <a class='af' href='".$href."/addCategory?pid=".$data['category_id']."' target='_blank'>增加</a>|<a class='af'  target='_blank' href='".$href."/editCategory?cid=".$data['category_id']."'>修改</a>||<a class='af'  target='_blank' href='".$href."/delCategory?cid=".$data['category_id']."'>删除</a></td></tr>";
		}

	}



	private function genTd($i){
		$str = "";
		for($j=1;$j<$i;$j++){
			$str .= "<td></td>";
		}
		return $str;
	}


	private function build_tree($data){
		$root = $data[1];
		$childs = json_decode($root['childs'],true);
		foreach($childs as $k => $v){
			$root['nodes'][] = $this->build_node($v);
		}
		return $root;
	}

	private function build_node($id){
		$obj = $this->nodes[$id];
		if(!empty($obj['childs'])){
			$childs = json_decode($obj['childs'],true);
			foreach($childs as $K => $v){
				$obj['nodes'][] = $this->build_node($v);
			}
		}
		return $obj;
	}




	public function addCategory(){
		if(isset($_POST['sub'])){
			$pid = input("pid");
			$category_name = input("category_name");
			$url = input("url");

			$model = model("category");
			$model->addCategory([
				'pid' => $pid,
				'category_name' => $category_name,
				'url' => $url,
			]);
			
			$this->redirect("/index/category");


		}
		else{
			$pid = input("get.pid");
			$this->assign("pid",$pid);
			$this->assign("icons",Config::get("icons"));
			$model = model("category");
			$list = $model->getAll();
			$this->assign("category_name",$list[$pid]['category_name']);
			$this->assign("list",$list);
			return $this->fetch("addCategory");
		}
	}


	public function init(){
		exit;
		$config = $this->con();
		$data[] = [
			'category_id' => 1,
			'category_name' => '根',
			'url' => '',
			'icon' => 'fa fa-files-o',
			'parent_id' => 0,
			'childs' => [2,5,8,11,14,19],
		];




		foreach($config as $k => $v){
			$v['category_id'] = $v['id']+1;
			$v['category_name'] = $v['name'];
			$v['parent_id'] = $v['parent']+1;
			unset($v['id']);
			unset($v['name']);
			unset($v['parent']);
			foreach($v['childs'] as $ik => &$iv){
				$iv +=1;
			}



			$v['childs'] = json_encode($v['childs']);

			$data[] = $v;
		}

		$model = model("category");
		$model->saveAll($data);
		var_dump($data);
		exit;
	}


	public function editCategory(){
		if(isset($_POST['sub'])){
			$cid = input("post.cid");
			$category_name = input("post.category_name");
			$url = input("post.url");
			$data = [
				'url' => $url,
				'category_name' => $category_name,
			];
			model("category")->update($data,['category_id'=>$cid]);
			$this->redirect("/index/category/index");

		}
		else{
			$cid = input("get.cid");
			$model = model("category");
			$node = $model->getOneByCid($cid);
			$this->assign("cat",$node);
			return $this->fetch("editCategory");
		}
	}

	public function delCategory(){
		$model = model("category");
		$cid = input("cid");
		$model->delCategory($cid);
	}


	public function update(){
		$model = model("category");
		$rs = $model->getAll();
		cache_set('list',$rs);
		return $this->redirect("/index/category/index");
	}


	public function con(){
		return [
			[
				'id' => 1,
				'name' => '文档工具',
				'url' => '',
				'icon' => 'fa fa-files-o',
				'parent' => 1,
				'childs' => [2,3],
			],
			[
				'id' => 2,
				'name' => '文档匹配',
				'url' => '/index/record/supplement',
				'icon' => 'fa fa-files-o',
				'parent' => 1,
				'childs' => [],
			],
			[
				'id' => 3,
				'name' => '文档状态',
				'url' => '/index/record/status',
				'icon' => 'fa fa-files-o',
				'parent' => 1,
				'childs' => [],
			],
			[
				'id' => 4,
				'name' => '职介',
				'url' => '',
				'icon' => 'fa fa-user',
				'parent' => 0,
				'childs' => [5,6],
			],

			[
				'id' => 5,
				'name' => '屏幕',
				'url' => '/index/ftzj/barrage',
				'icon' => 'fa fa-th-large',
				'parent' => 4,
				'childs' => [],
			],
			[
				'id' => 6,
				'name' => '后台',
				'url' => '/index/ftzj/admin',
				'icon' => 'fa fa-th-large',
				'parent' => 4,
				'childs' => [],
			],
			[
				'id' => 7,
				'name' => '维护',
				'url' => '',
				'icon' => 'fa fa-user',
				'parent' => 0,
				'childs' => [9],
			],

			[
				'id' => 8,
				'name' => '维护',
				'url' => '/index/comment/comment',
				'icon' => 'fa fa-th-large',
				'parent' => 7,
				'childs' => [],
			],
			[
				'id' => 9,
				'name' => '后台',
				'url' => '/index/comment/commentList',
				'icon' => 'fa fa-th-large',
				'parent' => 7,
				'childs' => [],
			],


			[
				'id' => 10,
				'name' => '用户',
				'url' => '',
				'icon' => 'fa fa-user',
				'parent' => 0,
				'childs' => [11,12],
			],

			[
				'id' => 11,
				'name' => '个人主页',
				'url' => '/index/User/index',
				'icon' => 'fa fa-th-large',
				'parent' => 10,
				'childs' => [],
			],
			[
				'id' => 12,
				'name' => '修改密码',
				'url' => '/index/User/changePwd',
				'icon' => 'fa fa-th-large',
				'parent' => 10,
				'childs' => [],
			],


			[
				'id' => 13,
				'name' => '管理员',
				'url' => '',
				'icon' => 'fa fa-user',
				'parent' => 0,
				'childs' => [14,15,16,17],
			],

			[
				'id' => 14,
				'name' => '用户管理',
				'url' => '/index/manager/user',
				'icon' => 'fa fa-th-large',
				'parent' => 13,
				'childs' => [],
			],
			[
				'id' => 15,
				'name' => '群组设置',
				'url' => '/index/manager/group',
				'icon' => 'fa fa-th-large',
				'parent' => 13,
				'childs' => [],
			],
			[
				'id' => 16,
				'name' => '菜单设置',
				'url' => '/index/category/index',
				'icon' => 'fa fa-th-large',
				'parent' => 13,
				'childs' => [],
			],
			[
				'id' => 17,
				'name' => '权限设置',
				'url' => '/index/pr/index',
				'icon' => 'fa fa-th-large',
				'parent' => 13,
				'childs' => [],
			],
			[
				'id' => 18,
				'name' => '设置',
				'url' => '/index/category/index',
				'icon' => 'fa fa-th-large',
				'parent' => 0,
				'childs' => [19],
			],
			[
				'id' => 19,
				'name' => '感谢名单',
				'url' => '/index/common/thanks',
				'icon' => 'fa fa-th-large',
				'parent' => 18,
				'childs' => [],
			],


		];
	}


}

