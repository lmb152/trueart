<?php
	require_once "config.php";
	if($_GET["code"])
    {
        $code=$_GET["code"];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $data=https_request($url);
        $json=json_decode($data);
        $open_id=$json->openid;
        $access_token=$json->access_token;
        $info_url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$open_id.'&lang=zh_CN';
        $info=https_request($info_url);
        $info=json_decode($info);
        print_r($info);
        // $aurl='http://api2.trueart.com/v2/NewWeixin/GetWxid/'.$open_id.'/'.$_GET['uid'];
        // header("location:$aurl");
    }
    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
?>