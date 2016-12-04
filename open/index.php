<?php 
    header("Content-type: text/html; charset=utf-8");
    $appid='wxd80f807f93b4d498';
    $secret='90272cebf1395a17263473b15ca7f6c0';
    if(isset($_GET['code'])){
        $code=$_GET['code'];
        // 通过code取回access_token和openid
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $json=json_decode(https_request($url));
        $access_token=$json->access_token;
        // print_r($access_token);
        $openid=$json->openid;
        // 通过access_token和openid取回用户的详细信息
        $info_url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid;
        $info=json_decode(https_request($info_url));
        // print_r($info);exit;
        // post数据的地址
        $post_url="http://yiker.trueart.com/WeChatAutoReg.shtml";
        // $post_callback=https_request($post_url,$info);
        // print_r($post_callback); exit;
        // $post_callback=str_replace('"', '', $post_callback);
?>
    <form id="postform" method="post" action="http://yiker.trueart.com/WeChatAutoReg.shtml">
        <input type="hidden" name="openid" value="<?php echo $info->openid;?>"/>
        <input type="hidden" name="nickname" value="<?php echo $info->nickname;?>"/>
        <input type="hidden" name="sex" value="<?php echo $info->sex;?>"/>
        <input type="hidden" name="language" value="<?php echo $info->language;?>"/>
        <input type="hidden" name="city" value="<?php echo $info->city;?>"/>
        <input type="hidden" name="province" value="<?php echo $info->province;?>"/>
        <input type="hidden" name="country" value="<?php echo $info->country;?>"/>
        <input type="hidden" name="headimgurl" value="<?php echo $info->headimgurl;?>"/>
        <input type="hidden" name="privilege" value="[]"/>
        <input type="hidden" name="unionid" value="<?php echo $info->unionid;?>"/>
        <input type="hidden" name="returnurl" value="<?php echo $_GET['returnurl'];?>"/>
    </form>
    <script type="text/javascript">
         document.getElementById("postform").submit();
    </script>

<?php
        // header("location:$post_callback");
    }else{
        $return_url=$_GET['returnurl'];
        $redirectUrl="http://wechat.trueart.com/open/index.php?returnurl=".$return_url;
        $oaurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$appid. "&redirect_uri=".urlencode($redirectUrl)."&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
        header("location:$oaurl");
    }
      // http请求方法
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
