<?php
header("Content-type: text/html; charset=utf-8");
class controller{
	protected $database = '';

	public function __construct($database)
	{
		$this->database = $database;
	}
	public function login(){
		$username = trim($_POST['username']);
	  	$password = trim($_POST['password']);
	  	$sql="select * from user where uname='".$username."' and password='".md5($password)."'";
	  	$user=$this->database->query($sql)->fetch();
	  	if($user){
	  		$_SESSION['uid']=$user['uid'];
  			$_SESSION['user']=$user['uname'];
  			$_SESSION['role']=$user['role'];
  			$_SESSION['appid']=$user['appid'];
  			$_SESSION['secret']=$user['secret'];
  			$_SESSION['brand']=$user['brand'];
  			echo "<script>alert('登陆成功');window.location.href='index.php';</script>";
  			exit();
  		}else{
	  		echo "<script>alert('请检查密码');window.location.href='login.html';</script>";
	    	exit();
	  	}
	}
	//update数据
	public function update(){
      // var_dump($_POST);
		$uid=$_SESSION['uid'];
		$psw1=$_POST['password_a'];
		$psw2=$_POST['password_b'];
		$update_data=array(
				'password'=>md5($psw2)
			);
		$uid=$this->database->update('user',$update_data,array('uid=' => $uid ));
	}
	// 新建用户
	function adduser(){
       echo "123";
  
	}
	// 编辑用户
	function edituser(){

	}

	//导出数据
	public function export(){
		$start_time = $_POST['start_time'].'00:00:00';
		$start_time = date('Y-m-d H:i:s',strtotime($start_time));
		$end_time = $_POST['end_time'].' 00:00:00';
		$end_time = date('Y-m-d H:i:s',strtotime($end_time));
		$sql="select * from winning where wdate between '".$start_time."' and '".$end_time."'";
		$datas = $this->database->query($sql)->fetchAll();
		$output = '';
	    $header = '';
	    $content = '';
	    $header = "id\tname\tmobile\topenid\tprizemoney\tdatetime\t\n";
	    $i=1;
	    foreach($datas as $data) {
	    	$content .= $i. "\t"
	    	. iconv('UTF-8', 'GBK', $data['wname']) . "\t" 
	    	. iconv('UTF-8', 'GBK', $data['wmobile']) . "\t"
	    	. iconv('UTF-8', 'GBK', $data['openid']) . "\t"
	    	. iconv('UTF-8', 'GBK', $data['prizemoney']) . "\t"
	    	. $data['wdate']. "\t\n";
	    	$i=$i+1;
	    }
	    $output = $header . $content;
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/vnd.ms-execl");
		header("Content-Type: application/force-download");
		header("Content-Type: application/download");
	    header("Content-Disposition: attachment; filename=prize" . time() . ".xls");
	    header("Content-Transfer-Encoding: binary");
	    header("Pragma: no-cache");
	    header("Expires: 0");
	    print $output;
	    exit();
	}
	// 登出
	public function logout(){
		unset($_SESSION['user']);
		session_destroy();
		echo "<script>alert('登出成功');window.location.href='login.html';</script>";
    	exit();
	}
}
	