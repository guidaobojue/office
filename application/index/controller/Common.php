<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use \app\index\model\company;
use \app\index\model\job;
use app\extend\qrcode\QRcode;

class Common extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}


	public function thanks(){
		return $this->fetch("thanks");
	}



	public function qrcode(){
		$qrcode = $_GET['qrcode'];
		include('../extend/phpqrcode/qrlib.php');
		QRcode::png("http://".WEB_URL.DL."index/common/qrupload?qrcode=".$qrcode);
	}

	public function qrupload(){
		return $this->fetch("success");
		if(isset($_POST['sub'])){
			$file = request()->file('image');
			$data = [];
			$data['qrcode'] = $_POST['qrcode'];
			if($file){
				$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
				if($info){
					$data['thumb'] = $info->getSaveName();
					$this->assign("status",'1');
				}
				$data['time'] = time();
				$model = model("upload");
				$model->save($data);

			}
			return $this->fetch("success");
		}
		if(empty($_GET['qrcode'])){
			$this->redirect("/index/common/question");
		}
		$qrcode = addslashes($_GET['qrcode']);
		$this->assign("qrcode",$qrcode);
		return $this->fetch("qrupload");
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
			$data['qrcode'] = $post['qrcode'];
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

		return $this->fetch("comment_list");
	}


	public function details(){
		$id = input("id");
		$model = model("Comment");
		$rs = $model->getOne($id);
		if(empty($rs['thumb'])){
			$rs['thumb'] = $rs['qr_thumb'];
		}
		$this->assign("info",$rs);
		return $this->fetch("details");
	}




	public function question(){
		$qrcode = time() . rand(1,1000).rand(1,1000);
		$this->assign("qrcode",$qrcode);
		
		return $this->fetch("question");
	}




}

