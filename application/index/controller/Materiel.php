<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Materiel extends \think\Controller
{

	public function __construct(){
		parent::__construct();
	}



	public function index(){
		$this->assign("list_num",14);
		$model = model("Printer");
		$list = $model->list();
		$page = $list->render();

		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("index");


	}

	/*
	 * @desc 墨盒进货
	 */
	public function purchase(){
		if(isset($_POST['sub'])){
			$model_ids = $_POST['materiel_model_id'];
			$nums = $_POST['num'];
			$data = [];
			for($i=0;$i<count($model_ids);$i++){
				$data[] = [
					'id' => $model_ids[$i],
					'num' => (int)$nums[$i],
				];
			}

			$model = model("materiel");
			$model->addItems($data);
			return $this->redirect("/index/materiel/index");

		}
		$model = model("materiel");
		$list = $model->getAll();
		$this->assign("models",$list);
		return $this->fetch("purchase");
	}

	public function items(){
		$model = model("materiel");
		$rs = $model->getAll();
		$this->assign("list",$rs);
		return $this->fetch("items");
	}

	public function delItem(){
		$model_id = input("model_id");
		if(!is_numeric($model_id))
			return false;
		
	}

	public function editItem(){
		$model_id = input("model_id");
		if(!is_numeric($model_id))
			return false;
		$printerModel = model("printer");
		$printerList = $printerModel->getAll();
		$this->assign("printlist",$printerList);
		$model = model("materiel");
		$this->assign("model_id",$model_id);

		if(input("sub")){
			$machine_id = input("machine_id");
			$model_name = input("model_name");
			$rs = $model->editMateriel($model_id,$machine_id,$model_name);
			if(!$rs)
				$this->error("参数错误");
			else
				return $this->redirect("/index/materiels/items");

		}

		$rs = $model->getOne($model_id);
		$this->assign("rs",$rs);
		return $this->fetch("editItem");
	}


	public function addItem(){
		$printerModel = model("printer");
		$printerList = $printerModel->getAll();
		if(input("?post.sub")){
			$model_names = $_POST['model_name'];
			$machine_ids = $_POST['machine_ids'];
			$matModel = model("Materiel");
			$temp = [];
			for($i=0;$i<count($model_names);$i++){
				$temp[] = 
					[
						'model_name' => $model_names[$i],
						'machine_id' => $machine_ids[$i],
					];
					
			}


			if(empty($model_names)){
				return $this->redirect("/index/materiel/index");
			}
			$rs = $matModel->checkModel($temp);
			return $this->redirect("/index/materiel/index");
		}
		$this->assign("printlist",$printerList);
		return $this->fetch("addItem");
	}


	public function materiel_list(){
		$num = input("num",0);
		$machine_id = input("machine_id");
		$model = model("materiel");

		$materielList = $model->getByMachineId($machine_id);
		$this->assign("materiels",$materielList);
		return $this->fetch("materiel_list");

	}



	public function consume(){
		$user_id = input("user_id",null);
		$user_name = input("user_name",null);
		$materiel_model_id = input("materiel_model_id");
		$num = input("num");
		if((empty($user_id) && empty($user_name) )|| empty($materiel_model_id) || empty($num)){
			$this->error("参数错误");
		}

		$model = model("materiel");
		$rs = $model->consume($materiel_model_id,$user_id,$user_name,$num);
		if($rs){
			return $this->success("成功","/index/materiel/index");
		}
		else{
			return $this->error("失败");
		}
	}




	/*
	 *@desc 墨盒使用记录
	 */
	public function record(){
		$materiel_model_id = input("materiel_model_id");
		$model = model("materiel");
		$umodel = model("user");
		$rs = $model->getTableList($materiel_model_id);
		$temp = [];
		foreach($rs as $k => $v){
			$uid = $v['user_id'];
			$uobj = $umodel->getByUid($uid);
			$v->uname = $uobj['uname'];
			$temp[$k] = $v;
		}
		$page = $rs->render();
		$rs = $temp;
		$this->assign("page",$page);
		$this->assign("list",$rs);
		return $this->fetch("historys");

	}

	/*
	 * @desc 墨盒统计按月统计
	 */

	private function initStat(){
		$model = model("materiel");
		$rs = $model->getMaterielRecord();
		$lastMonth = "202001";


		foreach($rs as $k => $v){
			if($v['date'] >= $lastMonth)
				$lastMonth = $v['date'];
		}
		$lastMonth .= "01";
		if(date("m",strtotime($lastMonth)) == 12){
			$lastMonth = ((int)date("Y",strtotime($lastMonth))+1)."01";
		}


		$tableList = $model->getAllTable($lastMonth);
		$date = [];
		foreach($tableList as $k => $v){
			$curDate = date("Ym",$v['time']);
			$modelId = $v['materiel_model_id'];
			if(!isset($date[$curDate])){
				$date[$curDate] = [];
			}
			if(!isset($date[$curDate][$modelId]))
				$date[$curDate][$modelId] = 0;
			$date[$curDate][$modelId] += $v['num'];
		}

		foreach($date as $k => $v){
			if($k != date("Ym",time()))
				$model->addMaterielRecord($k,$v);

		}

		$temp = [];
		foreach($rs as $k => $v){
			$temp[$v['date']] = json_decode($v['content'],true);
		}
		$date = $temp;
		$lastMonthRecord = $model->getLastRecord();

		$date[date("Ym",time())] = $lastMonthRecord;
		return $date;
	}

	public function consumeStat(){
		$data = $this->initStat();
		$materielModel = model("materiel");
		$materiels = $materielModel->getAll();
		$temp = [];
		foreach($materiels as $k => $v){
			$temp[$v->materiel_model_id] = $v;
		}
		$materiels = $temp;
		$this->assign("data",$data);
		$this->assign("materiels",$materiels);
		return $this->fetch("records");
	}

	public function consumeExport(){
		$year = input("year");
		if(!is_numeric($year)||$year>date("Y")||$year<(date("Y")-2))
			return false;
		$data = $this->initStat();
		$materielModel = model("materiel");
		$materiels = $materielModel->getAll();
		$temp = [];
		foreach($materiels as $k => $v){
			$temp[$v->materiel_model_id] = $v;
		}
		$materiels = $temp;

		$table = ['序号'];
		for($i=1;$i<13;$i++){
			$table[] = $i."月";
		}
		$temp = [];

		foreach($data as $k => $v){
			foreach($v as $ik => $iv){
				if(!isset($temp[$ik])){
					$temp[$ik] = [];
				}
				$temp[$ik][$k] = $iv;
			}
		}
		$data = $this->sup($year,$temp,$materiels);
		$title = [ '机型'];
		for($i=1;$i<13;$i++){
			$title[] = $i."月";
		}

		$rs = writeExcel($data ,$title,date("Ym")."耗材列表");
		return $this->redirect("/xls/".$rs);
		#echo '<a href="/xls/'.$rs.'">下载</a>';

	}

	private function cover($num){
		if($num<10){
			return "0".$num;
		}
		else
			return $num;
	}

	/*
	 * @desc 补充
	 */
	private function sup($year,$data,$materiels){
		$temp = [];
		foreach($data as $k => $v){
			if(isset($materiels[$k]))
				$temp[$k] = [$materiels[$k]['model_name']];
			else{
				$temp[$k] = ["不存在"];
			}
			for($i=1;$i<=12;$i++){
				$p = $year.$this->cover($i);
				if(isset($v[$p])){
					$temp[$k][$p] = $v[$p];
				}
				else{
					$temp[$k][$p] = "";
				}

			}
		}
		return $temp;
	}



	public function prints(){
		$this->assign("list_num",14);
		$model = model("Printer");
		$list = $model->list();
		$page = $list->render();

		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("prints");


	}

	public function addPrint(){
		if(isset($_POST['sub'])){
			$model_names = $_POST['model_names'];

			$model = model("printer");
			$model->addItems($model_names);
			return $this->redirect("/index/materiel/index");
		}
		$model = model("printer");
		$list = $model->getAll();
		$this->assign("models",$list);
		return $this->fetch("addPrint");
	}





	public function del(){
		$model_id = input("model_id");
		$model = model("printer");
		$rs = $model->del($model_id);
		echo json_encode(true);
	}


	public function stats(){
		$model_id = input("model_id");
		$model = model("Materiel");
		$dbrs = $model->getStat($model_id,5);
		$rs = [];
		foreach($dbrs as $k => $v){
			$rs[$v->materiel_table_id] = [
				'table_id' => $v->materiel_table_id,
				'model_id' => $v->materiel_model_id,
				'create_time' => $v->create_time,
				'user_id' => $v->user_id,
				'num' => $v->num,
			];
		}

		$userModel = model("user");
		$data = [];
		$userIds = [];
		foreach($rs as $k => $v){
			if(!in_array($v['user_id'],$userIds)){
				$userIds[] = $v['user_id'];
			}
		}
		$userInfo = [];
		foreach($userIds as $k => $v){
			$tmp  = $userModel->getOne($v);
			$userInfo[$v] = $tmp['uname'];
		}

		$data = [];
		foreach($rs as $k => $v){
			$tmp = $v;
			$tmp['uname'] = $userInfo[$v['user_id']];
			$data[$k] = $tmp;
		}
		$page = $dbrs->render();
		$this->assign("list",$data);
		$this->assign("page",$page);
		return $this->fetch("stat");

	}


}

