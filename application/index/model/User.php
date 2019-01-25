<?php
namespace app\index\model;
use think\Model;
class user extends Model
{
    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }

    public function register($uname,$pwd){

	    $rs = $this->where("uname = '$uname'")->find();
	    if(is_null($rs)){
		    $data['uname'] = $uname;
		    $data['pwd'] = md5($pwd);
		    return $this->save($data);
	    }
	    else
		    return false;

    }


    public function login($uname,$pwd){
	    $rs = $this->where("uname = '$uname'")->find();
	    if($rs){
		    $rsPwd = $rs['pwd'];
		    if(md5($pwd) == $rsPwd){
			    $data = $rs->data;
			    unset($data->pwd);
			    $_SESSION['user'] = $data;
			    return true;
		    }
		    else{
			    return false;
		    }

	    }
	    else{
		    return false;
	    }
    }

    public function editInfo($uid,$data){
	    $rs = $this->save($data,['user_id'=>$uid]);
	    return $rs;
    }
    public function getOne($uid){
	    return $this->find(['user_id'=>$uid]);
    }

}
