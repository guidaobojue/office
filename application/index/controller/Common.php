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

	public function  com(){
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

	public function cert(){
		if(isset($_POST['sub'])){
			$data = $_POST;
			unset($data['sub']);
			$model = model("Cert");
			#$rs = $model->check($data['company_name']);
			#if(!$rs)
			$id = $model->add($data);
			$this->assign("new_id",$id);
		}
		return $this->fetch("cert");

	}
	public function chgCert(){
		$model = model("cert");
		if(isset($_POST['sub'])){
			$data = $_POST;
			$cert_id = $data['cert_id'];
			unset($data['sub']);
			$model->chg($cert_id,$data);
			$this->redirect("/index/common/certlist");

		}
		else{
			$cert_id = input("id");
		}
		$rs = $model->getOne($cert_id);
		$this->assign("d",$rs);
		return $this->fetch("chgCert");
	}

	public function certlist(){
		$model = model("cert");
		$search= input("search");
		$rs = $model->getAll($search);


		$this->assign("page",$rs->render());
		$this->assign("list",$rs);
		return $this->fetch("cert_list");

	}


	public function addNewNum(){
		$model = model("cert");
		$cert_id = input("cert_id");
		$rs = $model->chgNewNum($cert_id,1);
		die(json_encode($rs));

	}
	public function subNewNum(){
		$model = model("cert");
		$cert_id = input("cert_id");
		$rs = $model->chgNewNum($cert_id,-1);
		die(json_encode($rs));
	}


	public function addHasNum(){
		$model = model("cert");
		$cert_id = input("cert_id");
		$rs = $model->chgHasNum($cert_id,1);
		die(json_encode($rs));
	}
	public function subHasNum(){
		$model = model("cert");
		$cert_id = input("cert_id");
		$rs = $model->chgHasNum($cert_id,-1);
		die(json_encode($rs));
	}

	public function fuck(){
		$a = readExcel(WEB_DIR."/test.xlsx");
		$list = array_column($a['data'],10);
		$list = array_unique($list);
		$data = $a['data'];
		$temp = [];
		foreach($data as $k => $v){
			if(!empty($v[1])){
				$temp[] = $v;
			}
		}
		$data = $temp;
		$temp = [];
		foreach($data  as $k => $v){
			foreach($list as $ik =>$iv){
				if($k == 204){
				}
				if(strpos($iv,substr($v[1],0,12)) !== false){
					$v[11]  = "存在";
					$v[12] = $iv;
				}

			}
			$temp[] = $v;
		}
		foreach($temp as $k => &$v){
			if(!isset($v[11]))
				$v[11] = "";
			if(!isset($v[12]))
				$v[12] = "";

		}
		$data = $temp;

		$title = ["a","b","c","d","e","f","g","h","i","j","k","l","m"];
		#var_dump(count($title),count($data[0])); exit;
		$rs = writeExcel($data ,$title,"nice");
		echo '<a href="/xls/'.$rs.'">下载</a>';

	}
	public function test2(){
		$a = readExcel(WEB_DIR."/1.xlsx");
		$aD = $a['data'];
		$b = readExcel(WEB_DIR."/2.xlsx");
		$bD = $b['data'];


		/*
		$b = [];
		$is = [];
		$total = [];
		foreach($bD as $k => $v){
			$total[$this->getname($v[1])] = $v[1];
			foreach($aD as $ik => $iv){
				if($this->getname($v[1]) == $this->getname($iv[1]))
					$is[$this->getname($v[1])] = $v[1];
			}
		}
		$no = [];
		foreach($total as $k => $v){
			foreach($is as $ik => $iv){
				if($k == $ik){
					unset($total[$k]);
				}
			}
		}

		foreach($total as $k => $v){
			echo $v."<br>";
		}
		 */




		$datas = [];
		$noMon = [];
		$is = [];
		$total = [];
		foreach($aD as $k =>$v){
			$rs = $this->getOther($v[1],$b['data']);
			$total[] = $v[1];
			if($rs){
				$datas[] = array_merge(array_slice($v,0,15),$rs);
				$is[] = trim($v[1]);

			}
			else{
				$datas[] = array_slice($v,0,18);
				$noMon[] = $v[1];
			}

		}
		$title = [ '序号','单位名称','统一社会信用代码','企业类别','经营范围','注册地址','办公地址','注册资金','实缴注册资本金','税务登记地是否为丰台区','单位近一年纳税','单位近三年纳税','单位近三年合法用工人数','工作居住证需求数量','备注','企业纳税（不含个税）','税务所属时间','备注' ]; 
		$rs = writeExcel($datas,$title,"hebing");
		echo '<a href="/xls/'.$rs.'">下载</a>';
		exit;

	}
	public function getname($name){
		$name = str_replace(" ","",$name);
		$name = str_replace("\r","",$name);
		$name = str_replace("\n","",$name);
		$name = str_replace("（","",$name);
		$name = str_replace("）","",$name);
		return $name;
	}
	public function getOther($name,$list){
		foreach($list  as $k => $v){
			if($this->getname($name) == $this->getname($v[1])){
				return array_slice($v,2,3);
			}
		}
		return false;
	}


	public function backup(){
		exit;
		$url = WEB_DIR."/backup.xls";
		$rs = readExcel($url);
		$data = $rs['data'];
		$data = array_slice($data,1);
		$temp = [];
		$company_name = "";
		foreach($data as $k => $v){
			if(empty($v[1]) && empty($v[2])){
				continue;
			}
			if(!empty($v[1])){
				$company_name = $v[1];
			}
			$temp[] = [
				'company_name'=>$company_name,
				'user_name'=>$v[2],
				'tel'=>$v[3],
				'own_tel'=>$v[4],
				'create_time'=> strtotime(str_replace(".","-",$v[5])),
				'comments'=>$v[6],
			];
		}
		$model = model("temp");
		$model->adds($temp);

	}
	public function diff(){
		exit;
		$model = model("temp");
		$rs = $model->getAll();
		$list = $model->getBackupAll();
		$data = array_slice($list,161);
		$temp = [];
		$ins= [];
		foreach($data as $k => $v){
			$ins = $v;
			unset($ins['backup_id']);
			$temp[] = $ins;
		}

		$model->adds($temp);
	}

	public function export(){
		$model = model("temp");
		$list = $model->getAll();
		foreach($list as $k =>  &$v){
			if(is_numeric($v['create_time']))
				$v['create_time'] = date("Y-m-d",$v['create_time']);
		}
		$title = [
			'序号','单位名称','经办人','联系电话','负责人电话','备案时间','备注','标记'
		];
		$rs = writeExcel($list,$title,"backup");
	}


	public function checkcompany(){
		$company_name = input("company_name");
		$model = model("temp");
		$rs = $model->getOne(trim($company_name));
		die(json_encode($rs));

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


	public function he(){

		$url = WEB_DIR."/need";
		$temp=scandir($url);
		$name = [];
		$datas = [];
		$title = [];
		$i=0;
		foreach($temp as $v){
			if(strpos($v,".xl") !== false){
				try{
					$data = readExcel($url ."/".$v);
					$title = $data['data'][1];
					if(isset($data['data'][2])){
						$datas[] = $data['data'][2];
					}
				}
				catch(Exception $e){
					echo "Error : $url<br>";
				}

			}
		}

		$has=[];
		$temp = [];
		foreach($datas as $k => $v){
			$v[2] = " ".$v[2];
			$name = $this->getname($v[1]);
			if(!in_array($name,$has)){
				$has[] = $name;
				$temp[] = $v;
			}
		}
		$datas = $temp;



		$rs = writeExcel($datas,$title,"total");
		echo '<a href="/xls/'.$rs.'">下载</a>';
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


	public function test3(){
		$this->path = "/home/matengfei/Application/data/data";
		$this->data = [];
		if ($handle = opendir($this->path)) {
			/* This is the correct way to loop over the directory. */
			while (false !== ($entry = readdir($handle))) {
				if(strpos("..",$entry) !== false){
				}
				else{
					$depName = $entry;
					$this->getContent($entry);
				}
			}
			closedir($handle);
			$title = array_fill(0,count($this->data[0]),"");
			$rs = writeExcel($this->data,$title,"com");
			echo '<a href="/xls/'.$rs.'">下载</a>';
		}
	}






	public function getContent($entry){
		if($handle = opendir($this->path ."/" . $entry)){
			while(false != ($fileName = readdir($handle))){
				$file = file_get_contents($this->path . "/" . $entry."/".$fileName);
				$file  = iconv('GBK', 'UTF-8//IGNORE', $file);
				$preg = preg_match("/系统制造商:(.*)/",$file,$match);
				if(isset($match[1]))
					$comModel = $match[1];
				else
					$comModel = '';

				$preg = preg_match("/系统型号:(.*)/",$file,$match);
				if(isset($match[1])){
					$comId = $match[1];
				}
				else
					$comId = "";

				$preg = preg_match("/OS 名称:(.*)/",$file,$match);
				if(isset($match[1])){
					$os= $match[1];
				}
				else
					$os= "";

				preg_match("/\d(.*)/",$os,$match);
				if(isset($match[1])){
					$osModel = $match[1];
				}
				else
					$osModel = "";
				$os = str_replace($osModel,"",$os);


				/*
				系统制造商:       FOUNDERTECH
					:
				 */


				$un = str_replace(".txt","",$fileName);
				$osId = $this->countOs(trim($os),trim($osModel));
				$this->data[] = [
					'dp' => $entry,
					'un' => $un,
					'comid' => trim($comId),
					'cm' => trim($comModel),
					'os' => trim($os),
					'osid' => $osId,
					'om' => trim($osModel),
					'time' => '',
					'install' => '',
				];
				$inserD = function($name,$osid){
					return [
						'dp' => '',
						'un' => '',
						'comid' => '',
						'cm' => '',
						'os' => $name,
						'osid' => $osid,
						'om' => '',
						'time' => '',
						'install' => '',
						''
					];

				};
				$osId = $this->countOs("360杀毒软件","360杀毒软件");
				$this->data[] = $inserD("360杀毒软件",$osId);
				$osId = $this->countOs("360安全中心","360安全中心");
				$this->data[] = $inserD("360安全中心",$osId);
				$osId = $this->countOs("360浏览器","360浏览器");
				$this->data[] = $inserD("360浏览器",$osId);
				$osId = $this->countOs("WPS","WPS");
				$this->data[] = $inserD("WPS",$osId);
			}

		}
	}

	public function countOs($os,$om){
		$osname = $os.'-'.$om;
		if(!isset($this->osids)){
			$this->osids = [];
		}
		foreach($this->osids as $k => $v){
			if($v == $osname)
				return $k;
		}
		if(!isset($this->max))
			$this->max =1;
		else{
			$this->max +=1;
		}
		if($this->max >=10 && $this->max <100){
			$max = "0".$this->max;
		}
		elseif($this->max <10){
			$max = "00" . $this->max;
		}
		else{
			$max = $this->max;
		}
		$osIds = "A". $max;
		$this->osids[$osIds] = $osname;
		return $osIds;


	}


	public function test(){

		$has = [];
		$list = [];


		$has = file_get_contents("./list");
		$has = json_decode($has);
		if(empty($has))
			$has = [];
		$dir = "/home/matengfei/Application/Php/office/test/4/";
		$dh= opendir($dir);
		while (($file = readdir($dh)) !== false) {
			if(strpos($file,"xls") !== false){
				$list[] = $file;
			}
		}
		if(count($has) >= count($list)){
			var_dump($has);
			echo "END";
			exit;
		}
		closedir($dh);


		foreach($list as $k => $v){
			if(!in_array($v,$has)){
				$path = $dir . $v;
				var_dump($v);
				$data = readExcel($path);
				$data = $data['data'];


				$rs = file_get_contents("./content");
				$rs = json_decode($rs);
				if(empty($rs))
					$rs = [];

				for($i=3;$i<count($data);$i++){
					if(!empty($data[$i][6])){
						$rs[] = $data[$i];
					}
					else{
					}

				}

				file_put_contents("./content",json_encode($rs));
				$has[] = $v;
				file_put_contents("./list",json_encode($has));
				header('Location: http://dev.nanyuan.com/index/common/test');

			}
		}

		header('Location: http://dev.nanyuan.com/index/common/test');

		exit;


		#ini_set("memory_limit","1800M");
		#set_time_limit(0);
		$data = [];
		$all = [];
		$wrong = [];

		$n = 0;
		while (($file = readdir($dh)) !== false) {
			if(strpos($file,"xls") !== false){
				$path = $dir . $file;
				$list[] = $file;
				$data = readExcel($path);
				$data = $data['data'];
				if(isset($data[2][0])){
					$all[] = $data[2];
				}
				else{
					$wrong[] = $data[2];
				}

			}
			else{
			}

		}
		$content = json_encode($all);
		$f = file_put_contents("./asdfg.txt",$content);
		closedir($dh);

		/*
		$this->path = "/home/matengfei/Application/data/data";
		$a = readExcel(WEB_DIR."/1.xlsx");
		$aD = $a['data'];
		$b = readExcel(WEB_DIR."/2.xlsx");
		$bD = $b['data'];
		 */
	}

	public function end(){
		$file = file_get_contents("./content");
		$rs = json_decode($file);

		$dir = "/home/matengfei/Application/Php/office/test/data.xlsx";
		$sfile = readExcel($dir);
		$temp = $sfile['data'];
		$coms = [];
		foreach($temp as $k => $v){
			$coms[$v[1]] = $v;
		}

		for($i=0;$i<count($rs);$i++){
			$rs[$i] = array_slice($rs[$i],0,18);
			if(!empty($rs[$i][0])){
				$a0 = $rs[$i][0];
				$a1 = $rs[$i][1];
				$a2 = $rs[$i][2];
				$a3 = $rs[$i][3];
				$a4 = $rs[$i][4];
			}
			else{
				$rs[$i][0] = $a0;
				$rs[$i][1] = $a1;
				$rs[$i][2] = $a2;
				$rs[$i][3] = $a3;
				$rs[$i][4] = $a4;
			}
			$rs[$i][1] .= " ";
			$rs[$i][7] .= " ";
			$dis = 18 - count($rs[$i]);
			if($dis > 0 ){
				for($j= 18 - $dis;$j<18; $j++){
					$rs[$i][$j] = "";
				}
			}
		}

		$title = [
			'单位名称',
			'统一社会信用代码',
			'2020年工作居住证分配量',
			'2020年已办理工作居住证数量',
			'需求数量',
			'序号',
			'姓名',
			'身份证号码',
			'学位',
			'职称',
			'工作居住证申请方式',
			'现任职务',
			'参加工作时间',
			'在现单位缴纳社保年限',
			'近6个月平均月应税收入',
			'备注',
			' ',
			' ',

		];

		$rs = writeExcel($rs,$title,"new_1");
		file_put_contents("./content",json_encode(""));
		file_put_contents("./list",json_encode(""));
		echo '<a href="/xls/'.$rs.'">下载</a>';


	}


}

