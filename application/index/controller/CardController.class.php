<?php
namespace Home\Controller;
use Org\Util\Id;
use Think\Controller;
use Home\Model\CardModel;
use Org\Util\Deadline;
class CardController extends Controller {


	public function __construct(){
		parent::__construct();


		A("Permission")->check();
	}

	public function xlxs(){
		$card_number= I("card_number",'');
		$service = I("service","");
		$department = I("department","");
		$service_id = I("service_id","");

		$money_up= I("money_up","");
		$money_down= I("money_down","");
		$start_time= I("start_time","");
		$end_time= I("end_time","");



		$this->card_number = $card_number;
		$this->service = $service;
		$this->department = $department;
		$this->service_id = $service_id;

		$this->money_up = $money_up;
		$this->money_down = $money_down;
		$this->start_time = $start_time;
		$this->end_time = $end_time;




		$page = I("p",1);
		$pageSize = 15;


		$maps = [];
		$print = [];
		if(!empty($card_number)){
			$maps[] = " b.card_number = '$card_number'";
			$print['card_number'] = $card_number;
		}

		if(!empty($service)){
			$maps[] = " a.service = '$service'";
			$print['service'] = $service;
		}

		if(!empty($service_id)){
			$maps[] = " a.service_id = '$service_id'";
			$print['service_id'] = $service_id;
		}


		if(!empty($department)){
			$maps[] = " a.department = '$department'";
			$print['department'] = $department;
		}




		if(!empty($money_up)){
			$maps[] = " a.money <  $money_up";
			$print['money_up'] = $money_up;
		}

		if(!empty($money_down)){
			$maps[] = " a.money >=  $money_down";
			$print['money_down'] = $money_down;
		}

		if(!empty($start_time)){
			$print['start_time'] = $start_time;
		}

		if(!empty($end_time)){
			$print['end_time'] = $end_time;
		}








		$orders = [];
		$a = I("aa","");
		$b = I("bb","");
		$c = I("cc","");
		$d = I("dd","");


		if(!empty($a)){
			$orders[] = "b.create_time desc";
			$print['a']  = $a;
		}
		if(!empty($b)){
			$orders[] = "b.total_recharge desc";
			$print['b']  = $b;
		}
		if(!empty($c)){
			$orders[] = "b.money desc";
			$print['c']  = $c;
		}
		if(!empty($d)){
			$orders[] = "b.exp desc";
			$print['d']  = $d;
		}

		$this->print = http_build_query($print);




		$order = "order by ";
		if(!empty($orders))
			$order .=  implode(" ,  ",$orders);
		else{
			$order .= "a.create_time desc";
		}



		$start = ($page - 1) * $pageSize;

		if(count($maps) > 0){
			$where = implode(" and ",$maps);
			$count = M("user")->query("select count(*)  from dx_recharge_log a left join dx_card b on a.card_id = b.card_id where $where $order");
			$this->count = current(current($count));
			$lists = M("user")->query("select a.*,b.card_number,b.card_id from dx_recharge_log a left join dx_card b on a.card_id = b.card_id where $where $order");
		}
		else{
			$this->count = M("user")->count();
			$lists = M("user")->query("select a.*,b.card_number,b.card_id  from dx_recharge_log a left join dx_card b on a.card_id = b.card_id $order");
		}



		foreach($lists as $k => &$v){
			$v['money'] = sprintf("%.2f",$v['money']);
		}

		$this->lists = $lists;


		$lists = $this->lists;

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




		$rang = ['A','B','C','D','E','F'];
		//卡号 	时间 	金额 	接待人员 	接待部门 	员工工号

		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);

		foreach($rang as $k => $v){
			$objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(20);
		}



		$data['A1'] = '卡号';
		$data['B1'] = '充值时间';
		$data['C1'] = '金额';
		$data['D1'] = '接待人员';
		$data['E1'] = '接待部门';
		$data['F1'] =  '员工工号';


		$i = 2;

		foreach($lists as $k => $v){
			$data['A'. $i] = $v['card_number'];
			$data['B'. $i] = date("Y-m-d H:i:s",$v['create_time']);
			$data['C'. $i] = chunk_split(sprintf("%.2f",$v['money']));
			$data['D'. $i] = $v['service'];
			$data['E'. $i] = $v['department'];
			$data['F'. $i] = $v['service_id'];



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
		$objPHPExcel->getActiveSheet()->setTitle('会员列表');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		//$objPHPExcel->setActiveSheetIndex(0);


		// Save Excel 2007 file



		$file_name = "recharge_".time().rand(1,1000) .".xlsx";
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




	public function summary(){
		$start_time = I("start_time",date("Y-m-d",time()));
		$end_time = I("end_time",date("Y-m-d",time()));

		if(empty($start_time ))
			$start_time = date("Y-m-d",time() - 3600 * 24 * 31);
		if(empty($end_time))
			$end_time= date("Y-m-d",time());




		$this->start_time = $start_time;
		$this->end_time = $end_time;

		$type = I("type",1);
		if($type == 1){
			$start_time = date("Y",strtotime($start_time));
			$end_time = date("Y",strtotime($end_time));
			if($start_time == $end_time)
				$end_time +=1;

			$start_time .= "-1-1";
			$end_time .= "-1-1";
			$start_time = strtotime($start_time);
			$end_time = strtotime($end_time);
			$cards = M("card")->where("type =1 and create_time >= $start_time and create_time  < $end_time")->select();


			$data = [];
			$temp = [];
			foreach($cards as $k => $v){
				$key = date("Y年",$v['create_time']);
				if(isset($temp[$key])){
					$temp[$key] += 1;
				}
				else{
					$temp[$key] = 1;
				}
			}

			$dates = [];
			for($start = $start_time ;$start < $end_time ; $start += 3600 * 24 * 365){
				$key = date("Y年",$start);
				if(isset($temp[$key]))
					$dates[$key] = $temp[$key];
				else
					$dates[$key] = 0;
			}

		}
		if($type == 2){
			$start_time = strtotime($start_time);
			$end_time =strtotime($end_time);
			$end_time += 3600 * 24 * 31;
			$start_time = strtotime(date("Y-m",$start_time));
			$end_time = strtotime(date("Y-m",$end_time));

			$cards = M("card")->where("type =1 and create_time >= $start_time and create_time  < $end_time")->select();


			$data = [];
			$temp = [];
			foreach($cards as $k => $v){
				$key = date("Y-m",$v['create_time']);
				if(isset($temp[$key])){
					$temp[$key] += 1;
				}
				else{
					$temp[$key] = 1;
				}
			}

			$dates = [];
			for($start = $start_time ;$start < $end_time ; $start += 3600 * 24 * 31){
				$key = date("Y-m",$start);
				if(isset($temp[$key]))
					$dates[$key] = $temp[$key];
				else
					$dates[$key] = 0;
			}
		}
		if($type == 3){
			$start_time = strtotime($start_time);
			$end_time =strtotime($end_time) + 3600 * 24;
			$cards = M("card")->where("type =1 and create_time >= $start_time and create_time  < $end_time")->select();


			$data = [];
			$temp = [];
			foreach($cards as $k => $v){
				$key = date("Y-m-d",$v['create_time']);
				if(isset($temp[$key])){
					$temp[$key] += 1;
				}
				else{
					$temp[$key] = 1;
				}
			}

			$dates = [];
			for($start = $start_time ;$start < $end_time ; $start += 3600 * 24){
				$key = date("Y-m-d",$start);
				if(isset($temp[$key]))
					$dates[$key] = $temp[$key];
				else
					$dates[$key] = 0;
			}
		}


		$this->type = $type;
		$this->title = json_encode(array_keys($dates));
		$this->body = json_encode(array_values($dates));


		$this->total = array_sum($dates);


		$ch = ['1'=>'年',2=>'月',3=>'日'];
		$this->cn = $ch[$type];



		$this->display("Card/summary");

	}





	public function upload(){
		$card_id = I("get_id");
		$type = I("type");

		$uploads_dir = "./upload/";
		if(!empty($_FILES['cp']['name'])){
			$v = $_FILES['cp'];
			$tmp_name =  $v['tmp_name'];
			list($suf,$ex)  = explode(".",$v['name']);
			$suf = md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);
			$data['pic_p'] =  $suf;
			echo json_encode(array('t'=>1,'url'=>$suf));
		}

		if(!empty($_FILES['cf']['name'])){
			$v = $_FILES['cf'];
			$tmp_name =  $v['tmp_name'];
			list($suf,$ex)  = explode(".",$v['name']);
			$suf = md5($suf.time()).".".$ex;
			$rs=  move_uploaded_file($tmp_name, $uploads_dir."/".$suf);
			$data['pic_f'] =  $suf;
			echo json_encode(array('t'=>2,'url'=>$suf));
		}


	}




	public function recharge(){
		if(I("sub")){
			$service = I("service");
			$department = I("department");
			$card_id = I("get.card_id");
			$money = I("money");
			$service_id = I("service_id","");
			$exp = I("exp","");
			$isOld= I("isOld",0);



			$error = [];
			if(!is_numeric($money) || $money <= 0){
				$error['money']  = true;
			}

			if(empty($service)){
				$error['service'] = true;
			}
			if(empty($department)){
				$error['department'] = true;
			}




			if(!is_numeric($exp)){
				$error['exp'] = true;
			}


			if(!is_numeric($isOld)){
				$error['old'] = true;
			}




			if(empty($service_id) || !is_numeric($service_id)){
				$error['service_id'] = true;
			}




			if(!empty($error)){
				$this->department = $department;
				$this->service = $service;
				$this->card = M("card")->where("card_id = '$card_id'")->find();
				$user_id = $this->card['user_id'];
				$this->user = M("user")->where("user_id = '$user_id'")->find();
				$this->card_id = $card_id;
				$this->error = $error;
				$this->exp= $exp;
				$this->service_id= $service_id;
				$this->display("Card/recharge");
			}
			else{

				$time = time();
				$data['service'] = $service;
				$data['department'] = $department;
				$data['card_id'] = $card_id;
				$data['money'] = $money;
				$data['money_log'] = $money;
				$data['create_time'] = $time;
				$data['service_id'] = $service_id;



				$card = M("card")->where("card_id = '$card_id'")->find();
				$card_number = $card['card_number'];



				$present = A("present")->getPresent($money,$exp,$isOld);
				$reminder =  $card['money'] + $money + $present;


				$deadLine = new DeadLine();


				$data['card_number'] = $card_number;
				$data['period'] = $exp;
				$data['present'] = $present;
				$data['present_log'] = $present;
				$data['reminder'] = $reminder;
				$data['deadline'] = $deadLine->getDeadLine($time,$exp);
				$data['isold'] = $isOld;


				$rs = M("recharge_log")->add($data);

				$total_recharge = (double)$card['total_recharge']  + (double)$money;
				$money = (double)$card['money'] + (double)$money;
				$present = (double)$card['present'] + (double)$present;
				$rs = M("card")->where("card_id = '$card_id'")->save(['money'=>$money,'present'=>$present,'last_recharge_time'=>$time,"total_recharge"=>$total_recharge]);

				$this->redirect("Member/lists");
			}

		}
		else{
			$card_id = I("card_id");
			if(is_numeric($card_id)){
				$this->card = M("card")->where("card_id = '$card_id'")->find();
				$user_id = $this->card['user_id'];
				$this->user = M("user")->where("user_id = '$user_id'")->find();
				$this->card_id = $card_id;
				$this->available = D("rechargeLog")->getAvailable($card_id);
				$this->display("Card/recharge");

			}
		}
	}


	public function rechargeList(){
		$card_id = I("get.card_id");
		$page = I("p",1);

		$pageSize = 15;
		$start = ($page - 1) * $pageSize;


		$datas =  M("recharge_log")->where("card_id ='$card_id'")->order("recharge_log_id desc")->limit($start,$pageSize)->select();
		$count =  M("recharge_log")->where("card_id ='$card_id'")->count();


		foreach($datas as $k => &$v){
			$v['money'] = sprintf("%.2f",$v['money']);
		}

		$this->assign("datas",$datas);

		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());

		$this->display("Card/rechargeList");

	}



	public function search(){
		$card_number= I("card_number",'');
		$service_id = I("service_id","");

		$deadline= I("start_time",date("Y-m-d",time()));
		$period= I("period","");



		$this->card_number = $card_number;
		$this->service_id = $service_id;

		$this->deadline = $deadline;
		$this->period= $period;




		$page = I("p",1);
		$pageSize = 15;


		$maps = [];
		$print = [];
		if(!empty($card_number)){
			$maps[] = " b.card_number = '$card_number'";
			$print['card_number'] = $card_number;
		}


		if(!empty($service_id)){
			$maps[] = " a.service_id = '$service_id'";
			$print['service_id'] = $service_id;
		}



		if(!empty($deadline)){
			$down =  strtotime($deadline);
			$up =  $down + 3600 * 24;
			$maps[] = "a.deadline  <= $up";
			$maps[] = "a.deadline  >= $down";

		}

		if(!empty($period)){
			$maps[] = "a.period = '$period'";
		}




		$orders = [];
		$a = I("aa","");
		$b = I("bb","");
		$c = I("cc","");
		$d = I("dd","");




		if(!empty($a)){
			$orders[] = "b.create_time desc";
			$print['a']  = $a;
		}
		if(!empty($b)){
			$orders[] = "b.total_recharge desc";
			$print['b']  = $b;
		}
		if(!empty($c)){
			$orders[] = "b.money desc";
			$print['c']  = $c;
		}

		$this->print = http_build_query($print);




		$order = "order by ";
		if(!empty($orders))
			$order .=  implode(" ,  ",$orders);
		else{
			$order .= "a.deadline desc";
		}



		$start = ($page - 1) * $pageSize;

		if(count($maps) > 0){
			$where = implode(" and ",$maps);
			$count = M("user")->query("select count(*)  from dx_recharge_log a left join dx_card b on a.card_id = b.card_id where $where $order");
			$count = current(current($count));
			$lists = M("user")->query("select a.*,b.card_number,b.card_id,b.total_recharge from dx_recharge_log a left join dx_card b on a.card_id = b.card_id where $where $order limit $start,$pageSize");


		}
		else{
			$count = M("recharge_log")->count();
			$lists = M("user")->query("select a.*,b.card_number,b.card_id,b.total_recharge from dx_recharge_log a left join dx_card b on a.card_id = b.card_id $order limit $start,$pageSize");
		}




		$total= array_sum(array_column($lists,'money')) + array_sum(array_column($lists,"present"));

		foreach($lists as $k => &$v){
			$v['money'] = sprintf("%.2f",$v['money']);
			$v['total'] = sprintf("%.2f",(double)$v['money'] + (double)$v['present']);
		}



		$this->total = sprintf("%.2f",$total);




		foreach($lists as $k => &$v){
			$v['money'] = sprintf("%.2f",$v['money']);
			$v['total'] = sprintf("%.2f",$v['money']+$v['present']);
		}

		$this->lists = $lists;

		$this->count = $count;

		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());
		$this->display("Card/search");
	}





	public function pin(){
		$user_id = I("user_id",'');
		if(!is_numeric($user_id)){
			return false;
		}

		M("user")->where("user_id= '$user_id'")->save(array("type"=>0));
		M("card")->where("user_id= '$user_id'")->save(array("type"=>0));
		echo json_encode(1);
		$this->display("search");
	}



	public function lists(){

		$username = I("username","");
		$card_number = I("card_number","");

		if(!empty($username)){
			$username = addslashes($username);
			$maps[] = " a.username like '%$username%'";
		}
		if(!empty($card_number) && is_numeric($card_number)){
			$maps[] = " b.card_number like '%$card_number%'";
		}
		#$maps[] = " b.type =1 ";

		$where = implode(" and ",$maps);




		$page = I("p",1);
		$pageSize = 15;

		if(count($maps) > 0){
			$count = M("user")->query("select count(*)  from dx_user a left join dx_card b on a.user_id = b.user_id where $where");
		}
		else{
			$count = M("user")->query("select count(*)  from dx_user a left join dx_card b on a.user_id = b.user_id");
		}
		$count = current(current($count));
		$start = ($page - 1)  * $pageSize ;

		if(count($maps)>0){
			$lists = M("user")->query("select a.*,b.* from dx_user a left join dx_card b on a.user_id = b.user_id where $where order by a.user_id desc  limit $start,$pageSize");
		}
		else{
			$lists = M("user")->query("select a.*,b.* from dx_user a left join dx_card b on a.user_id = b.user_id order by a.user_id desc  limit $start,$pageSize");
		}


		foreach($lists as $k => &$v){
			$card_id = $v['card_id'];
			$v['recharge'] = M("recharge_log")->where("card_id = '$card_id'")->sum("money");
			$v['recharge'] = sprintf("%.2f",$v['recharge']);
			$v['money'] = sprintf("%.2f",$v['money']);
		}




		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());
		$this->assign('datas', $lists);
		$this->display("Card/list");
	}



	private function genImg($data,$filename = null){
		$suf = "jpg";
		if(strpos($data,"jpg") !== false){
			$suf = "jpg";
		}
		if(strpos($data,"png") !== false){
			$suf = "png";
		}

		$base_data = str_replace('data:image/'.$suf.';base64,', '', $data);
		$uploadDir = "./upload/";


		if(is_null($filename)){
			$filename = time().rand(100,999).'.'.$suf;
		}

		$rs = file_put_contents($uploadDir.$filename,base64_decode($base_data));
		return $filename;

	}

	public function open(){
		if(isset($_POST['sub'])){

			#var_dump($_POST); exit;
			$uploads_dir = realpath("./upload");
			$files = [];
			$error = [];
			$uploads_dir = "./upload/";



			$username = I('username');
			$telephone = I("telephone");
			$card_type = I("card_type","1");
			$isOld = I("isOld","0");
			$card = I("card");
			$id = I("id");
			$money = I("money",0);
			$exp = I("exp",1);
			$username = addslashes($username);
			$telephone = addslashes($telephone);
			$id = addslashes($id);




			if(empty($username)){
				$error['username'] = '用户名不能为空';
			}

			if(strlen($card)!=7){
				$error['card_number'] = '卡号为７位';
			}
			if(!is_numeric($card)){
				$error['card_number'] = '卡号必须为数字';
			}
			if(M("card")->where("card_number='$card'")->find()){
				$error['card_number'] = '卡号已存在';
			}

			if($money > 10000000){
				$error['money'] = '金额不能超过1千万';
			}


			if(!is_numeric($isOld)){
				$error['old'] = '请重新选择';
			}



			if($money ==  0){
				$error['money'] = '请充值';
			}

			if($money < 0){
				$error['money'] = '金额须为正数';
			}



			if($card_type == 1){
				$idObj = new Id();
				if(!$idObj->validateIDCard($id)){
					$error['id'] = '身份证号不正确';
				}
				/*
				if(M("user")->where("id='$id'")->find()){
					#$error['id'] = true;
				}
				 */
			}



			if(strlen($telephone) != 11 || !is_numeric($telephone)){
				$error['telephone']= '手机号码不正确';
			}

			if(!is_numeric($money)){
				$error['money'] = '金额须为小写数字';
			}

			if(M("user")->where("telephone='$telephone'")->find()){
				$error['telephone'] = '手机号已存在';
			}


			$cp  = I("cp","");
			$cf = I("cf","");
			if(!empty($cp)){
				$data['pic_p'] =  $this->genImg($cp);
			}
			else{
				#$error['card_p'] = '缺少身份证正面图';
			}

			if(!empty($cf)){
				$data['pic_f'] =  $this->genImg($cf);
			}
			else{
				#$error['card_f'] = '缺少身份证背面图';
			}



			if(!empty($error)){
				$this->exp = $exp;
				$this->card = $card;
				$this->username= $username;
				$this->money= $money;
				$this->id= $id;
				$this->telephone= $telephone;
				$this->assign('error',$error);
				$this->error = $error;
				$this->display("Card/open");
			}
			else{
				$data['username'] = $username;
				$data['telephone'] = $telephone;
				$data['money'] = $money;
				$data['id_type'] = $card_type;
				$data['id'] = $id;

				$present = A("present")->getPresent($money,$exp,$isOld);

				$user_id  = M("user")->add($data);
				$time = time();
				$card_data = [
					'card_number' => $card,
					'money' => $money,
					'present' => $present,
					'create_time' => $time,
					'user_id' => $user_id,
					'last_recharge_time'=> $time,
					'total_recharge' => $money,

				];


				$card_id = M("card")->add($card_data);

				$deadLine = new Deadline();

				$reminder = $money + $present;


				if($money != 0){
					M("recharge_log")->add([
						'money' => $money,
						'money_log' => $money,
						'create_time' => $time,
						'department'=> '首充',
						'service'=> '首充',
						'service_id'=> '首充',
						'card_id' => $card_id,
						'card_number' => $card,
						'period' => $exp,
						'present' => $present,
						'present_log' => $present,
						'reminder' => $reminder,
						'deadline' => $deadLine->getDeadLine($time,$exp),
						'isold' => $isOld,

					]);
				}


				$this->ok = 1;
				$this->display("Card/open");

			}

		}
		else{
			$this->display("Card/open");
		}

	}

	public function index(){
	}


}
