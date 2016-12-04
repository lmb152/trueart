<?php
    date_default_timezone_set('Asia/Chongqing');
    require_once "config.php";
    require_once "wx_sdk.php";
    require_once "common.php";

    if(isset($_GET["code"]))
    {  
        $code=$_GET["code"];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $common=new common();
        $data=$common->httpGet($url);
        $open_id=json_decode($data)->openid;
        $show_html ='<a href="?newsid=1&openid='.$open_id.'">官网运营</a><br/>';
        $show_html .='<a href="?newsid=2&openid='.$open_id.'">商品代售</a><br/>';
        $show_html .='<a href="?newsid=3&openid='.$open_id.'">广告投放</a><br/>';
        $show_html .='<a href="?newsid=4&openid='.$open_id.'">公关推广</a><br/>';
        $show_html .='<a href="?newsid=5&openid='.$open_id.'">影像拍摄</a><br/>';
        $show_html .='<a href="?newsid=6&openid='.$open_id.'">设计印务</a><br/>';
        $show_html .='<a href="?newsid=7&openid='.$open_id.'">合作共赢</a><br/>';
        print_r($show_html);
    }elseif(isset($_GET['newsid'])){
        $newsid=$_GET['newsid'];
        $media_id_arr=array(
            'FQEtc_9aqyELsXONjoxmAM9FfX9QY6BKVz30jIfpkAg',
            'FQEtc_9aqyELsXONjoxmALI-5OCcCpn-in2986_PSDc',
            'FQEtc_9aqyELsXONjoxmAD_o-OfkTReOfasnS1qVz9c',
            'FQEtc_9aqyELsXONjoxmAF2YmH-eS3SQIdS3IRdepPM',
            'FQEtc_9aqyELsXONjoxmAGteoT3ZhiM7N2k2Keu5-mU',
            'plrcVcWb38HYzR_JWB1sP4oomn5yS2hmuRYsmm2ZQ7U',
            'eBnTbMou5ORxeYmttly2LXia4GdZL5CYr7OeI1WiCtQ',
            'x64EU-9sNVIOjgCqiA84XqjCyofik36T2RuiS0zl0N4',
            'M1zrvNJlkoOJnhGVNPvsqlxl9SSAeBcrcKwxJUpTIgw',
            '3xhHXLADvpN6JFVzikPiBoD50xktfODNJ75-uyUnrcY'
        );
        $open_id = $_GET['openid'];
        $sdk = new WX_SDK($appid, $secret);
        $sdk_json=$sdk->getAccessTokenOnly();
        $access_token=json_decode($sdk_json)->access_token;
        $send_data='{
                       "touser": "'.$open_id.'",  
                       "mpnews":{              
                                "media_id":"'.$media_id_arr[$newsid-1].'"               
                                 },
                       "msgtype":"mpnews" 
                    }';
        $send_url='https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token;
        $common=new common();
        $result=$common->https_request($send_url,$send_data);
        print_r("<script>alert('手机震动了,发送成功')</script>");

        $show_html ='<a href="?newsid=1&openid='.$open_id.'">官网运营</a><br/>';
        $show_html .='<a href="?newsid=2&openid='.$open_id.'">商品代售</a><br/>';
        $show_html .='<a href="?newsid=3&openid='.$open_id.'">广告投放</a><br/>';
        $show_html .='<a href="?newsid=4&openid='.$open_id.'">公关推广</a><br/>';
        $show_html .='<a href="?newsid=5&openid='.$open_id.'">影像拍摄</a><br/>';
        $show_html .='<a href="?newsid=6&openid='.$open_id.'">设计印务</a><br/>';
        $show_html .='<a href="?newsid=7&openid='.$open_id.'">合作共赢</a><br/>';
        print_r($show_html);
    }

    
    
    
