<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Deadline;
use Home\Extend\Refund;
class RecordController extends Controller {
	private $subMoney;
	private $subPresent;
	private $hasCrash;
	private $docId;


	public function __construct(){
		parent::__construct();
		A("Permission")->check();
		$this->hasCrash = 0;
		$this->subMoney =0;
		$this->subPresent=0;

	}

	public function xlxs(){
		$card_id = I("card_id");
		$start_time = I("start_time");
		$end_time = I("end_time");
		$card_number = I("card_number");
		$username = I("username");
		$money_up = I("money_up");
		$money_down = I("money_down");

		$maps = [];

		$page =I("p",1);
		$pageSize = 15;

		$area= I("area");
		$location = I("location");
		if($area != "请选择园区" && !empty($area)){
			$maps[] = " area = '$area' ";
		}
		if($location != '请选择营业点' && !empty($location)){
			$maps[] = " location = '$location' ";
		}


		if(is_numeric($card_number)){
			$maps[] = " card_number = '$card_number' ";
		}

		if(!empty($start_time)){
			$temp = strtotime($start_time) + 1 ;
			$maps[] = " create_time >=". $temp . " " ;
		}
		if(!empty($end_time)){
			$temp = strtotime($end_time) + 3600 * 24 -1;
			$maps[] = " create_time <".$temp . " " ;
		}





		if(!empty($username)){
			$maps[] = " username like '%$username%'";
		}



		if(!empty($money_up)){
			$maps[] = " spend <  $money_up";
			$print['money_up'] = $money_up;
		}

		if(!empty($money_down)){
			$maps[] = " spend >=  $money_down";
			$print['money_down'] = $money_down;
		}

		if(!empty($card_id)){
			$maps[] = " card_id= '$card_id'";
		}

		$this->card_iid = $card_id;
		$this->start_time = $start_time;
		$this->end_time = $end_time;
		$this->card_number = $card_number;
		$this->username= $username;


		if(count($maps) > 0){
			$list = M("card_log")->where($maps)->order("create_time desc")->select();
		}
		else{
			$list = M("card_log")->where($maps)->order("create_time desc")->select();
		}



		$this->total = 0;
		foreach($list  as $k => &$v){
			$this->total += $v['spend'];
			$v['spend'] =  sprintf("%.2f", $v['spend']);
		}
		$total = sprintf("%.2f",$this->total);
		$lists = &$list;
		unset($v);



		include "./ThinkPHP/Library/Org/Util/PHPExcel/Classes/PHPExcel.php";

		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		/** Include PHPExcel */

		// Create new PHPExcel object
		$objPHPExcel = new \PHPExcel();

		// Set document properties
		/*
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
			->setLastModifiedBy("Maarten Balliauw")
			->setTitle("PHPExcel Test Document")
			->setSubject("PHPExcel Test Document")
			->setDescription("Test document for PHPExcel, generated using PHP classes.")
			->setKeywords("office PHPExcel php")
			->setCategory("Test result file");
		 */




		$rang = ['A','B','C','D','E','F','G'];

		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);

		foreach($rang as $k => $v){
			$objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(20);
			#$objPHPExcel->getActiveSheet()->getColumnDimension($v)->setAutoSize(true);
		}



		$data['A3'] = '序号';
		$data['B3'] = '时间';
		$data['C3'] = '用户名';
		$data['D3'] = '费用';
		$data['E3'] = '园区';
		$data['F3'] =  '营业点';
		$data['G3'] =  '备注';

		$objPHPExcel->setActiveSheetIndex(0) ->setCellValue("A1", "总计");
		$objPHPExcel->setActiveSheetIndex(0) ->setCellValue("B1", $total."元");
		$i = 4;
		foreach($lists as $k => $v){
			$data['A'. $i] = $v['card_log_id'];
			$data['B'. $i] = date("Y-m-d H:i:s",$v['create_time']);
			$data['C'. $i] = $v['username'];
			$data['D'. $i] = chunk_split(sprintf("%.2f",$v['spend']));
			$data['E'. $i] = $v['area'];
			$data['F'. $i] = $v['location']."";
			$data['G'. $i] = $v['comment']."";
			$i++;
		}


		foreach($data as $k => $v){
			$objPHPExcel->setActiveSheetIndex(0) ->setCellValue($k, $v);
		}





		// Miscellaneous glyphs, UTF-8

			/*
		$objPHPExcel->getActiveSheet()->setCellValue('A8',"Hello\nWorld");
		$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(-1);
		$objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setWrapText(true);
			 */


		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('消费列表');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		//$objPHPExcel->setActiveSheetIndex(0);


		// Save Excel 2007 file



		$file_name = time().rand(1,1000) .".xlsx";
		$file = WEB_DIR."/xls/" .  $file_name;
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($file);

		/*
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save(str_replace('.php', '.xls', __FILE__));
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		 */

		echo "<html><body>
			注意查看下载任务
			<script>
window.location.href = '/xls/$file_name';
</script>
			</body></html>";



	}


	public function add(){
		$card_id = I("get.card_id");
		$refundObj = new Refund();
		if(is_numeric($card_id)){
			$this->card_id = $card_id;
			$card = M("card")->where("card_id ='$card_id'")->find();
			$card['available'] = D("rechargeLog")->getAvailable($card_id);
			$this->card = $card;
		}
		else{
			$this->redirect("/Card/lists");
		}

		if(I("sub")){
			$area = I("area","");
			$location = I("location","");
			$money = I("money",0);
			$comment = I("text");
			$service = I("service");
			$department = I("department");

			if(empty($service)){
				$error['service'] = true;
			}
			if(empty($department)){
				$error['department'] = true;
			}

			$service = addslashes($service);
			$department = addslashes($department);

			if($area == "请选择园区")
				$error['area'] = true;

			if($location == "请选择营业点")
				$error['location'] = true;

			if(!is_numeric($money) || $money <=0){
				$error['money']  = true;
			}


			if((double)$money > D("rechargeLog")->getAvailable($card_id)){
				$error['money']  = true;
			}








			if(!empty($error)){
				$this->money = $money;
				$this->text = $comment;
				$this->service = $service;
				$this->department = $department;

				$this->assign("error",$error);
				$this->display("Record/add");
			}
			else{
				$data['area'] = $area;
				$data['location'] = $location;

				$data['service'] = $service;
				$data['department'] = $department;

				$data['spend'] = $money;
				$data['create_time'] = time();
				$data['comment'] = $comment;
				$data['card_id'] = $card_id;
				$data['type'] = 1;
				$card  = M("card")->where("card_id = '$card_id'")->find();
				$data['card_number'] = $card['card_number'];
				$data['user_id'] = $card['user_id'];
				$user_id = $data['user_id'];

				$this->subtract($card_id,$money);
				$data['username'] = M("user")->where("user_id = '$user_id'")->getField("username");
				M("card")->where("card_id = '$card_id'")->save(['money'=>$this->card['money']-$this->subMoney,'present'=>$this->card['present'] - $this->subPresent]);
				M("card_log")->add($data);
				$this->redirect("/Member/lists");
			}

		}
		else{
			$this->card_id = $card_id;
			$this->display("Record/add");
		}
	}





	private function subtract($card_id,$money){
		$logs  = M("recharge_log")->where("card_id = '$card_id' and type =1")->order("create_time asc")->select();
		foreach($logs as $k => $v){
			$recharge_log_id = $v['recharge_log_id'];

			if($v['money'] > 0 || $v['present'] > 0){
				$money = $this->forsub($recharge_log_id,$money,$v['money'],$v['present'],$v['deadline'],$v['period'],$v['isold']);
				if(!$money){
					return true;
				}
			}

		}


	}

	private function forsub($id,$spend,$money,$present,$deadline,$period,$isOld){
		$locMoney = 0 ;
		if($deadline >= time()){
			//未到期

			if($spend >= $money){
				$this->subMoney += $money;
				$locMoney += $money;
				$spend -= $money;
				$money = 0;

			}
			else if($spend < $money){
				$this->subMoney += $spend;
				$locMoney +=$spend;
				$money -= $spend;
				$spend =0;
			}
			else{
			}


			$subPresent = A("present")->getPresent($locMoney,$period,$isOld);
			$this->subPresent += $subPresent;

			$present = $present - $subPresent;


			if($present < 0 ){
				$data = [
					'money'=>$money,
					'present'=>0,
					'type' => 0,
				];
			}
			else{
				$data = [
					'money'=>$money,
					'present' => $present,
				];
			}

			M("recharge_log")->where("recharge_log_id = '$id'")->save($data);

			return $spend;


		}
		else{
			//到期
			if($spend >= $money){
				$this->subMoney += $money;
				$spend -= $money;
				$money = 0;

			}
			else if($spend < $money){
				$this->subMoney += $spend;
				$money -= $spend;
				$spend =0;
			}
			else{
			}

			$data['money'] = $money;
			M("recharge_log")->where("recharge_log_id = '$id'")->save($data);

			if($spend == 0)
				return $spend;

			
			if($spend >= $present){
				$this->subPresent += $present;
				$spend -= $present;
				$present = 0;

			}
			else if($spend < $present){
				$this->subPresent += $spend;
				$present -= $spend;
				$spend =0;
			}
			else{
			}


			if($present == 0 && $money == 0){
				$data = [
					'money'=>0,
					'present' => 0,
					'type' => 0,
				];
			}
			else{
				$data = [
					'money'=>$money,
					'present' =>$present 
				];
			}


			M("recharge_log")->where("recharge_log_id = '$id'")->save($data);

			return $spend;

		}
	}


	public function dlist(){
		$card_id = I("card_id");
		$start_time = I("start_time");
		$end_time = I("end_time");
		$card_number = I("card_number");
		$username = I("username");

		$money_up = I("money_up");
		$money_down = I("money_down");

		$maps = [];

		$page =I("p",1);
		$pageSize = 15;

		$area= I("area");
		$location = I("location");
		$print=[];
		if($area != "请选择园区" && !empty($area)){
			$maps[] = " area = '$area' ";
			$print['area'] = $area;;
		}
		if($location != '请选择营业点' && !empty($location)){
			$maps[] = " location = '$location' ";
			$print['location'] = $location;
		}


		if(is_numeric($card_number)){
			$maps[] = " card_number = '$card_number' ";
			$print['card_number'] = $card_number;
		}

		if(!empty($start_time)){
			$temp = strtotime($start_time) + 1 ;
			$maps[] = " create_time >=". $temp . " " ;
			$print['start_time'] = $start_time;
		}
		if(!empty($end_time)){
			$temp = strtotime($end_time) + 3600 * 24 -1;
			$maps[] = " create_time <".$temp . " " ;
			$print['end_time'] = $end_time;
		}

		if(!empty($username)){
			$maps[] = " username like '%$username%'";
			$print['username'] = $username;
		}




		if(!empty($money_up)){
			$maps[] = " spend <  $money_up";
			$print['money_up'] = $money_up;
		}

		if(!empty($money_down)){
			$maps[] = " spend >=  $money_down";
			$print['money_down'] = $money_down;
		}








		$this->print = http_build_query($print);

		$this->card_iid = $card_id;
		$this->start_time = $start_time;
		$this->end_time = $end_time;
		$this->card_number = $card_number;
		$this->money_up = $money_up;
		$this->money_down = $money_down;
		$this->username= $username;


		if(count($maps) > 0){
			$count = M("card_log")->where($maps)->count();
			$list = M("card_log")->where($maps)->page($page,$pageSize)->order("card_log_id desc")->select();
		}
		else{
			$count = M("card_log")->where($maps)->count();
			$list = M("card_log")->where($maps)->page($page,$pageSize)->order("card_log_id desc")->select();
		}

		$this->total = 0;
		foreach($list  as $k => &$v){
			$this->total += $v['spend'];
			$v['spend'] =  sprintf("%.2f", $v['spend']);
		}
		$this->total = sprintf("%.2f",$this->total);

		$this->lists = $list;
		$this->card = M("card")->where("card_id = '$card_id'")->find();
		$user_id = $this->card['user_id'];
		$this->user = M("user")->where("user_id= '$user_id'")->find();




		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());
		$this->display("Record/index");

	}


	public function doPrint(){
	}


	private function getAreaDetail(){
		$arr_province = ['大兴东酒店', '大兴东游乐场', '大兴东电玩城', '大兴东演艺中心', '大兴东会议中心', '大兴东会展中心', '大兴东生产中心'];
		#$arr_city = [ ["请选择营业点"], ['盛世中华酒店'], ['餐饮', '商超', '门票'], ['游戏币消费', '其他产品消费'], ['门票', '餐饮', '商超'], ['会议场馆', '餐饮',], ['一层消费', '二层消费', '三层消费', '四层消费'], ['酒', '水', '饮料', '土特产', '综合'], ]; 

		$area = [];
		foreach($arr_province as $k => $v){
			$area[$v] = 0;
		}

		$recordList = M("card_log")->select();
		foreach($recordList as $k => $v){
			$key = $v['area'];
			if(isset($area[$key])){
				$area[$key] += $v['spend'];
			}
		}

		return $area;


	}

	private function getMonthDetail(){
		$curYear = date("Y");
		$data = [];
		$recordList = M("card_log")->select();
		foreach($recordList as $k => $v){
			list($year,$month) = explode(",",date("Y,m",$v['create_time']));
			$key = $year.'-'.$month;
			if(isset($data[$key])){
				$data[$key] += $v['spend'];
			}
			else
				$data[$key] = 0.00;
		}

		$dateFuc = function(){
			$data = [];
			$cur = date("Y-m",time());
			for($i=0;$i<12;$i++){
				$time = strtotime($cur) - $i;
				$cur  = date("Y-m",$time);
				$data[$cur] = 0.00;
			}
			return $data;
		};
		$dates = $dateFuc();
		foreach($dates as $k => &$v){
			if(isset($data[$k]))
				$dates[$k] = $data[$k];
		}

		return array_reverse($dates);


	}

	public function getServiceDetail(){
		$recordList = M("card_log")->select();
		$data = [];
		foreach($recordList as $k => $v){
			if(isset($data[$v['service']])){
				#var_dump($v['service'],$data[$v['service']],$v['spend']);
				$data[$v['service']] += (double)$v['spend'];
			}
			else{
				if(empty($v['service']))
					$data['未命名'] = $v['spend'];
				else
					$data[$v['service']] = $v['spend'];
			}
		}
		return $data;
	}





	public function summary(){
		$this->type = I("type",1);
		if(!is_numeric($this->type))
			$this->type = 1;

		if($this->type == 1){
			$data = $this->getAreaDetail();
		}
		if($this->type == 2){
			$data = $this->getMonthDetail();
		}
		if($this->type == 3){
			$data = $this->getServiceDetail();
		}
		$this->total = 0;
		foreach($data as $k => &$v){
			$this->total += $v;
			$v = sprintf("%.2f",$v);
		}
		$this->total = sprintf("%.2f",$this->total);






		$title = array_keys($data);
		$body  = array_values($data);

		$this->title = json_encode($title);
		$this->body = json_encode($body);


		$this->display("Record/summary");

	}







	public function search(){
		$card_id = I("get.card_id");
		$start_time = I("start_time");
		$end_time = I("end_time");

		$money_up = I("money_up");
		$money_down = I("money_down");

		$maps = [];

		$page =I("p",1);
		$pageSize = 15;


		$area= I("area");
		$location = I("location");
		$print=[];
		if($area != "请选择园区" && !empty($area)){
			$maps[] = " area = '$area' ";
			$print['area'] = $area;;
		}
		if($location != '请选择营业点' && !empty($location)){
			$maps[] = " location = '$location' ";
			$print['location'] = $location;
		}



		if(!empty($start_time)){
			$temp = strtotime($start_time) + 1 ;
			$maps[] = " create_time >=". $temp . " " ;
			$print['start_time'] = $start_time;
		}
		if(!empty($end_time)){
			$temp = strtotime($end_time) + 3600 * 24 -1;
			$maps[] = " create_time <".$temp . " " ;
			$print['end_time'] = $end_time;
		}




		if(!empty($username)){
			$maps[] = " username like '%$username%'";
			$print['username'] = $username;
		}





		if(!empty($money_up)){
			$maps[] = " spend <  $money_up";
			$print['money_up'] = $money_up;
		}

		if(!empty($money_down)){
			$maps[] = " spend >=  $money_down";
			$print['money_down'] = $money_down;
		}


		$maps['card_id'] = $card_id;
		$print['card_id'] = $card_id;




		$this->print = http_build_query($print);

		$this->card_id = $card_id;
		$this->start_time = $start_time;
		$this->end_time = $end_time;
		$this->card_number = $card_number;
		$this->money_up = $money_up;
		$this->money_down = $money_down;
		$this->username= $username;


		if(count($maps) > 0){
			$count = M("card_log")->where($maps)->count();
			$list = M("card_log")->where($maps)->page($page,$pageSize)->order("card_log_id desc")->select();
		}
		else{
			$count = M("card_log")->where($maps)->count();
			$list = M("card_log")->where($maps)->page($page,$pageSize)->order("card_log_id desc")->select();
		}




		if(!empty($start_time)){
			$maps[] = " create_time >=". strtotime($start_time). " " ;
		}
		if(!empty($end_time)){
			$maps[] = " create_time <". strtotime($end_time). " " ;
		}



		if(is_numeric($money_down)){
			$maps[] = " spend  >= $money_down";
		}

		if(is_numeric($money_up)){
			$maps[] = " spend  < $money_up";
		}



		$money = M("recharge_log")->where("card_id = '$card_id'")->sum("money");
		$present = M("recharge_log")->where("card_id = '$card_id'")->sum("present");
		$this->money = $money + $present;




		$this->total = 0.00;
		foreach($list as $k => &$v){
			$this->total += $v['spend'];
		}

		$this->lists = $list;
		$this->card = M("card")->where("card_id = '$card_id'")->find();
		$user_id = $this->card['user_id'];
		$this->user = M("user")->where("user_id= '$user_id'")->find();
		$this->card_id = $card_id;

		$this->available =  D("rechargeLog")->getAvailable($card_id);

		$this->cost= M("card_log")->where($maps)->sum("spend");
		if(empty($this->cost))
			$this->cost = sprintf("%.2f",$this->cost);
		$this->total = $this->cost + $this->card['money'];

		$this->total = sprintf("%.2f",$this->total);

		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());
		$this->display("Record/detail");
	} 


	public function export(){
		$uploads_dir = "./upload/";


		if(!empty($_FILES['file']['name'])){
			$v = $_FILES['file'];
			$tmp_name =  $v['tmp_name'];
			list($suf,$ex)  = explode(".",$v['name']);
			$suf = "up_".md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);


			include "./ThinkPHP/Library/Org/Util/PHPExcel/Classes/PHPExcel.php";

			include "./ThinkPHP/Library/Org/Util/PHPExcel/Classes/PHPExcel/IOFactory.php";
			include "./ThinkPHP/Library/Org/Util/PHPExcel/Settings.php";
			\PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);


			$file = $uploads_dir . $suf;

			$objPHPExcel = \PHPExcel_IOFactory::load($file);


			$height = $rowCnt = $objPHPExcel->getSheet(0)->getHighestRow(); 

			if($height < 2){
				return false;
			}


			$key = ['A','B','C','D','E','F','G','H','I'];

			$this->docId = $this->addDoc();

			$data= [];
			$em = 0;
			for($i=2;$i<=$height;$i++){
				$temp = [];
				foreach($key as $k => $v){
					$temp[]=  $objPHPExcel->getSheet(0) ->getCell($v.$i)->getFormattedValue();
				}

				if(!empty(trim($temp[0]))){


					$this->ins($temp);
				}
				else{
					$em++;
				}
			}
			$this->ok = 1;
			if($this->hasCrash !==0){
				$this->assign("crash",true);
			}
			$this->setDoc($this->docId,$height-1-$em,$this->hasCrash);


		}

		$page =I("p",1);
		$pageSize = 15;

		$this->lists= M("export_document")->page($page,$pageSize)->order("create_time desc")->select();

		$count = M("export_document")->count();

		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());
		$this->display("export");

	}

	private function ins($data){
		$card_number = $data[0];
		$username = $data[1];
		$money = $data[2];
		$start_time = $data[3];
		$period = $data[4];
		$service_id= $data[5];
		$service = $data[6];
		$department  = $data[7];
		$isOld= $data[8];




		$products = C("products");
		$key  = "";
		foreach($products as $k => $v){
			if(trim($v) == $period)
				$key = $k;
		}

		if(!is_numeric($key)){
			$this->exportLog($data,3);  // 产品不匹配
			return false;
		}
		$period = $key;

		$card = M("card")->where("card_number= '$card_number'")->find();
		if($card){
			$card_id = $card['card_id'];
			$present = A("present")->getPresent($money,$period,$isOld);
			$deadLine = new Deadline();
			$data['card_number'] = $card_number;
			$data['period'] = $period;
			$data['present'] = $present;
			$data['present_log'] = $present;
			$data['reminder'] = $reminder;
			$data['deadline'] = $deadLine->getDeadLine(strtotime($start_time),$period);



			$data['service'] = $service;
			$data['department'] = $department;
			$data['card_id'] = $card_id;
			$data['money'] = $money;
			$data['money_log'] = $money;
			$data['create_time'] = strtotime($start_time);
			$data['service_id'] = $service_id;
			$data['isold'] = $isOld;



			$card_number = $card['card_number'];


			$reminder =  $card['money'] + $money + $present;


			$data['present'] = $present;
			$data['reminder'] = $reminder;


			$rs = M("recharge_log")->add($data);
			if(!$rs){
				$this->exportLog($data,2); //允值失败
				return false;
			}

			$total_recharge = (double)$card['total_recharge']  + (double)$money;

			$money = (double)$card['money'] + (double)$money;
			$present = (double)$card['present'] + (double)$present;


			if($data['create_time'] > $start_time){
				$time = $data['create_time'];
			}
			else
				$time = $start_time;
			$rs = M("card")->where("card_id = '$card_id'")->save(['money'=>$money,'present'=>$present,'last_recharge_time'=>$time,"total_recharge"=>$total_recharge]);

			#$this->exportLog($data,0); //导入成功

		}
		else{
			$this->exportLog($data,1); //不存在此卡
		}




	}


	public function exportList(){
		$document_id = I("document_id");
		$page = I("get.p");

		$pageSize = 15;

		$this->lists = M("export_log")->where("type != 0 and document_id = '$document_id'")->page($page,$pageSize)->select();
		$count = M("export_log")->where("type != 0 and document_id = '$document_id'")->page($page,$pageSize)->order("export_log_id desc")->count();

		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());
		$this->display("export_log_list");
	}


	private function exportLog($data,$type=0){
		$this->hasCrash+=1;
		$data['card_number'] = $data[0];
		$data['username'] = $data[1];
		$data['money'] = $data[2];
		$data['start_time'] = $data[3];
		$data['period'] = $data[4];
		$data['service_id'] =  $data[5];
		$data['service'] = $data[6];
		$data['department'] = $data[7];
		$data['type'] = $type;
		$data['document_id'] = $this->docId;
		M("export_log")->add($data);

	}

	public function docKnow(){
		$id  = I("document_id");
		M("export_document")->where("document_id='$id'")->save(['status'=>1]);
		M("export_log")->where("document_id ='$id'")->save(['isKnow'=>1]);
		echo "1";
	}

	public function know(){
		$id  = I("id");
		$rs = M("export_log")->where("export_log_id ='$id'")->save(['isKnow'=>1]);
		echo "1";
	}
	public function allKnow(){
		M("export_log")->save(['isKnow'=>1]);
	}

	public function addDoc(){
		return M("export_document")->add(['create_time'=>time(),'adminname'=>$_SESSION['user']['uname']]);
	}

	public function setDoc($docId,$total,$wrong){
		M("export_document")->where("document_id = '$docId'")->save(['total'=>$total,'success'=>$total-$wrong]);
	}






	public function refundLog(){
		$card_id = I("get.card_id");
		$page = I("p",1);

		$pageSize = 15;
		$start = ($page - 1) * $pageSize;


		$datas =  M("refund_log")->where("card_id ='$card_id'")->order("create_time desc")->page($page,$pageSize)->select();
		$count =  M("refund_log")->where("card_id ='$card_id'")->count();


		foreach($datas as $k => &$v){
			$v['money'] = sprintf("%.2f",$v['money']);
		}

		$this->assign("datas",$datas);

		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());

		$this->display("refundList");



	}



}
