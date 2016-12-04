<?php
	require_once "config.php";
	$redirectUrl="http://wechat.trueart.com/Wechat_api/myyiker.php";
	/*
	scope=snsapi_base 静默授权
	scope=snsapi_userinfo 手动同意
	*/
	$oaurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" .$appid. "&redirect_uri=".urlencode($redirectUrl)."&response_type=code&scope=snsapi_userinfo&state=STATE&connect_redirect=1#wechat_redirect";
	header("location:$oaurl");

?>