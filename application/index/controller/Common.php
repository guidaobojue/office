<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use \app\index\model\company;
use \app\index\model\job;

class Common extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}


	public function thanks(){
		return $this->fetch("thanks");
	}



	public function comment(){
		$obj = model("comment");
		if(isset($_POST['sub'])){
			$post = $_POST;

			$data['content'] = $post['text'];
			$data['user_name'] = $post['user_name'];
			$data['user_tel'] = $post['user_tel'];
			$data['department'] = $post['department'];
			$data['status'] = 0;
			$data['create_time'] = time();
			$data['thumb'] ="" ;



			$file = request()->file('image');
			if($file){
				$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
				if($info){
					$data['thumb'] = $info->getSaveName();
				}
			}
			$obj->add($data);
			$this->assign("ok",1);
		}

		$data = [];
		$list= $obj->list();
		$page = $list->render();
		$this->assign("list",$list);
		$this->assign("page",$page);

		return $this->fetch("front_comment");
	}


	public function details(){
		$id = input("id");
		$model = model("Comment");
		$rs = $model->getOne($id);
		$this->assign("info",$rs);
		return $this->fetch("details");
	}


	public function commentList(){
		$obj = model("comment");
		$data = [];
		$list= $obj->list();
		$page = $list->render();
		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("comment_list");
	}


	public function question(){
		return $this->fetch("comment");
	}

	public function auditing(){
		$id = input("id");
		$model = model("Comment");
		$model->read($id);
		echo json_encode(1);

	}

}

