<?php
	//获取用户openid 
	// print_r($_COOKIE["uid"]);
    if($_GET["code"])
    {
        $code=$_GET["code"];
        $appid='wxcf102de002227700';
        $appsecret='985030841dc69c20e7394fe0d9267979';
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
        $data=https_request($url);
        $json=json_decode($data);
        $open_id=$json->openid;
        print_r($open_id.'---'.$_GET['uid']);
        // if(isset($_GET["type"])){
        //     $oaurl="http://dev.iprotime.com/public/levis/form.html?openid=".$open_id;
        // }else{
        //     $oaurl="http://dev.iprotime.com/public/levis/index.html?openid=".$open_id;
        // }
        // header("location:$oaurl");
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