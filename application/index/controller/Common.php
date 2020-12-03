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

	public function com(){
		/*
		$rs = readExcel("/home/matengfei/Application/Php/office/data.xls");
		$data = $rs['data'];
		 */
		$model = model("cert");
		$list = $model->getAlls();
		$temp =[]; 
		foreach($list as $k => $v){
			$temp[] = [
				'id' => $v['cert_id'],
				'u' => $v['company_name'],
				'n' => $v['user_name'],
				's' => $v['tel'],
			];
		}
		$title=[
			'序号',
			'单位名称',
			'经办人',
			'手机号',
		];
		$rs = writeExcel($temp,$title,"new_data");


		echo '<a href="/xls/'.$rs.'">下载</a>';
	}

	/*
	public function ini(){
		exit;
		$rs = readExcel("/home/matengfei/Application/Php/office/money.xlsx");
		$ls = [];
		$data = $rs['data'];
		$temp = [];
		foreach($data as $k => $v){
			$temp[trim($v[1])] = $v;
		}
		$data =$temp;


		$certmodel = model("cert");
		$certRs = $certmodel->getAlls();
		$temp = [];
		$list = [];
		foreach($certRs as $k => $v){
			if(isset($data[$v['company_name']])){
				$rs = $data[$v['company_name']];
				$list[] = [
					'cert_id' => $v['cert_id'],
					'taxe' => $rs[2],
					'taxe_time' => $rs[3],
				];

			}
		}
		$certmodel->isUpdate()->saveAll($list);
	}

	public function init(){
		exit;
		$tempmodel = model("temp");
		$tempRs = $tempmodel->getAll();
		$certmodel = model("cert");
		$certRs = $certmodel->getAlls();
		$data = [];
		foreach($tempRs as $k => $v){
			$data[$v['company_name']] = $v;
		}
		$temp = [];
		$list = [];
		foreach($certRs as $k => $v){
			if(isset($data[$v['company_name']])){
				$rs = $data[$v['company_name']];
				$list[] = [
					'cert_id' => $v['cert_id'],
					'user_name' => $rs['user_name'],
					'tel' => $rs['tel'],
					'own_uname' => $rs['own_uname'],
					'own_tel' => $rs['own_tel'],
				];

			}
		}
		$certmodel->isUpdate()->saveAll($list);
	}

	public function record(){
		$rs = readExcel("/home/matengfei/Application/Php/office/工作居住证分配表发公服.xlsx");
		$data = [];
		$temp = [];
		$rs = $rs['data'];
		foreach($rs as $k => $v){
			$data[] = [ 
				'company_name' => $v[1],
				'new_num' => $v[5],
			];
		}
		$model = model("cert");
		$model->saveAll($data);
	}
	 */

	private function getname($name){
		$name = str_replace(" ","",$name);
		$name = str_replace("\r","",$name);
		$name = str_replace("\n","",$name);
		$name = str_replace("（","",$name);
		$name = str_replace("）","",$name);
		return $name;
	}
	private function getOther($name,$list){
		foreach($list  as $k => $v){
			if($this->getname($name) == $this->getname($v[1])){
				return array_slice($v,2,3);
			}
		}
		return false;
	}








	public function add(){
		if(isset($_POST['sub'])){
			$model = model("temp");
			$data = $_POST;
			$backup_id = isset($_POST['backup_id']) ? $_POST['backup_id'] : "";
			unset($data['sub']);
			unset($data['backup_id']);
			$data['create_time'] = time();

			$company_name = trim($_POST['company_name']);
			$user_name = trim($_POST['user_name']);
			$tel = trim($_POST['tel']);
			$own_tel= trim($_POST['own_tel']);
			if(empty($company_name)){
				return $this->fetch("backup");
			}

			if(empty($tel) || empty($own_tel)){
				$data['tip'] = 1;
			}

			if(empty($backup_id)){
				$rs = $model->checkE($company_name,$user_name);
				if(!$rs){
					$rs = $model->addNew($data);
					$this->assign("new_id",$rs);
				}
				else{
					$this->assign("new_id","己存在");
				}
			}
			else {
				$model->up($_POST['backup_id'],$data);
				$this->assign("new_id",$backup_id);
			}
		}
		return $this->fetch("backup");
	}



	/*
	 * thanks 感谢名单
	 * qrcode 生成二维码
	 * qrupload 二维码传图页面
	 * comment 问题反馈页面
	 * details 查看评论
	 * question 评论页面
	 */

	public function thanks(){

		$this->assign("list_num",19);
		return $this->fetch("thanks");
	}



	public function qrcode(){
		$qrcode = $_GET['qrcode'];
		include('../extend/phpqrcode/qrlib.php');
		QRcode::png("http://".WEB_URL.DL."index/common/qrupload?qrcode=".$qrcode);
	}

	public function qrupload(){
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

