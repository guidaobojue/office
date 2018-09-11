<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {


	public function __construct(){
		parent::__construct();
		if(!isset($_SESSION['user'])){
			#$allow = c("allow");
			$allow = ['login','loginPage'];
			if(!in_array(ACTION_NAME,$allow)){
				$this->redirect("Index/loginPage");
			}
		}
		else{
		}


	}


	public function userLists(){
		$p = I('p', 1);
		$number=10;
		$name = $_SESSION['user']['uname'];
		// 分页数据	
		$map['u_name'] = $name;
		$count = M('Dx_log')->where($map)->count();		
		$page = new \Think\Page($count, $number);
		$lists = M('Dx_log')->where($map)->page($p, $number)->order('l_date desc')->select();
		foreach($lists as $k=>$v){
			$lists[$k]['l_date']=date('Y-m-d',$v['l_date']);
		}
		$this->assign('page', $page->show());
		$this->assign('lists', $lists);
		$this->display('Index/userLists');
	}
	//登录页
	public function index(){
		$this->card_total = M("card")->count();
		if(empty($this->card_total))
			$this->card_total = 0;
		$this->card_normal = M("card")->where("type = 1")->count();
		if(empty($this->card_normal))
			$this->card_normal = 0;
		$this->card_freeze=  $this->card_total - $this->card_normal;





		$getCard = function($type=1){
			$cardData = [];
			$temp = [];
			$time = strtotime(date("Y-m-d",time() - 3600 * 24 * 133));
			if($type == 1)
				$cardRs = M("card")->where("create_time > $time")->select();
			else
				$cardRs = M("card")->where("type=0 and create_time > $time")->select();
			foreach($cardRs as $k => $v){
				$key  = date("m",$v['create_time']);
				if(isset($temp[$key])){
					$temp[$key] += 1;
				}
				else
					$temp[$key] = 1;
			}


			$time = time();
			for($i=0;$i<=5;$i++){
				$time = strtotime(date("Y-m",$time)) - $i;
				$cur  = date("m",$time);
				if(isset($temp[$cur]))
					$cardData[$cur] = $temp[$cur];
				else
					$cardData[$cur] = 0;

			}

			return  array_reverse($cardData);
		};
		$cardData = $getCard(1);
		$card_key = array_keys($cardData);
		foreach($card_key as $k => &$v){
			$v .= "月";
		}
		$this->card_key = json_encode($card_key);
		$this->card_values= array_values($cardData);
		$this->card_values = json_encode($this->card_values);


		$cardData = $getCard(0);
		$this->card_freeze_values = json_encode(array_values($cardData));


		$this->display('Index/index');
	}
	//登出
	public function logout(){
		unset($_SESSION['user']);
		unset($_SESSION['manager']);
		$this->redirect("/Index/index");
	}

	public function loginPage(){
		$this->display("Index/login");
	}

	//登录
	public function login(){
		$card=I('post.card',1);

		$uname =stripcslashes(trim(I('post.uname','','htmlspecialchars')));
		$error = [];
		if($uname==""){
			$error['uname'] = true;
		}
		$pwd =stripcslashes(trim(I('post.pwd','','htmlspecialchars')));
		if($pwd==""){
			$error['pwd'] = true;
		}
		if(strlen($uname)>18 || strlen($pwd)>15){
			$error['uname'] = true;
			$error['pwd'] = true;
		}

		if($card=='1'|| true){
			$res =  D("manager")->where("mname = '$uname'")->find();

			if(is_array($res)){
				if($res['mpwd'] == md5($pwd)){
					$_SESSION['user']['id'] = $res['mid'];
					$_SESSION['user']['card'] = $card;
					$_SESSION['user']['uname'] = $res['mname'];
				}
				else{
					$error['uname'] = true;
					$error['pwd'] = true;
				}
			}else{
				$error['uname'] = true;
				$error['pwd'] = true;
			}
			if(empty($error)){
				$this->redirect("/Index/index");
			}
			else{
				$this->error = $error;
				$this->display("login");
			}


		}else{
			$res =  D("user")->where("username= '$uname'")->find();
			if($res['u_status'] == 0){
				echo json_encode(array("status"=>400,"data"=>array("账号被冻结")));	
				exit;
			}
			$res['mid']=$res['u_id'];
			$res['mname']=$res['u_name'];
			$res['mpwd']=$res['u_pwd'];
			if(is_array($res)){
				if($res['mpwd'] == md5($pwd)){
					$_SESSION['user']['id'] = $res['mid'];
					$_SESSION['user']['card'] = $card;
					$_SESSION['user']['uname'] = $res['mname'];
					echo json_encode(array("status"=>200,"data"=>array("登陆成功")));
				}
				else{
					echo json_encode(array("status"=>400,"data"=>array("帐号或密码错误")));	
				}
			}else{
				echo json_encode(array("status"=>400,"data"=>array("帐号或密码错误")));		
			}
		}
	}

	//管理员管理
	public function manager(){		
		$lists = M('Dx_manager')->select();		
		$this->users=$lists;
		$this->display();
	}
	//管理员添加
	public function managerAdd(){
		$this->display();
	}

	//日志管理
	public function applyLog(){
		$p = I('p', 1);
		$number=15;
		$count = M('Dx_applylog')->count();
		$page = new \Think\Page($count, $number);
		$lists = M('Dx_applylog')->page($p, $number)->order('retime desc')->select();
		foreach($lists as $k=>$v){
			$lists[$k]['l_time'] = date('Y-m-d',$v['l_time']);
			$lists[$k]['retime'] = date('Y-m-d H:i:s',$v['retime']);
		}
		$this->assign('lists',$lists);
		$this->assign('page',$page->show());
		$this->display();
	}


	public function doManagerAdd(){
		if(empty(I('mname')) || empty(I('pwd')) || empty(I('qpwd'))){
			$this->error('请输入完整数据');
		}else
			if(I('pwd')!=I('qpwd')){
				$this->error('两次密码不一致');
			}
		$map['mname']=I('mname');
		$rs= M('Dx_manager')->where($map)->find();
		//判断重复
		if($rs){
			$this->error('添加失败,姓名已添加');
		}
		$data = array(
			'mname' =>  I('mname'),
			'mpwd' => md5(I('pwd')),
		);
		// 自动验证
		if (D('Dx_manager')->create($data))
		{	
			if(M('Dx_manager')->add($data)){
				$this->success('成功', U('Index/manager'));
			}else{
				$this->error('失败');
			}
		} else {
			$this->error(D('Dx_manager')->getError());
		}	
	}
	//本周倒休
	public function week(){
		$w = date('w'); 
		$date = "";
		$arr = "";
		$where['time'] = I('time',time());
		$week =array('周一','周二','周三','周四','周五','周六');
		for($i = 1;$i<7;$i++){
			$s = $i-$w;	
			$date[$i]['time'] = date('Y-m-d',strtotime("$s day",$where['time']));
			$date[$i]['week']=$week[$i-1];
			$date[$i]['name']="";
		}
		$map['l_date'] = array(array('EGT', strtotime($date[1]['time'])), array('ELT',strtotime($date[6]['time'])+(24*3600-1)), 'and');
		$map['l_type'] = '1';
		$rs = M('Dx_log')->where($map)->select();
		foreach($date as $k=>$v){
			foreach($rs as $kk=>$vv){
				if(strtotime($v['time']) == (int)$vv['l_date']){
					$date[$k]['name'] .= $vv['u_name']." ";
				}
			}
		}

		$this->assign('lists',$date);
		$this->display();
	}
	//删除管理员
	public function doManagerDel(){
		$id=I('id');
		$mid=$_SESSION['manager']['id'];

		if($id==1){
			$this->error('该管理员不能删除');
		}
		if($id==$mid){
			$this->error('该账号为登陆账号,不能删除');			
		}
		$rs = M("Dx_manager")->where("mid ='$id'")->delete();
		if($rs){
			$this->success('成功');
		}else{
			$this->error('失败');
		}
	}
	//修改管理员信息
	public function managerUpdate(){
		$id=I('id');

		$rs = M("Dx_manager")->where("mid ='$id'")->find();

		$this->id = $rs['mid'];
		$this->display('Index/managerUpdate');
	}

	public function doManagerUpdate(){
		$pwd = I('pwd');
		$qpwd = I('qpwd');
		$id = I('id');
		if(empty(I('pwd')) || empty(I('qpwd'))){
			$this->error('请输入完整数据');
		}
		if($pwd!=$qpwd){
			$this->error('两次密码不一致');
		}
		$data = array(
			'mpwd' =>  md5($pwd),
		);
		// 自动验证
		if (D('Dx_manager')->create($data))
		{
			if(false !== M('Dx_manager')->where('mid='.$id)->save($data))
				$this->success('修改成功', U('Index/manager'));
			else
				$this->error('修改失败');
		} else {
			$this->error(D('Dx_manager')->getError());
		}
	}


	//员工修改密码
	public function userPwd(){
		$this->display();
	}
	public function doUserPwd(){
		$pwd =stripcslashes(trim(I('pwd','','htmlspecialchars')));
		$qpwd =stripcslashes(trim(I('qpwd','','htmlspecialchars')));
		if(empty(I('pwd')) || empty(I('qpwd'))){
			$this->error('请输入完整数据');
		}
		if($pwd!=$qpwd){
			$this->error('两次密码不一致');
		}
		if(strlen($pwd)>12){
			$this->error('请输入小于12位密码');	
		}
		$id=$_SESSION['user']['id'];

		$data = array(
			'u_pwd' =>  md5($pwd),
		);
		// 自动验证
		if (D('Dx_user')->create($data))
		{
			if(false !== M('Dx_user')->where('u_id='.$id)->save($data))
				$this->success('修改成功', U('Index/userLists'));
			else
				$this->error('修改失败');
		} else {
			$this->error(D('Dx_user')->getError());
		}
	}
	//申请倒休
	public function apply(){
		$this->display();
	}
	public function doApply(){
		$l_date = I('time');
		if(empty(I('time'))){
			$this->error('请输入完整数据');
		}
		$name = $_SESSION['user']['uname'];
		$time = date('Y-m-d',time());
		$h = date('H',time());
		$day = date('Y-m-d',strtotime(" +1 days"));
		$day2 = date('Y-m-d',strtotime(" +2 days"));

		if($h>12){
			if(strtotime($l_date)<strtotime($day2)){
				$this->error('申请失败，只能申请后天及以后的日期');
			}
		}
		if(strtotime($l_date)<strtotime($day)){
			$this->error('申请失败，只能申请明天及以后的日期');
		}

		$map['u_name']=$name;
		$time=strtotime(I('time'));
		$map['l_date']=$time;
		$rs= M('Dx_log')->where($map)->find();
		$num= M('Dx_log')->where("l_date = '$time'")->count();
		//判断重复
		if($rs){
			$this->error('申请失败,该天已申请');
		}
		$data = array(
			'u_name' =>  $name,
			'l_date'  => $time,
			'l_type'  => '0',
		);
		// 自动验证
		if (D('Dx_log')->create($data))
		{
			if(M('Dx_log')->add($data)){
				$this->success('成功', U('Index/userLists'));
			}else{
				$this->error('失败');
			}
		} else {
			$this->error(D('Dx_log')->getError());
		}

	}
	//申请列表
	public function applyLists(){
		$p = I('p', 1);
		$number=10;
		$map['l_type']='0';
		$count = M('Dx_log')->where($map)->count();
		$page = new \Think\Page($count, $number);
		$lists = M('Dx_log')->where($map)->page($p, $number)->order('l_date')->select();
		foreach($lists as $k=>$v){
			$lists[$k]['l_date']=date('Y-m-d',$v['l_date']);
		}
		if(!$lists){
			$lists[0]['u_name']='暂无申请';
			$lists[0]['l_date']='暂无申请';
			$lists[0]['l_status']='1';
			$lists[0]['l_type']='0';
		}
		$this->assign('page', $page->show());
		$this->assign('lists', $lists);
		$this->assign('num', $count);
		$this->display('Index/applyLists');
	}
	//批复申请
	public function responded(){
		$id = I('id');
		$type = I('status');
		$l_date = strtotime(I('time'));
		if($type == 1){
			$num = M('Dx_log')->where("l_date = '$l_date' && l_type = 1")->count();
			if($num>=4){
				$this->error('名额已满');
			}
		}
		$data = array(
			'l_type' =>  $type,
		);
		$time = time();
		$mname = $_SESSION['manager']['uname'];
		$uname = M('Dx_log')->where("l_id = $id ")->find();

		$l_type = M('Dx_log')->where("l_id = $id ")->find();
		if($l_type['l_type']!=0){
			$this->error('申请已批复，请刷新页面');
		}
		$arr = array(
			'm_name' => $mname,
			'u_name' => $uname['u_name'],
			'retime' => $time,
			'l_time' => $uname['l_date']
		);
		// 自动验证
		if (D('Dx_log')->create($data))
		{
			if(false !== M('Dx_log')->where('l_id='.$id)->save($data)){
				M('Dx_applylog')->add($arr);	
				$this->success('成功');
			}else{
				$this->error('失败');
			}		
		} else {
			$this->error(D('Dx_log')->getError());
		}
	}

	//倒休列表
	public function lists(){
		$p = I('p', 1);
		$number = I('number',30);
		$count = M('Dx_user')->where("u_address = '安顺' && u_status = 1")->count();
		$page = new \Think\Page($count, $number);
		$user = M('Dx_user')->page($p, $number)->where("u_address = '安顺' && u_status = 1")->select();
		$l_time = date('Y-m-d',time());
		$li_time = date('m/d',time());
		$time = strtotime($l_time);
		$nowtime=time();
		//今天周几

		$w = date('w'); 
		$date = "";
		$arr = "";
		$where['time'] = I('time',time());
		for($i = 1;$i<7;$i++){
			$s = $i-$w;	
			$date[$i] = date('Y-m-d',strtotime("$s day",$where['time']));
			$arr[$i]['time'] = strtotime(date('Y-m-d',strtotime("$s day",$where['time'])));
			$arr[$i]['week'] = $i;
		}

		$where['start_time'] = I('start_time',$arr[1]['time']);
		$where['end_time'] = I('end_time',$arr[6]['time']);
		$map['l_date'] = array(array('EGT', $where['start_time']), array('ELT', $where['end_time']+(24*3600-1)), 'and');
		$rs = M('Dx_log')->where($map)->select();
		foreach($user as $k=>$v){
			foreach($arr as $key=>$val){
				$user[$k]['arr'][$val['time']]['status']=0;
				$user[$k]['arr'][$val['time']]['l_type']='';
			}
		}

		foreach($rs as $kk=>$vv){
			foreach($user as $k=>$v){
				if($v['u_name']==$vv['u_name']){
					$user[$k]['arr'][$vv['l_date']]['status']=1;
					$user[$k]['arr'][$vv['l_date']]['l_type']=$vv['l_type'];
				}
			}
		}
		$re=M('Dx_log');
		$name="";
		$todayuser = M('Dx_log')->where("l_date='$time' && l_type = 1")->select();
		foreach($todayuser as $v){
			$name .= $v['u_name']."&nbsp;";
		}
		if($name==""){
			$name="暂无";
		}
		//导出excel
		if(I('get.do') == 'export')
		{	
			$results = M('Dx_log')->where($map)->order('l_date')->select();
			foreach($results as $k=>$v){
				$results[$k]['l_date']=date('Y-m-d',$v['l_date']);
			}
			if(empty($results)){
				$this->error('无导出数据');
			}
			A('Excel')->export(C('EXPORT_TITLE_DAOXIU'), $results,'倒休表');
		}
		$this->num=$l_time;
		$this->nums=$li_time;
		$this->name=$name;
		$this->week=$date;
		$this->users=$user;
		$this->number=$number;
		$this->p=$p;
		$this->assign('page', $page->show());
		$this->assign('where', $where);
		$this->assign('nowtime', $nowtime);
		$this->display('Index/lists');
	}
	//详情页删除
	public function detailDel(){
		$userId=I('id');
		$rs = M("Dx_log")->where("l_id ='$userId'")->delete();
		if($rs){
			$this->success('成功');
		}else{
			$this->error('失败');
		}
	}
	//修改姓名
	public function userUpdate(){
		$userId=I('id');
		$rs = M("Dx_user")->where("u_id ='$userId'")->find();
		$this->id = $rs['u_id'];
		$this->name = $rs['u_name'];
		$this->address = $rs['u_address'];
		$this->number = $rs['u_number'];
		$this->display('Index/userUpdate');
	}

	public function doUserUpdate(){
		$id = I('id');
		if(empty(I('username')) || empty(I('address')) || empty(I('number'))){
			$this->error('请输入完整数据');
		}
		if(!empty(I('pwd')) && !empty(I('qpwd'))){
			if($pwd!=$qpwd){
				$this->error('两次密码不一致');
			}else{
				$data['u_pwd']=md5(I('pwd'));
			}
		}
		$data['u_name']=I('username');
		$data['u_address']=I('address');
		$data['u_number']=I('number');
		$map['u_name']=I('username');
		$rs= M('Dx_user')->where($map)->where("u_id != $id")->find();
		//判断重复
		if($rs){
			$this->error('修改失败,改姓名已存在');
		}
		// 自动验证
		if (D('Dx_user')->create($data))
		{
			if(false !== M('Dx_user')->where('u_id='.$id)->save($data))
				$this->success('修改成功', U('Index/userDel'));
			else
				$this->error('修改失败');
		} else {

			$this->error(D('Dx_user')->getError());
		}
	}
	//搜索
	public function search(){
		$p = I('p', 1);
		$number=10;
		// 分页数据	
		$where['u_name'] = I('dx_name');
		$where['start_time'] = strtotime(I('start_time'));
		$where['end_time'] = strtotime(I('end_time')); 
		if(!empty($where['u_name']))
			$map['u_name'] = $where['u_name'];
		if(!empty($where['start_time']) && !empty($where['end_time']))
		{
			if($where['start_time'] > $where['end_time'])
				$this->error('开始时间不能大于结束时间');
			$map['l_date'] = array(array('EGT', $where['start_time']), array('ELT', $where['end_time']+(24*3600-1)), 'and');
		}
		if(!empty($where['start_time']) && empty($where['end_time']))
			$map['l_date'] = array('EGT', $where['start_time']);
		if(empty($where['start_time']) && !empty($where['end_time']))
			$map['l_date'] = array('ELT', $where['end_time']+(24*3600-1));
		$map['l_type']='1';
		$count = M('Dx_log')->where($map)->count();
		$page = new \Think\Page($count, $number);
		$lists = M('Dx_log')->where($map)->page($p, $number)->order('l_date')->select();
		foreach($lists as $k=>$v){
			$lists[$k]['l_date']=date('Y-m-d',$v['l_date']);
		}
		//导出excel
		if(I('get.do') == 'export')
		{
			$results = M('Dx_log')->where($map)->order('l_date')->select();
			foreach($results as $k=>$v){
				$results[$k]['l_date']=date('Y-m-d',$v['l_date']);
			}

			if(empty($results)){
				$this->error('无导出数据');
			}
			A('Excel')->export(C('EXPORT_TITLE_DAOXIU'), $results,'倒休表');
		}
		$where['start_time'] = I('start_time');
		$where['end_time'] = I('end_time'); 
		$this->assign('where', $where);
		$this->assign('page', $page->show());
		$this->assign('lists', $lists);
		$this->display('Index/searchList');
	}
	//修改
	public function update(){
		$userId=I('id');
		$user = M('Dx_user')->select();

		$rs = M("Dx_log")->where("l_id ='$userId'")->find();
		$this->id = $rs['l_id'];
		$this->times = date('Y-m-d',$rs['l_date']);
		$this->name = $rs['u_name'];
		$this->users = $user;
		$this->display('Index/update');
	}
	//执行修改
	public function doUpdate(){
		$id = I('id');
		if(empty(I('time')) || empty(I('username'))){
			$this->error('请输入完整数据');
		}
		$time=strtotime(I('time'));
		$num= M('Dx_log')->where("l_date = '$time'")->count();

		$map['u_name']=I('username');
		$map['l_date']=$time;
		$rs= M('Dx_log')->where($map)->find();

		//判断重复
		if($rs){
			$this->error('修改失败,数据已存在');
		}
		//判断人数
		if($num>=4){
			$this->error('已无名额,请换一天');
		}
		$data = array(
			'u_name' =>  I('username'),
			'l_date'  => $time,
		);
		// 自动验证
		if (D('Dx_log')->create($data))
		{
			if(false !== M('Dx_log')->where('l_id='.$id)->save($data))
				$this->success('修改成功', U('Index/search'));
			else
				$this->error('修改失败');
		} else {
			$this->error(D('Dx_log')->getError());
		}
	}


	//删除
	public function del(){
		$userId=I('id');
		$rs = M("Dx_log")->where("l_id ='$userId'")->delete();
		if($rs){
			$this->success('成功');
		}else{
			$this->error('失败');
		}		
	}

	//个人倒休详情
	public function detail(){
		$p = I('p', 1);
		$number=10;
		$u_number = '';
		$map['u_name']=I('name');
		$count = M('Dx_log')->where($map)->count();
		$page = new \Think\Page($count, $number);
		$lists = M('Dx_log')->where($map)->page($p, $number)->order('l_date desc')->select();
		$user = M('Dx_user')->where("u_name = '".$map['u_name']."' ")->select();
		if(!empty($user)){
			foreach($user as $v){
				$number = $v['u_number']; 
			}
		}

		foreach($lists as $k=>$v){
			$lists[$k]['l_date']=date('Y-m-d',$v['l_date']);
		}
		if(!$lists){
			$lists[0]['u_number'] = $number;
			$lists[0]['u_name']=I('name');
			$lists[0]['l_date']="暂无倒休";
			$lists[0]['l_status']='1';
		}
		$this->assign('page', $page->show());
		$this->assign('lists', $lists);
		$this->assign('num', $count);
		$this->display('Index/detail');
	}

	//添加倒休
	public function add(){
		$map['u_address'] = '安顺';
		$map['u_status'] = '1';
		$user = M('Dx_user')->where($map)->select();
		$daoxiu= M('Dx_log')->select();
		foreach($user as $k=>$v){
			$num=count($daoxiu);
			for($i=0;$i<$num;$i++){
				if($v['u_name']==$daoxiu[$i]['u_name']){

					$user[$k]['arr'][]=$daoxiu[$i];
				}
			}
		}
		$this->users=$user;
		$this->display('Index/add');
	}

	//执行添加倒休
	public function doAdd(){
		if(empty(I('time')) || empty(I('username'))){
			$this->error('请输入完整数据');
		}
		$map['u_name']=I('username');
		$time=strtotime(I('time'));
		$map['l_date']=$time;
		$map['l_type']='1';
		$rs= M('Dx_log')->where($map)->find();		
		//判断重复
		if($rs){
			$this->error('添加失败,数据已存在');
		}
		$data = array(
			'u_name' =>  I('username'),
			'l_date'  => $time,
			'l_type'  => '1',
		);
		$nowTime = time();
		$mname = $_SESSION['manager']['uname'];

		$arr = array(
			'm_name' => $mname,
			'u_name' => I('username'),
			'retime' => $nowTime,
			'l_time' => $time
		);
		// 自动验证
		if (D('Dx_log')->create($data))
		{
			if(M('Dx_log')->add($data)){
				M('Dx_applylog')->add($arr);
				$this->success('成功', U('Index/lists'));
			}else{
				$this->error('失败');
			}
		} else {
			$this->error(D('Dx_log')->getError());
		}
	}

	//添加人员
	public function userAdd(){
		$this->display();
	}

	//执行添加人员
	public function doUserAdd(){	
		if(empty(I('username')) || empty(I('address')) || empty('pwd') || empty('number')){
			$this->error('请输入完整数据');
		}
		$map['u_name']=I('username');
		$rs= M('Dx_user')->where($map)->find();
		//判断重复
		if($rs){
			$this->error('添加失败,姓名已添加');
		}
		$data = array(
			'u_name' =>  I('username'),
			'u_address' =>  I('address'),
			'u_pwd' => md5(I('pwd')),
			'u_number'=>I('number'),
		);
		// 自动验证
		if (D('Dx_user')->create($data))
		{	
			if(M('Dx_user')->add($data)){
				$this->success('成功', U('Index/userDel'));
			}else{
				$this->error('失败');
			}
		} else {
			$this->error(D('Dx_user')->getError());
		}
	}


	public function userDel(){
		$p = I('p', 1);
		$number=30;
		$count = M('Dx_user')->count();
		$page = new \Think\Page($count, $number);
		$lists = M('Dx_user')->page($p, $number)->order('u_address desc')->select();
		foreach($lists as $k=>$v){
			$name = $v['u_name'];
			$lists[$k]['days'] = M('Dx_log')->where(" u_name = '$name'")->count();
		}

		$this->users=$lists;
		$this->assign('page', $page->show());
		$this->display('Index/userDel');
	}

	//冻结账号
	public function doUserDel(){
		$id=I('id');
		$status =1-I('status');
		$data = array(
			'u_status' =>  $status,
		);
		// 自动验证
		if (D('Dx_user')->create($data))
		{
			if(false !== M('Dx_user')->where('u_id='.$id)->save($data))
				$this->success('成功', U('Index/userDel'));
			else
				$this->error('失败');
		} else {
			$this->error(D('Dx_user')->getError());
		}
	} 




	public function getModulePer(){
	}


}
