<?php
/*
  获取用户信息
 */
date_default_timezone_set('Asia/Chongqing');
require_once "config.php";
require_once "wx_sdk.php";

//Appid与Secret参数检查
if (empty($appid) || empty($secret)) {
    exit(json_encode(array('code' => 0, 'info' => 'Parameter error')));
}

//返回token benbxiecuo
$sdk = new WX_SDK($appid, $secret);
exit($sdk->getAccessTokenOnly());


