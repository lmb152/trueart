<?php
require_once "Wechat_api/common.php";
require_once "Wechat_api/wx_sdk.php";
require_once "medoo.php";
$uid=$_SESSION['uid'];
$appid=$_SESSION['appid'];
$secret=$_SESSION['secret'];
$sdk = new WX_SDK($appid, $secret);

$common=new common();
//Appid与Secret参数检查
if (empty($appid) || empty($secret)) {
    exit(json_encode(array('code' => 0, 'info' => 'Parameter error')));
}
$access_token=json_decode($sdk->getAccessTokenOnly())->access_token;
	// 同步前先清空
	$sql='delete from wechat_news where uid='.$uid;
	$database->query($sql);
	$count_url='https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token='.$access_token;
	$count_data=$common->https_request($count_url);
	$count=json_decode($count_data)->news_count;
	$page=ceil($count/20);
	for ($i=0; $i < $page && ($i*20)<=$count; $i++) { 
		$news_url='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$access_token;
		$post_data=array("type"=>"news","offset"=>($i*20),"count"=>"20");
		$news=$common->https_request($news_url,json_encode($post_data));
		$news_obj=json_decode($news)->item;
		foreach ($news_obj as $key => $value) {
			// print_r($value);
			$content=$value->content->news_item;
			$title='';
			foreach ($content as $ckey => $cvalue) {
				$title.=$cvalue->title.'-';
			}
			$news_data['news_title']=$title;
			$news_data['update_time']=$value->update_time;
			$news_data['media_id']=$value->media_id;
			$news_data['uid']=$uid;
			$database->insert('wechat_news',$news_data);
		}
	}
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

	<body>
		<div style="margin:50px auto 0 auto;text-align:center;font-size:26px;">
			<?php print_r("欢迎使用崇真微信公众平台管理系统");?>
		</div>
		<div style="margin: 20px auto;text-align:center;font-size:16px;">
			今天是<?php print_r(date('Y年m月d日',time()));?>
		</div>
		
	</body>
</html>
