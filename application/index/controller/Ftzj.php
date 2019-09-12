<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use \think\config;

class Ftzj extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();
	}

	/*
	 * 大屏显示数据
	 */
	public function barrage() {
		$coms = $this->getData();
		$data = [];
		foreach($coms as $k => $v){
			if($v['is_show'] == 1)
				$data[] = $v;
		}
		$this->assign("list",$data);
		return $this->fetch("index");
	}


	/*
	 * @desc 定时抓取数据
	 */
	public function timing(){
		$time = strtotime(date("Y-m-d",time()));
		$cache_data = cache_get('timing');
		$temp = [];
		if(!$cache_data){
			//不存在
			$json = file_get_contents("https://api.ftrbj.com/api/Rec/?ts=$time");
			$data = json_decode($json,true);
			$data = $this->filter($data);
			#$data = $data['data'];
		}
		else{
			$conTime = $cache_data['time'];
			if($time > $conTime){
				//不存在
				$json= file_get_contents("https://api.ftrbj.com/api/Rec/?ts=$time");
				#$json = Config::get("data");
				$data = json_decode($json,true);
				$data = $this->filter($data);
			}
			else{
				$data = $cache_data['data'];
			}
		}


		$rs = [
			'time' => $time,
			'data' => $data,
		];
		cache_set('timing',$rs);
		return $data;
	}


	private function filter($data){
		$temp = [];
		foreach($data as $k => $v){
			if(!isset($temp[$v['EPName']])){
				$temp[$v['EPName']]= [
					'company_id' => isset($v['USCCode']) ? $v['USCCode'] : "",
					"company_name"=>$v['EPName'],
					"release_date"=> time(),
					"due_date"=> time() + 3600 * 24 * 365,
					"address"=>$v["WDName"],
					"user_name"=> $v['Contact'],
					"user_tel"=> $v['ContactTel'],
					"status"=> 1,
					"comments"=>"",
					"is_show"=>1,
					"origin"=> 0,
					'jobs' => [
						["job_name"=>$v['RecName'],
						"age"=> isset($v['Age']) ? $v['Age'] : "",
						"education"=>$v['EduName'],
						"address"=>$v['WDName'],
						"comments"=> $v['RecDesc'],
						"is_show"=> 1,
						"create_time"=> strtotime($v['PublishTime']),
						"due_time"=> strtotime($v['ExpTime']),
						"origin"=> 0,
						"zj_company_id"=> isset($comNames[$v['EPName']]) ? $comNames[$v['EPName']] : "",
						"working_life"=>$v['WorkYears'],
						"money"=>$v['RecMoney'],
						'nature' => $v['Nature'],
					]

				],
			];
			}
			else{
				$temp[$v['EPName']]['jobs'][] = [
						"job_name"=>$v['RecName'],
						"age"=> isset($v['Age']) ? $v['Age'] : "",
						"education"=>$v['EduName'],
						"address"=>$v['WDName'],
						"comments"=> $v['RecDesc'],
						"is_show"=> 1,
						"create_time"=> strtotime($v['PublishTime']),
						"due_time"=> strtotime($v['ExpTime']),
						"origin"=> 0,
						"zj_company_id"=> isset($comNames[$v['EPName']]) ? $comNames[$v['EPName']] : "",
						"working_life"=>$v['WorkYears'],
						"money"=>$v['RecMoney'],
						'nature' => $v['Nature'],
				];
			}
		}
		return $temp;
	}

	public function jobs(){
		$this->assign("list_num",5);
		$search = input("search");
		$obj = model("job");
		$data = [];

		if(!empty($search)){
			$list= $obj->list(10,$search);
			$this->assign("search",$search);
		}
		else
			$list= $obj->list(10);

		$comModel = model("company");
		foreach($list as $k => &$v){
			$com = $comModel->getOne($v->zj_company_id);
			$v->company_id = $com['company_id'];
			$v->company_name = $com['company_name'];
			$v->user_tel= $com['user_tel'];
		}

		$page = $list->render();
		$this->assign("search",$search);
		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("jobs");
	}


	/*
	 * 后台操作
	 */
	public function admin(){
		$this->assign("list_num",5);
		$search = input("search");
		$obj = model("company");
		$data = [];

		if(!empty($search)){
			$list= $obj->list(10,$search);
			$this->assign("search",$search);
		}
		else{
			$list= $obj->list(10);
		}

		$page = $list->render();
		$this->assign("search",$search);
		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("admin");
	}

	public function checkId(){
		$model = model("company");
		$company_id = input("company_id");
		$rs = $model->hasCompanyId($company_id);
		if($rs)
			return json_encode(true);
		else{
			return json_encode(false);
		}
	}

	public function addTime(){
		$post = $_POST;
		$job_ids = $post['job_ids'];

		$model = model("job");
		foreach($job_ids as $k => $v){
			$model->addTime($v,3600 * 24 * 182);
		}
		echo json_encode(1);

	}


	public function addCom(){
		if(isset($_POST['sub'])){

			$post = $_POST;
			unset($post['id'],$post['sub']);

			$data['origin'] = 1;
			$data['release_date'] = time(); 
			$data['company_id'] = $post['company_id'];
			$data['company_name'] = $post['company_name'];
			$data['address'] = $post['address'];
			$data['user_name'] = $post['user_name'];
			$data['user_tel'] = $post['user_tel'];
			$model = model("Company");
			$zj_company_id = $model->addCompany($data);

			$temp = [];
			if(isset($post['job_name'])){
				$job_names = $post['job_name'];
				$job_addresss = $post['job_address'];
				$comments= $post['comments'];
				$create_time = time();
				$is_show = 1;
				$due_time = $create_time + 182 * 3600 * 24;
				$origin = 1;
				$moneys = $post['money'];
				$ages = $post['age'];
				$educations = $post['education'];

				for($i=0;$i<count($post['job_name']);$i++){
					$temp[] = [
						'job_name' => $job_names[$i],
					'age' => $ages[$i],
					'education' => $educations[$i],
					'address' => $job_addresss[$i],
					'comments' => $comments[$i],
					'is_show' => $is_show,
					'create_time' => $create_time,
					'due_time' => $due_time,
					'origin' => $origin,
					'money' => $moneys[$i],
					'zj_company_id' => $zj_company_id,
				];
				}
				$model = model("job");
				$model->saveAll($temp);

			}
			

			return $this->fetch("add");
		}
		else{
			return $this->fetch("add");
		}
	}


	public function editCom(){
		$company_id = input("id");
		if(!is_numeric($company_id)){
			return false;
		}
		$company_id = (int)$company_id;
		$model = model("company");
		$rs = $model->getOne($company_id);
		$this->assign("com",$rs);



		if(isset($_POST['sub'])){
			$post = $_POST;
			unset($post['id'],$post['sub']);
			$rs = $model->editCompany($company_id,$post);
			$this->redirect("/index/ftzj/admin");
		}
		else{
			return $this->fetch("edit");
		}

	}



	public function job(){
		$id = input("id");
		if(!is_numeric($id))
			return false;

		$obj = model("job");
		$list= $obj->companyJobList($id);
		$comModel = model("company");

		$data = [];
		foreach($list as $k => &$v){
			$comRs = $comModel->getOne($v['zj_company_id']);
			$v['user_tel'] = $comRs['user_tel'];
		}

		$page = $list->render();
		$this->assign("list",$list);
		$this->assign("page",$page);
		$this->assign("comId",$id);
		$this->assign("zj_company_id",$id);
		return $this->fetch("job");
	}

	public function changeShow(){
		$job_id = input("job_id");
		$model = model("job");
		$model->changeShow($job_id);
		die(json_encode(1));
	}


	public function addJob(){
		$id = input("id");
		$this->assign("zj_company_id",$id);
		if(isset($_POST['sub'])){

			$post = $_POST;
			$post['zj_company_id'] = $id;
			$post['origin'] =  1;   //本地增加
			$post['create_time'] = time();
			$post['due_time'] = $post['create_time'] + 3600 * 24 * 182;//strtotime($post['due_time']);
			unset($post['sub']);
			unset($post['id']);
			$model = model("job");
			$model->addJob($post);
			$this->redirect("/index/ftzj/job/id/$id");


		}
		else{
			return $this->fetch("addJob");
		}

	}

	public function editJob(){
		$id = input("id");
		$comId = input("comId");
		$this->assign("zj_job_id",$id);
		$this->assign("comId",$comId);
		$model = model("job");

		if(isset($_POST['sub'])){
			$post = $_POST;
			$post['due_time'] = strtotime($post['due_time']);
			unset($post['sub'],$post['id'],$post['comId']);

			$model->editJob($id,$post);
			if(!empty($comId))
				$this->redirect("/index/ftzj/job/id/$comId");
			else
				$this->redirect("/index/ftzj/jobs");
		}
		else{
			$rs = $model->getOne($id);
			$rs['due_time'] = date("Y-m-d",$rs['due_time']);
			$this->assign("job",$rs);
			return $this->fetch("editJob");
		}



	}
	public function exportJob(){
		$post = $_POST;
		$start_time = $post['start_time'];
		$end_time = $post['end_time'];


		$start_time = strtotime($start_time);
		$end_time = strtotime($end_time);
		if(!$start_time || !$end_time)
			return false;

		$end_time += 24 * 3600 -1;

		$model = model("job");

		$data = $model->getList($start_time,$end_time);
		$title = [
			'序号',
			'职位名称',
			'年龄',
			'学历',
			'地址',
			'备注',
			'创建时间',
			'失效时间',
		];



		$temp = [];
		foreach($data as $k => $v){
			$temp[] = [
				$v['zj_job_id'],
				$v['job_name'],
				$v['age'],
				$v['education'],
				$v['address'],
				$v['comments'],
				date("Y-m-d H:i:s",$v['create_time']),
				date("Y-m-d H:i:s",$v['due_time']),
			];
		}

		$data = $temp;
		if(!empty($data)){
			$rs = writeExcel($data,$title);
			$this->redirect("/xls/".$rs);
		}
		else
			die("数据为空");




	}

	public function delJob(){
		$job_id= input("job_id");

		$model = model("job");
		$rs = $model->delJob($job_id);

		die(json_encode(1));

	}
	public function delCompany(){
		$company_id = input("company_id");

		$model = model("company");
		$rs = $model->delCompany($company_id);

		$model = model("job");
		$rs = $model->delCompanyJob($company_id);

		die(json_encode(1));

	}

	public function exportComment(){
		$post = $_POST;
		$start_time = $post['start_time'];
		$end_time = $post['end_time'];

		$start_time = strtotime($start_time);
		$end_time = strtotime($end_time);
		if(!$start_time || !$end_time)
			return false;

		$end_time += 24 * 3600-1;

		$model = model("comment");
		$data = $model->getList($start_time,$end_time);

		$temp = [];
		$temps = [];
		foreach($data as $k => $v){
			$temp = [];
			$temp[] = $v['comment_id'];
			$temp[] = $v['user_name'];
			$temp[] = $v['user_tel'];
			$temp[] = $v['department'];
			$temp[] = $v['content'];
			$temp[] = date("Y-m-d H:i:s",$v['create_time']);
			$temps[] = $temp;
		}

		$data = $temps;


		$title = [
			'序号',
			'联系人',
			'联系方式',
			'科室',
			'内容',
			'创建时间',
		];




		$rs = writeExcel($data,$title);
		$this->redirect("/xls/".$rs);

	}

	private function getData(){
		$jobModel = model("job");
		$companyModel = model("company");
		$caches = $this->timing();

		$jobs = $jobModel->getCAll();
		$coms = $companyModel->getAll();

		$temp = [];
		foreach($coms as $k => $v){
			$temp[$v['zj_company_id']] = $v;
		}
		$coms = $temp;

		foreach($jobs as $k => $v){
			$v['create_time'] = date("Y-m-d",$v['create_time']);
			$v['due_time'] = date("Y-m-d",$v['due_time']);
			if(empty($v['money']))
				$v['money'] = '面议';
			if(isset($coms[$v['zj_company_id']]))
				$coms[$v['zj_company_id']]['jobs'][] = $v;

		}
		$temp = [];
		foreach($coms as $k => $v){
			if(isset($v['jobs']))
				$temp[$v['company_id']] = $v;
		}
		$coms = $temp;

		$temp = [];
		foreach($caches as $k => $v){
			$temp[$v['company_id']] = $v;
			if(isset($coms[$v['company_id']])){
				$jobs = array_merge($v['jobs'],$coms[$v['company_id']]['jobs']);
				$coms[$v['company_id']]['jobs'] = $jobs;
			}
			else{
				$coms[$v['company_id']] = $v;
			}

		}

		foreach($coms as $k => $v){
			if(empty($v['jobs']))
				unset($coms[$k]);
		}
		return $coms;
	}



	public function screen(){
		$coms = $this->getData();
		$pageSize = 10;
		$this->assign("first",json_encode(array_slice($coms,0,10)));
		$this->assign("list",json_encode($coms));
		$this->assign("pageSize",$pageSize);
		$this->assign("total",ceil(count($coms) / $pageSize));
		return $this->fetch("screen");
	}


	/*
	 * 导入公司
	 */
	public function importComs(){
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

			$coms=[];
			foreach($data as $k => $v){
				$temp['company_id'] = $v[2];
				$temp['company_name'] =$v[1] ;
				$temp['release_date'] = strtotime($v[5]);
				$temp['due_date'] = strtotime($v[6]);
				$temp['address'] = $v[11];
				$temp['user_name'] = $v[9];
				$temp['user_tel'] = $v[10];
				$temp['status'] = 1;
				$temp['comments'] = '';
				$temp['is_show'] = 1;
				$temp['origin'] = '';
				$temp['qid'] = $v[0];
				$coms[] = $temp;

			}

			$model = model("Company");
			$model->saveAll($coms);
			
			return $this->fetch("importComs");
		}
		else{
			return $this->fetch("importComs");
		}
	}


	/*
	 * 导入职位
	 */
	public function importJobs(){
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




			$model = model("company");
			$temp = $model->getAll();
			$coms = [];
			foreach($temp as  $k => $v){
				$coms[$v['qid']] = $v;
			}
			$data = $excel['data'];

			$jobs = [];
			$temp = [];
			foreach($data as $k => $v){
				if(!isset($coms[$v[1]])){
					continue;
					
				}
				$temp['zj_company_id'] =  $coms[$v[1]]['zj_company_id'];
				$temp['job_name'] = $v[2];
				$temp['age'] = $v[3];
				$temp['education'] = $v[4];
				$temp['address'] = $v[6];
				$temp['comments'] = $v[7];
				$temp['is_show'] = 1;
				$temp['create_time'] = strtotime($v[8]);
				$temp['due_time'] = strtotime($v[9]);
				$temp['origin'] = '';
				$temp['working_life'] = '';
				$temp['money'] = $v[5];
				$temp['nature'] = 1;
				$temp['category_id'] = '';

				$jobs[] = $temp;
			}

			$model = model("Job");
			$model->saveAll($jobs);
			
			return $this->fetch("importJobs");
		}
		else{
			return $this->fetch("importJobs");
		}
	}

}
