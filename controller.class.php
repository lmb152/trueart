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
	  	// echo "<pre>";echo $sql;;exit;
	  	if($user){
	  		$_SESSION['uid']=$user['uid'];
  			$_SESSION['user']=$user['uname'];
  			$_SESSION['role']=$user['role'];
  			$_SESSION['appid']=$user['appid'];
  			$_SESSION['secret']=$user['secret'];
  			$_SESSION['brand']=$user['brand'];
  			$url='/wechat_api/get_news.php?action=syn';
  			$syn=$this->https_request($url);
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
		if ($psw1=='') {
			echo "<script>alert('请输入新密码');window.location.href='user.php?action=resetpwd';</script>";
		}
		if ($psw2=='') {
			echo "<script>alert('请再次输入新密码');window.location.href='user.php?action=resetpwd';</script>";
		}
		if ($psw1==$psw2) {
			$update_data=array(
				'password'=>md5($psw2)
			);
		$uid=$this->database->update('user',$update_data,array('uid=' => $uid ));
		echo "<script>alert('修改成功');history.go(-1);</script>";
		}else{
			echo "<script>alert('两次输入的密码不一致');window.location.href='user.php?action=resetpwd';</script>";
		}
		
	}
	// 新建用户
	public function adduser(){
      if(!isset($_POST['username'])|| empty($_POST['username'])){
            echo"<script>alert('请输入账号');history.go(-1);</script>";  exit;
        }
        if(!isset($_POST['password']) || empty($_POST['password'])){
            echo"<script>alert('请输入密码');history.go(-1);</script>";  exit;
        }
        $username=trim($_POST['username']);
        $password=trim($_POST['password']);
        $appid=trim($_POST['appid']);
        $secret=trim($_POST['secret']);
        $brand=trim($_POST['brand']);
        $sql="select uname from user where uname='".$username."'";
        // var_dump($sql);exit;
        $results = $this->database->query($sql)->fetchAll();
        // var_dump($results);exit;
        if (!empty($results)) {
        	echo "<script>alert('账号已存在');window.location.href='user.php?action=adduser';</script>";       
        }else{
        $data=array(
                'uname'=>$username,
                'password'=>md5($password),
                'appid'=>$appid,
                'secret'=>$secret,
                'created'=>time(),
                'brand'=>$brand
            );
        $res=$this->database->insert('user',$data);
        if ($res) {
        	echo "<script>alert('创建成功');history.go(-2);</script>";
        }else{
        	echo"<script>alert('创建失败');history.go(-1);</script>";  exit;
        }

    }
}
	// 编辑用户
	function edituser(){
      // echo "123";
		$uid=trim($_POST['uid']);
		$appid=trim($_POST['appid']);
		$secret=trim($_POST['secret']);
		$brand=trim($_POST['brand']);
		// var_dump($brand);exit;
	if ($appid=='') {
		echo"<script>alert('appid不能为空');history.go(-1);</script>"; exit; 
	}
	if ($secret=='') {
		echo"<script>alert('secret不能为空');history.go(-1);</script>"; exit; 
	}
	if ($brand=='') {
		echo"<script>alert('brand不能为空');history.go(-1);</script>";  exit;
	}
	$update_edit=array(
				'appid'=>$appid,
				'secret'=>$secret,
				'brand'=>$brand
				);
	$uid=$this->database->update('user',$update_edit,array('uid=' => $uid ));
	// var_dump($uid);exit;if
	if ($uid) {
		# code...
	}
	echo "<script>alert('修改成功');window.location.href='user.php?action=userlist';</script>";

     
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
		header("Location:login.html");
    	// exit();
	}
	public function single_delete(){
		$uid=$_GET['id'];
		// var_dump($uid);exit;
		$sql="delete from user WHERE uid='$uid'";
		$num= $this->database->query($sql);
		// var_dump($unm);exit;
		if (!empty($num)) {
			echo"<script>alert('删除成功');history.go(-1);</script>";  exit;
		}
	}
	public function https_request($url, $data = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}
	