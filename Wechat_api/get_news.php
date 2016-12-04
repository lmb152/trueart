<?php 
/*
  获取用户信息
 */
require_once "config.php";
require_once "wx_sdk.php";
require_once "common.php";
require_once "../medoo.php";
$sdk = new WX_SDK($appid, $secret);
$common=new common();
//Appid与Secret参数检查
if (empty($appid) || empty($secret)) {
    exit(json_encode(array('code' => 0, 'info' => 'Parameter error')));
}
$access_token=json_decode($sdk->getAccessTokenOnly())->access_token;

if($_GET['action']=='syn'){
	// 同步前先清空
	$sql='truncate table wechat_news';
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
			$database->insert('wechat_news',$news_data);
		}
	}
	print_r('<a href="../list.php?action=smg">返回发送</a>');	
}
?>