<?php
exit;
namespace app\index\controller;
use \think\view;
use \think\Request;
use \think\Model;
use app\extra\pri;

class Question extends \think\Controller
{
	private $error = [];

	public function __construct(){
		parent::__construct();

	}


	public function index(){
		$model = model("question");
		$list = $model->list();
		$this->assign("list",$list);
		return $this->fetch("index");
	}


	public function add(){
		$title = input("title");
		if(!empty($title)){
			$select = isset($_POST['select']) ? $_POST['select'] : [];
			$box = isset($_POST['box']) ? $_POST['box'] : [];
			$option = isset($_POST['option']) ? $_POST['option'] : [];
			$blank = isset($_POST['blank']) ? $_POST['blank'] : [];
			$questionModel = model("question");
			$question_table_id = $questionModel->addQuestion($title);

			foreach($select as $k => $v){
				$selectModel = new \app\index\model\Select;
				$itemModel = new \app\index\model\Selectitem;
				if(!isset($option[$k])){
					continue;
				}
				$select_id = $selectModel->addSelect($question_table_id,$v,0,$k);
				$itemModel->addItem($select_id,$option[$k]);
			}
			foreach($box as $k => $v){
				$selectModel = new \app\index\model\Select;
				$selectModel->addSelect($question_table_id,$v,1,$k);
			}

			foreach($blank as $k => $v){
				$selectModel = new \app\index\model\Select;
				$selectModel->addSelect($question_table_id,$v,2,$k);
			}

			$this->redirect("/index/question/index");

		}
		else
			return $this->fetch("add");
	}
	
	public function addFrag(){
		$name = input("name");
		$model = model("frag");
		$frag_id = $model->addFrag("");
		echo json_encode($frag_id);

	}

	public function editFrag(){
		$id = input("id");
		$model = model("frag");
		$rs = $model->getOne($id);
		if(empty($rs)){
			die("不存在该碎片");
		}
		else{
		}


	}


	public function table(){
		$table_id = input("id");
		$questionModel = model("question");
		$questionRs = $questionModel->getOne($table_id);

		$selectModel = model("select");
		$selectRs = $selectModel->getById($table_id);


		#$itemModel = new \app\index\model\Selectitem;
		$itemModel = model("selectitem");
		$data = [];
		foreach($selectRs as $k => &$v){
			if($v['type']==0){
				$itemRs = $itemModel->getBySelectId($v['select_id']);
				$v['items'] = $itemRs;
			}
		}
		$this->assign("question",$questionRs);
		$this->assign("list",$selectRs);
		return $this->fetch("template");
		


	}


	public function case(){
		return $this->fetch("case1");
	}

	public function report(){
		$question_id = input("question_id");
		$answer = $_POST['answer'];
		foreach($answer as $k => &$v){
			if(empty($v) && $v != 0)
				die("问卷填写有误");
		}

		$model = model("answer");
		$data['table_id'] = $question_id;
		$data['content'] = json_encode($answer);
		$model->addAnswer($data);
		return $this->fetch("success");
		
	}

	public function export(){
		$question_id = input("id");
		$selectModel = model("select");
		$itemModel = model("selectitem");
		$answerModel = model("answer");
		
		$selectRs = $selectModel->getById($question_id);
		$temp = [];
		foreach($selectRs as $k => $v){
			$temp[$v->select_id] = $v;
		}

		$selectRs = $temp;
		$items = [];
		foreach($selectRs as $k => $v){
			$items[$v['select_id']] = $itemModel->getBySelectId($v['select_id']);
		}

		$answerRs = $answerModel->getAllByQid($question_id);

		$data = [];
		foreach($answerRs as $k => $v){
			$contents = [];
			foreach($selectRs as $ik => $iv){
				$content = $v['content'];
				if(isset($content[$ik])){
					if($iv['type'] == 0){
						$temp = $items[$ik];
						$contents[] = $temp[0]['name'];
					}
					else{
						$contents[] = $content[$ik];
					}
				}
			}
			$data[] = $contents;
		}

		$title = [];
		foreach($selectRs as $k => $v){
			$title[] = $v->name;
		}
		

		if(empty($data)){
			$data[] = array_fill(0,count($title),"无");
		}
		$rs = writeExcel($data,$title);
		$this->redirect("/xls/".$rs);

	}




	public function suc(){
		return $this->fetch("success");
	}

	public function office(){
		return $this->fetch("office");
	}







}

