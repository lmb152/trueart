<?php
  	$appid='wxcf102de002227700';
	$redirectUrl="http://pigcmsdev.iprotime.com/backend/lamer/wechat/Wechat_api/syn.php";
    /*
    scope=snsapi_base 静默授权
    scope=snsapi_userinfo 手动同意
    */
    // $oaurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" .$appid. "&redirect_uri=".urlencode($redirectUrl)."&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect";
    $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.urlencode($redirectUrl).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
    header("location:$url");
?>
