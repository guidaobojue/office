<?php
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;

class Ftzj extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();
	}

	/*
	 * 大屏显示数据
	 */
	public function barrage()
	{
		$jobModel = model("job");
		$companyModel = model("company");

		$jobs = $jobModel->getCAll();
		$coms = $companyModel->getAll();

		$temp = [];
		foreach($coms as $k => $v){
			$temp[$v['zj_company_id']] = $v;
		}
		$coms = $temp;

		foreach($jobs as $k => $v){
			if(isset($coms[$v['zj_company_id']]))
				$coms[$v['zj_company_id']]['jobs'][] = $v;
		}



		$this->assign("list",$coms);
		return $this->fetch("index");
	}


	/*
	 * @desc 定时抓取数据
	 */
	public function timing(){

	}

	/*
	 * 后台操作
	 */
	public function admin(){
		$obj = model("company");
		$data = [];
		$list= $obj->list();
		$page = $list->render();
		$this->assign("list",$list);
		$this->assign("page",$page);
		return $this->fetch("admin");
	}


	public function addCom(){
		if(isset($_POST['sub'])){

			$post = $_POST;
			unset($post['id'],$post['sub']);

			$post['origin'] = 1;
			$post['release_date'] = time(); 

			$model = model("Company");
			$rs = $model->addCompany($post);





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
		$data = [];
		$list= $obj->companyJobList($id);
		$page = $list->render();
		$this->assign("list",$list);
		$this->assign("page",$page);
		$this->assign("comId",$id);
		$this->assign("zj_company_id",$id);
		return $this->fetch("job");
	}

	public function addJob(){
		$id = input("id");
		$this->assign("zj_company_id",$id);

		if(isset($_POST['sub'])){

			$post = $_POST;
			$post['zj_company_id'] = $id;
			$post['origin'] =  1;   //本地增加
			$post['create_time'] = time();
			$post['due_time'] = strtotime($post['due_time']);
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
			$this->redirect("/index/ftzj/job/id/$comId");
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
		$title ="fuck" ;

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



		foreach($data as $k => &$v){
			unset($v['is_show']);
			unset($v['origin']);
			unset($v['zj_company_id']);
			$v['create_time'] = date("Y-m-d",$v['create_time']);
			$v['due_time'] = date("Y-m-d",$v['due_time']);
		}

		$rs = writeExcel($data,$title);
		$this->redirect("/xls/".$rs);




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


}
