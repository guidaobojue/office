<?php
namespace app\index\model;
use think\Model;
class Message extends Model
{
	//自定义初始化
	protected function initialize()
	{
		//需要调用`Model`的`initialize`方法
		parent::initialize();
		//TODO:自定义的初始化
	}



	public function my(){
	}

	/*
	 * type 1,固定资产
	 */
	public function notify($ids,$title,$message,$url="",$type = 1){
		$this->save(['title'=>$title,'content'=>$message,'type'=>$type,'create_time'=>time(),"url"=>$url]);
		$message_id = $this->message_id;
		$sqls = [];
		$ids = array_unique($ids);
		foreach($ids as $k => $v){
			$sqls[]= "($v,$message_id,0)";

		}
		$sql = implode(",",$sqls);
		$this->query("insert into vp_user_message (user_id,message_id,status) values $sql");
		return true;




		
	}

	public function close(){
	}


}
