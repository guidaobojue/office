<?php
namespace Home\Controller;
use Org\Util\Id;
use Think\Controller;
use Home\Model\CardModel;
use Org\Util\Deadline;
class PresentController extends Controller {


	public function __construct(){
		parent::__construct();
	}


	public function getPresent($money = 0,$period,$isOld =0){
		$mil = C("mil");
		$tri = C("tri");

		if($isOld == 1){
			$config  = C("old");
		}
		else{
			$config = C("new");
		}


		if($money >=0 & $money <$mil){
			$config = $config[0];
		}
		else if($money >=$mil & $money <$tri){
			$config = $config[1];
		}
		else if($money >=$tri){
			$config = $config[2];
		}
		else{
			exit("利息缺少配置文件");
		}


		if(!isset($config[$period]))
			exit("利息缺少配置文件");

		return $money * (double)$config[$period] / 100;
	}



	/*
	 * 返回利率
	 */
	public function getPercent($money,$period,$isOld){
		$mil = C("mil");
		$tri = C("tri");

		if($isOld == 1){
			$config  = C("old");
		}
		else{
			$config = C("new");
		}


		if($money >=0 & $money <$mil){
			$config = $config[0];
		}
		else if($money >=$mil & $money <$tri){
			$config = $config[1];
		}
		else if($money >=$tri){
			$config = $config[2];
		}
		else{
			exit("利息缺少配置文件");
		}


		if(!isset($config[$period]))
			exit("利息缺少配置文件");

		return (double)$config[$period] / 100;
	}



}


