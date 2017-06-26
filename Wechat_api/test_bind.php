<?php
/*
    崇真艺客 http://www.fangbei.org/
    CopyRight 2015 All Rights Reserved
*/
require_once "config.php";
require_once "wx_sdk.php";
require_once '../api.class.php';
define("TOKEN", "wechat_abcd_1234");


// $appid="wx79471f40b33ae84f";
// $secret="0891d9d89fb012683565031b5a85b731";
if (isset($_GET['echostr'])) {
    $echoStr = $_GET["echostr"];
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);
    if($tmpStr == $signature){
        echo $echoStr;
        exit;
    }
    // $wechatObj->valid();
}


?>