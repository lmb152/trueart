<?php
    $appid='wxcf102de002227700';
    //设置secret
    //$secret='529b98cb83c7381f0c928f513b414510';
    $appsecret = '985030841dc69c20e7394fe0d9267979';
    //获取用户openid 
    if(isset($_GET["code"]))
    {
        $code=$_GET["code"];
        // $appid = "wxdbc6e760973c83cc";
        // $appsecret = "4390ebcc0738edb28902c44eacb0d644";
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
        $data=curlget($url);
        print_r($data);exit;
        $accstr ="{". substr($data, strlen("{")+strpos($data, "{"),(strlen($data) - strpos($data,"}"))*(-1))."}";
        $str=json_decode($accstr, true);
        $open_id=$str["openid"];
        file_put_contents('openid.txt', $open_id);
    }else{
        $redirectUrl="http://wechat.trueart.com/wechat.php";
        /*
        scope=snsapi_base 静默授权
        scope=snsapi_userinfo 手动同意
        */
        $oaurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" .$appid. "&redirect_uri=".urlencode($redirectUrl)."&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect";
        header("location:$oaurl");
    }
    // http请求方法
    function curlget($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    https://api.weixin.qq.com/sns/userinfo?access_token=2Uj9tle5tUtVHBn3YY8LxyOZkGvFNjWmGF-sW5SB0tyAxaPqWSBZg5TRWXYrM82SE-2f2eQBtVOiJydIqRco4KNGBraTvmgRxh4nqKWgjaA&openid=oZG2QjvgPFwFwVsvZ_vtkgmKwXZ8&lang=zh_CN 


    https://api.weixin.qq.com/sns/userinfo?access_token=r4YCuhf9B-LtHbCplcxf9S4gmx_5zjil0QxXcir9fLUfJb4l1u1wa6rfO3t-XRy_JRRnqzP8HQQNCqS0HxwR6cXRJsAy0e93RYAC_wkLPvA&openid=oZG2QjrKB_h3rXlbc_xajvVgsHvE
    https://api.weixin.qq.com/sns/userinfo?access_token=wGjrGrugHh0RG6GYQn-LOX0JaZL1stWbUj5gesuVLZ0Zsausf_F5ngPEGi9dRXQ1_X6OqOgOfNAxaQN0zVhfrLY3rfQuIeiMuZiUQrIWBps&openid=oZG2QjrKB_h3rXlbc_xajvVgsHvE
    https://api.weixin.qq.com/cgi-bin/user/info?access_token=wGjrGrugHh0RG6GYQn-LOX0JaZL1stWbUj5gesuVLZ0Zsausf_F5ngPEGi9dRXQ1_X6OqOgOfNAxaQN0zVhfrLY3rfQuIeiMuZiUQrIWBps&openid=oZG2QjrKB_h3rXlbc_xajvVgsHvE&lang=zh_CN 