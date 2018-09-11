<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Record extends \think\Controller
{
	private $subMoney;
	private $subPresent;
	private $hasCrash;
	private $docId;


	public function __construct(){
		parent::__construct();

	}


	public function search(){
	}



	public function supplement(){
		$uploads_dir = "../upload/";
		if(!empty($_FILES['file']['name'])){
			$v = $_FILES['file'];
			$tmp_name =  $v['tmp_name'];
			$names = filename($v['name']);
			$suf = $names['name'];
			$ex = $names['exp'];
			$suf = "up_".md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);

			$recordModel = model("Record");
			$excel = readExcel($uploads_dir.$suf);


			$data = $excel['data'];
			$title = $excel['title'];

			$companys = model("Record")->getAll();

			$data = combine($data,$companys);
			$title[] = "所属街道";
			$rs = writeExcel($data,$title);


			$this->ok = 1;
			if($this->hasCrash !==0){
				$this->assign("crash",true);
			}
			echo "<html><body>
				注意查看下载任务
				<script>
window.location.href = '/xls/$rs';
</script>
			</body></html>";

		}
		else
			return $this->fetch("supplement");






	}







	public function export(){
		$uploads_dir = "../upload/";
		if(!empty($_FILES['file']['name'])){
			$v = $_FILES['file'];
			$tmp_name =  $v['tmp_name'];
			$names = filename($v['name']);
			$suf = $names['name'];
			$ex = $names['exp'];
			$suf = "up_".md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);

			$recordModel = model("Record");
			$excel = readExcel($uploads_dir.$suf);
			$data = $excel['data'];
			$title = $excel['title'];
			$rs = $recordModel->addCompany($data);
			$this->ok = 1;
			if($this->hasCrash !==0){
				$this->assign("crash",true);
			}


		}
		$page =input("post.p",1);
		return $this->fetch("export");

	}



}
