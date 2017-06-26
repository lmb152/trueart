<?php 
	require_once "wx_sdk.php";
	$access_token=$_POST['access_token'];
	$media_id=$_POST['media_id'];
	$url='http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$media_id;

	$time=date("Ymd",time());

	$upload_dir="upload/";//上传的路径
	$file_name=$time.rand(1000,9999).'.'.$media_id.'.amr';
	$dir=$upload_dir.$file_name;//创建上传目录

	//判断目录是否存在 不存在则创建
	if(!file_exists($upload_dir)){
		mkdir($upload_dir,0777,true);
	}

	$contents=curl_download($url,$dir);
	if($contents){
		echo "上传成功";
	}
	function curl_download($url, $dir) {
		$ch = curl_init($url);
		$fp = fopen($dir, "wb");
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$res=curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		return $res;
	}
?>