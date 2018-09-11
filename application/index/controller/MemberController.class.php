<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\CardModel;
use Home\Extend\Refund;
use Org\Util\Deadline;
class MemberController extends Controller {

	private $rechargeLogs =[];
	private $error= false;
	private $subMoney;
	private $subPresent;
	private $subFine;
	private $refundObj;
	private $deadLineObj;


	public function __construct(){
		parent::__construct();
		$this->subMoney =0;
		$this->subPresent=0;
		$this->subFine=0;


		if(isset($_SESSION['member'])){
		}
		else if(in_array(ACTION_NAME,['search','result'])){
		}
		else{
			A("permission")->check();
		}
		$date = date("Y-m-d",time());
		$ip = $this->getIp();
		$total = M("login_record")->where("date = '$date' and ip='$ip'")->count();
		if($total >=5){
			unset($_SESSION['member']) ;
			$this->error= "错误次数过多,IP冻结一天";
		}


	}

	public function xlxs(){

		$telephone = I("telephone","");
		$username = I("username","");
		$type = I('type',-1);





		$orders = [];
		$print = [];

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




		$order = "order by ";
		if(!empty($orders))
			$order .=  implode(" ,  ",$orders);
		else{
			$order .= "a.user_id desc ";
		}







		if(is_numeric($telephone)){
			$maps[] = " a.telephone like '%$telephone%' ";
			$print['telephone'] = $telephone;
		}

		if(!empty($username)){
			$maps[] = " a.username like '%$username%'";
			$print['username'] = $username;
		}

		if($type != "-1" && is_numeric($type)){
			$maps[] = " a.type = $type ";
			$print['type'] = $type;

		}




		$start = ($page - 1) * $pageSize;
		if(count($maps) > 0){
			if($page !=1){

				$start = 0;
			}

			$tempMaps = $maps;
			foreach($tempMaps as $k => &$v){
				$v= str_replace("a.","",$v);
			}
			$where = implode(" and ",$maps);
			$lists = M("user")->query("select a.*,b.*  from dx_user a left join dx_card b on a.user_id = b.user_id where $where $order");
		}
		else{
			$lists = M("user")->query("select a.*,b.*  from dx_user a left join dx_card b on a.user_id = b.user_id $order");
		}



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




		$rang = ['A','B','C','D','E','F','G','H','J','K'];
		//卡号	姓名	手机号	开卡日期 	余额 	最后充值日期 	有效期/月 

		$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(30);

		foreach($rang as $k => $v){
			$objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(20);
		}



		$data['A1'] = '卡号';
		$data['B1'] = '姓名';
		$data['C1'] = '手机号';
		$data['D1'] = '开卡日期';
		$data['E1'] = '帐户总额';
		$data['F1'] =  '最后充值日期';
		$data['G1'] =  '证件类型';
		$data['H1'] =  '证件号';
		$data['J1'] =  '状态';


		$i = 2;

		$ids= ['身份证号','驾照','军官证','港澳通行证','大陆通行证','签证','护照'];
		foreach($lists as $k => $v){
			$data['A'. $i] = $v['card_number'];
			$data['B'. $i] = $v['username'];
			$data['C'. $i] = $v['telephone'];
			$data['D'. $i] = date("Y-m-d H:i:s",$v['create_time']);
			$data['E'. $i] = chunk_split(sprintf("%.2f",$v['money']+$v['present']));
			$data['F'. $i] = date("Y-m-d H:i:s",$v['last_recharge_time']);

			$data['H'. $i] = $v['id'];


			if($v['type'] == 1 ){
				$status =  "正常";
			}
			else{
				$status = "已冻结";
			}
			$data['J'. $i] = $status;




			$id_type = $v['id_type'] -1 ;
			$data['G'. $i] =  isset($ids[$id_type]) ? $ids[$id_type] : $ids[0];





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



	public function detail(){
		$card_id = I("card_id",'');
		if(empty($card_id) || !is_numeric($card_id)){
			return false;
		}


		$card = M("card")->where("card_id = '$card_id'")->find();
		$user_id = $card['user_id'];
		$this->user = M("user")->where("user_id = '$user_id'")->find();


		$total_recharge = M("recharge_log")->where("card_id='$card_id'")->sum("money");
		$this->total_recharge = sprintf("%.2f",$total_recharge);

		$this->available = D("rechargeLog")->getAvailable($card_id);

		$this->card = $card;
		$this->display("Member/detail");


	}



	public function result(){
		if($this->error){
			$this->assign("error",$this->error);
			$this->display("search");
			return false;
		}
		if(I("sub")){
			unset($_SESSION['member']);
		}
		$card_type = I("card_type","1");
		$card_number = I("card_number");
		$id = I("id");

		if(isset($_SESSION['member'])){
			$card_type = $_SESSION['member']['card_type'];
			$card_number = $_SESSION['member']['card_number'];
			$id = $_SESSION['member']['id'];
		}



		$sql = implode(" and ",$maps);
		$card = M("card")->where("card_number = '$card_number'")->find();

		if(!empty($card)){
			$user_id = $card['user_id'];
			$user = M("user")->where(" user_id = '$user_id' and  id = '$id'")->find();
			if(empty($user))
				$this->error = true;
			else{
				if($user['id_type'] != $card_type)
					$this->error = true;
			}

		}
		else
			$this->error =true;

		if(empty($user) || empty($card))
			$this->error = true;


		if($this->error){
			$ip = $this->getIp();
			$time = time();
			$data = array(
				'ip' => $ip,
				"create_time"=>$time,
				"date"=>date("Y-m-d",$time)
			);

			M("login_record")->add($data);

			$this->card_type = $card_type;
			$this->card_number = $card_number;
			$this->id = $id;
			$this->error = '证件号或卡号错误';
			$this->assign("error",$this->error);
			$this->display("search");

		}
		else{

			$pageSize = 15;

			$card_id = $card['card_id'];
			$total_recharge = M("recharge_log")->where("card_id ='$card_id'")->sum("money_log");
			$total_spend = M("card_log")->where("card_id = '$card_id'")->sum("spend");

			if(empty($total_recharge))
				$total_recharge = 0;
			if(empty($total_spend))
				$total_spend = 0;



			$rp = I("rp",1);
			$rpStart = ($rp - 1) * $pageSize;
			$this->recharges= M("recharge_log")->where("card_id ='$card_id'")->limit($rpStart,$pageSize)->order("recharge_log_id desc ")->select();
			$rpCount = M("recharge_log")->where("card_id ='$card_id'")->count();
			$rpShow= new \Think\Page($rpCount, $pageSize);
			$rpShow->setP("rp");
			$this->rpShow = $rpShow->show();


			$cp = I("cp",1);
			$cpStart = ($cp - 1) * $pageSize;
			$this->cardList= M("card_log")->where("card_id='$card_id'")->limit($cpStart,$pageSize)->order("card_log_id desc ")->select();
			$cpCount = M("card_log")->where("card_id='$card_id'")->count();
			$cpShow= new \Think\Page($cpCount, $pageSize);
			$cpShow->setP("cp");
			$this->cpShow = $cpShow->show();




			$this->total_recharge = sprintf("%.2f",$total_recharge);
			$this->total_spend = sprintf("%.2f",$total_spend);



			$this->available = D("rechargeLog")->getAvailable($card_id);
			$this->card = $card;
			$this->user = $user;
			$_SESSION['member'] = [
				'card_type'=>$card_type,
				'id' => $id,
				'card_number' => $card_number,
			];

			$this->display("result");

		}
	}


	public function search(){
		unset($_SESSION['member_search']) ;
		$this->display("search");
	}


	public function lists(){

		$telephone = I("telephone",'');
		$card_number = I("card_number",'');
		$username = I("username",'');
		$type = I("type","-1");


		$page = I("p",1);
		$pageSize = 12;

		$this->telephone= $telephone;
		$this->card_number = $card_number;
		$this->username = $username;
		$this->type = $type;

		$maps = [];
		$print = [];
		if(is_numeric($telephone)){
			$maps[] = " a.telephone like '%$telephone%' ";
			$print['telephone'] = $telephone;
		}

		if(!empty($username)){
			$maps[] = " a.username like '%$username%'";
			$print['username'] = $username;
		}

		if($type != "-1" && is_numeric($type)){
			$maps[] = " a.type = $type ";
			$print['type'] = $type;

		}

		if(is_numeric($card_number)){
			$maps[] = " b.card_number ='$card_number' ";
			$print['card_number'] = $card_number;
		}





		$orders = [];
		$a = I("aa","");
		$b = I("bb","");
		$c = I("cc","");
		$d = I("dd","");


		if(!empty($a)){
			if($a == 2){ $desc  ='asc' ;} else{ $desc  ='desc' ;}
			$orders[] = "b.create_time $desc";
			$print['a']  = $a;
		}
		if(!empty($b)){
			if($b == 2){ $desc  ='asc' ;} else{ $desc  ='desc' ;}
			$orders[] = "b.total_recharge $desc";
			$print['b']  = $b;
		}
		if(!empty($c)){
			if($c == 2){ $desc  ='asc' ;} else{ $desc  ='desc' ;}
			$orders[] = "b.money $desc";
			$print['c']  = $c;
		}
		if(!empty($d)){
			if($d == 2){ $desc  ='asc' ;} else{ $desc  ='desc' ;}
			$orders[] = "b.exp $desc";
			$print['d']  = $d;
		}

		$this->print = http_build_query($print);




		$order = "order by ";
		if(!empty($orders))
			$order .=  implode(" ,  ",$orders);
		else{
			$order .= "create_time desc";
		}




		$start = ($page - 1) * $pageSize;
		if(count($maps) > 0){

			$tempMaps = $maps;
			foreach($tempMaps as $k => &$v){
				$v= str_replace("a.","",$v);
			}
			if(!empty($card_number)){
				$where = implode(" and ",$maps);
				$count= M("user")->query("select count(*)  from dx_user a left join dx_card b on a.user_id = b.user_id where $where $order  limit $start,$pageSize");
				$count = current(current($count));

			}
			else{
				$count = M("user")->where($tempMaps)->count();
			}

			$where = implode(" and ",$maps);

			$lists = M("user")->query("select a.*,b.*  from dx_user a left join dx_card b on a.user_id = b.user_id where $where $order  limit $start,$pageSize");

		}
		else{
			$count = M("user")->count();
			$lists = M("user")->query("select a.*,b.*  from dx_user a left join dx_card b on a.user_id = b.user_id $order  limit $start,$pageSize");
		}

		$this->count = $count;
		$time = time();
		$this->refundObj = new Refund();
		foreach($lists as $k => &$v){
			$v['money'] = sprintf("%.2f",$v['money']);
			$v['total_fine'] = sprintf("%.2f",$v['total_fine']);
			$v['total'] = D("rechargeLog")->getAvailable($v['card_id']);
		}


		$this->lists = $lists;



		$page = new \Think\Page($count, $pageSize);
		$this->assign('page', $page->show());
		$this->display("Member/list");
	}

	public function open(){
		$user_id = I("user_id",'');
		if(!is_numeric($user_id)){
			return false;
		}

		M("user")->where("user_id= '$user_id'")->save(array("type"=>1));
		M("card")->where("user_id= '$user_id'")->save(array("type"=>1));
		echo json_encode(1);
	}




	public function pin(){
		$user_id = I("user_id",'');
		if(!is_numeric($user_id)){
			return false;
		}

		M("user")->where("user_id= '$user_id'")->save(array("type"=>0));
		M("card")->where("user_id= '$user_id'")->save(array("type"=>0));
		echo json_encode(1);
	}


	private function getIp()
	{

		if(!empty($_SERVER["HTTP_CLIENT_IP"]))
		{
			$cip = $_SERVER["HTTP_CLIENT_IP"];
		}
		else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
		{
			$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		else if(!empty($_SERVER["REMOTE_ADDR"]))
		{
			$cip = $_SERVER["REMOTE_ADDR"];
		}
		else
		{
			$cip = '';
		}
		preg_match("/[\d\.]{7,15}/", $cip, $cips);
		$cip = isset($cips[0]) ? $cips[0] : 'unknown';
		unset($cips);
		return $cip;
	}


	public function refund(){
		$page = I("p",1);
		$error = [];


		$pageSize = 12;
		$start = ($page - 1) * $pageSize;


		$card_id = I("get.card_id");
		$this->card_id = $card_id;
		if(empty($card_id))
			return false;


		$card = M("card")->where("card_id='$card_id'")->find();
		if(empty($card))
			return false;



		if(empty($error)){
			if(I("sub")){
				$this->refundObj = new Refund();
				$available = $this->refundObj->getMaxRefund($card_id);
				$refund =  I("money",0);
				if($refund > $available){
					$error['refund'] = '超额';
				}
				else if($refund <= 0  || !is_numeric($refund)){
					$error['refund'] = "数值有误";
				}
				else{
					//扣款
					$this->subMoney($card_id,$refund);
					$user_id = $card['user_id'];


					$card_money = $card['money'] - $this->subMoney;
					$card_present= $card['present'] - $this->subPresent;

					M("card")->where("card_id= '$card_id'")->save(array("money"=>$card_money,'present'=>$card_present));

					$admin = $_SESSION['user']['uname'];
					D("refundLog")->record($card_id,$refund,$admin);

					$this->ok=1;
				}
			}
		}




		$card = M("card")->where("card_id='$card_id'")->find();
		$logs = M("recharge_log")->where("card_id = '$card_id'")->page($page,$pageSize)->order("type desc ,deadline desc")->select();

		$total = 0;
		$fine = 0 ;

		$time = time();

		$this->deadLineObj= new Deadline();


		foreach($logs as $k => &$v){
			if($v['type'] == 1){
				$deadtime = $v['deadline'];

				$total += $v['present'] + $v['money'];

				if($deadtime> $time){
					//没到期

					if($v['period'] == C("year_product")){
						$due = $this->deadLineObj->getDeadLine($v['create_time'],2);  // 2代表半年
						if($due <= $time){  // 过了半年
							$fine +=  $v['present'];
							$v['fine'] = $v['present'];
						}
						else{ //没过半年
							$fine +=  getFine($v['money'],$v['create_time'],$v['deadline']) + $v['present'];
							$v['fine'] = getFine($v['money'],$v['create_time'],$v['deadline'])  + $v['present'];

						}
					}
					else{

						$temp =  getFine($v['money'],$v['create_time'],$v['deadline']) + $v['present'];
						$fine += $temp;
						$v['fine'] = $temp;

					}

					$v['total'] = $v['money'];

				}
				else{
					$v['fine'] = 0;
					$v['present_fine'] = 0;
					$v['refund'] = $v['money'] + $v['present'];


					$v['total'] = $v['money'] + $v['present'];
				}
				$v['refund'] = $v['money'] + $v['present']- $v['fine'];
			}
			else{
				$v['fine'] = 0;
				$v['present_fine'] = 0;
				$v['refund'] = 0;
				$v['total'] = $v['money'] + $v['present'];
			}


			$v['total'] = sp($v['total']);
			/*
			if($v['period'] == C("year_product")){
				$due = $this->deadLineObj->getDeadLine($v['start_time'],2);  // 2代表半年
				$time = time();
				if($due <= $time){  // 过了半年
					$v['total'] = sp($v['money']);
				}
			}
			 */
			$v['fine'] = sp($v['fine']);
			$v['present_fine'] = sp($v['present_fine']);


			if($v['refund']< 0){
				$v['refund'] = 0;
			}

			$v['refund'] = sp($v['refund']);


		}

		$refund = $total - $fine;
		if($refund < 0)
			$refund = 0;

		$this->fine = sp($fine);
		$this->refund = sp($refund);
		$this->total = sp($total);


		$this->lists = $logs;

		$rpShow= new \Think\Page(count($logs), $pageSize);
		$this->page= $rpShow->show();
		$this->assign("error", $error);
		$this->display("refund");
	}



	private function f($money){
		return sprintf("%.3f",$money);
	}

	public function refundOne(){
		$recharge_log_id = I("recharge_log_id","");
		$money = I("money",0);
		$this->refundObj = new Refund();
		if(empty($recharge_log_id)){
			return false;
		}
		if(!is_numeric($money) || !is_numeric($recharge_log_id)){
			return false;
		}
		if($money <= 0)
			return false;

		$card= D("rechargeLog")->getCard($recharge_log_id);

		$return = 0;
		if($card){
			$card_id = $card['card_id'];
			$available =  $this->refundObj->getMaxRechargeRefund($recharge_log_id);
			if($money > $available){
				$return  = 0;

			}
			else if($money == $available){
				D("refund")->blank($recharge_log_id);
				$return = 1;
			}
			else{
				$this->subMoneyByRechargeId($recharge_log_id,$money);
				$return = 1;

				$acMoney= $card['money']  - $this->subMoney;
				$acPresent= $card['present']  - $this->subPresent;
				if($acMoney< 0)
					$acMoney = 0;
				if($acPresent <0)
					$acPresent = 0;
				#$rs = M("card")->where("card_id = '$card_id'")->save(['money'=>$acMoney,'present'=>$acPresent]);
			}
			D("refundLog")->record($card_id,$money);

		}

		echo json_encode($return);





	}

	public function pinCard(){
		$card_id = I("card_id","");
		if(empty($card_id) || !is_numeric($card_id)){
			return false;
		}


		$card = M("card")->where("card_id = '$card_id'")->find();
		$user_id = $card['user_id'];
		#M("user")->where("user_id= '$user_id'")->save(array("type"=>0));
		M("card")->where("card_id= '$card_id'")->save(array("money"=>0,'present'=>0));
		M("recharge_log")->where("card_id ='$card_id'")->save(["money"=>0,"present"=>0,"type"=>0]);
		echo "1";
	}



	private function available($card_id){
		$money = 0;
		$logs = M("recharge_log")->where("card_id = '$card_id'")->select();
		$this->rechargeLogs = $logs;

		$time = time();
		$fine = 0;
		foreach($logs as $k => $v){
			$money += $v['money'];
			if($time >= $v['deadline']){
				$money += $v['present'];
			}
			$fine += getFine($v['money'],$v['create_time'],$v['deadline']);
		}
		return $money-$fine;;

	}



	private function subMoney($card_id,$money){
		$logs  = M("recharge_log")->where("card_id = '$card_id' and type =1")->order("create_time asc")->select();
		$time = time();
		foreach($logs as $k => $v){
			$recharge_log_id = $v['recharge_log_id'];

			if($v['money'] > 0 || $v['present'] > 0){
				$money = $this->forsub($recharge_log_id,$money,$v['money'],$v['present'],$v['create_time'],$v['deadline'],$v['period']);
				if(!$money){
					return true;
				}
			}
		}


	}

	private function subMoneyByRechargeId($recharge_log_id,$money){
		$this->deadLineObj = new Deadline();
		$logs  = M("recharge_log")->where("recharge_log_id = '$recharge_log_id'")->select();
		$time = time();
		foreach($logs as $k => $v){
			if($v['money'] > 0 || $v['present'] > 0){
				$money = $this->forsub($recharge_log_id,$money,$v['money'],$v['present'],$v['create_time'],$v['deadline'],$v['period'],$v['isold'],$v['money_log'],$v['total_fine']);
				if(!$money){
					return true;
				}
			}
		}


	}


	private function forsub($id,$spend,$money,$present,$start_time,$deadline,$period,$isOld,$money_log,$total_fine){
		$time = time();
		$locMoney = 0;
		if($deadline < $time){
			// 已过期
			if($spend >= $money){
				$this->subMoney += $money;
				$spend -= $money;
				$nowMoney = 0;

			}
			else{
				$this->subMoney += $spend;
				$nowMoney = $money - $spend;
				$spend =0;
			}

			if($nowMoney < 0 )
				$nowMoney = 0;
			$data =	['money'=>$nowMoney];;
			M("recharge_log")->where("recharge_log_id = '$id'")->save($data);

			if($spend== 0){
				return 0;
			}


			if($spend >= $present){
				$this->subPresent += $present;
				$spend -= $present;
				$nowPresent = 0;
			}
			else{
				$this->subPresent += $spend;
				$nowPresent-= $present - $spend;
				$spend =0;
			}

			if($nowPresent < 0 )
				$nowPresent = 0;

			if($nowPresent <= 0){
				$data = ['present'=>$nowPresent,'type'=>0];
			}
			else{
				$data = ['present'=>$nowPresent];
			}

			M("recharge_log")->where("recharge_log_id = '$id'")->save($data);

			if($spend == 0)
				return 0;
			else
				return $spend;


		}
		else{
			// 未过期
			$fine = getFine($spend,$start_time,$deadline);

			$due = $this->deadLineObj->getDeadLine($start_time,2);  // 2代表半年
			$time = time();
			if($period == C("year_product") &&  $due <= $time){
				$fine = 0 ;
			}


			$subMoney = $money - $fine;

			if($spend >= $subMoney){
				$this->subMoney = $money;  
				$locMoney += $money;
				$spend = $spend -  $subMoney;
				$nowMoney = 0;

			}
			else{
				$this->subMoney += $spend + $fine;
				$locMoney += $spend;
				$nowMoney = $money - $spend -  $fine;
				$spend =0;
			}

			$percent = A("Present")->getPercent($money_log,$period,$isOld);



			$subPresent = 0;
			if($period == C("year_product") &&  $due <= $time){
				$subPresent = $present;
			}
			else{
				$subPresent = $locMoney * $percent;
			}

			$this->subPresent = $subPresent;
			$nowPresent = $present - $subPresent;



			if($period == C("year_product")){
				$data =	['present'=>$nowPresent,'money'=>$nowMoney];
			}
			else{
				if($nowMoney <=0 ||  $nowPresent <= 0 ){
					$data =	['present'=>0,'money'=>0,'type'=>0];
				}
				else{
					$data =	['present'=>$nowPresent,'money'=>$nowMoney];
				}
			}


			$data['total_fine'] =$total_fine +$fine  + $subPresent; 

			$rs =  M("recharge_log")->where("recharge_log_id = '$id'")->save($data);
			return $spend;

		}


	}



}
