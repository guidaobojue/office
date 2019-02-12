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
		$data = $this->filter(0);
		return $data['cNode'];

	}

	public function filter($cid){
		$child = [];
		$temp = [];
		$child = $this->data[$cid];

		$childs = $child['childs'];
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
		if($n == 1){
			return [
				[
					'id' => 0,
					'name' => '根结点',
					'url' => '',
					'icon' => 'fa fa-files-o',
					'parent' => 0,
					'childs' => [1,4,7,10,13,18],
				],
				[
					'id' => 1,
					'name' => '文档工具',
					'url' => '',
					'icon' => 'fa fa-files-o',
					'parent' => 0,
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
					'name' => '评论',
					'url' => '',
					'icon' => 'fa fa-user',
					'parent' => 0,
					'childs' => [8,9],
				],

				[
					'id' => 8,
					'name' => '评论',
					'url' => '/index/common/comment',
					'icon' => 'fa fa-th-large',
					'parent' => 7,
					'childs' => [],
				],
				[
					'id' => 9,
					'name' => '后台',
					'url' => '/index/common/commentList',
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
					'url' => '/index/pri/index',
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
		else{
			return [
				[
					'id' => 0,
					'name' => '根结点',
					'url' => '',
					'icon' => 'fa fa-files-o',
					'parent' => 0,
					'childs' => [1,4,7,10,18],
				],
				[
					'id' => 1,
					'name' => '文档工具',
					'url' => '',
					'icon' => 'fa fa-files-o',
					'parent' => 0,
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
					'name' => '评论',
					'url' => '',
					'icon' => 'fa fa-user',
					'parent' => 0,
					'childs' => [8,9],
				],

				[
					'id' => 8,
					'name' => '评论',
					'url' => '/index/common/comment',
					'icon' => 'fa fa-th-large',
					'parent' => 7,
					'childs' => [],
				],
				[
					'id' => 9,
					'name' => '后台',
					'url' => '/index/common/commentList',
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

}
