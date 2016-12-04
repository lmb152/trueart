<?php
    date_default_timezone_set('Asia/Chongqing');
    require_once "config.php";
    require_once "wx_sdk.php";
    require_once "common.php";
    require_once "../medoo.php";

    if(isset($_GET["code"]))
    {  
        $code=$_GET["code"];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $common=new common();
        $data=$common->httpGet($url);
        $open_id=json_decode($data)->openid;
        $news_sql="select * from send_message order by sort";
        $news=$database->query($news_sql)->fetchAll();
        $show_html='点击链接有惊喜';
        foreach ($news as $key => $value) {
           $show_html .='<a href="?newsid='.$value['id'].'&openid='.$open_id.'">'.$value['title'].'</a><br/>';
        }
        print_r($show_html);
    }elseif(isset($_GET['newsid'])){
        $newsid=$_GET['newsid'];
        
        $news_sql="select * from send_message where id="$newsid;
        $news=$database->query($news_sql)->fetchAll();
        $media_sql="select * from wechat_news where news_id=".$news[0]['news_id'];
        $media=$database->query($media_sql)->fetchAll();

        $open_id = $_GET['openid'];
        $sdk = new WX_SDK($appid, $secret);
        $sdk_json=$sdk->getAccessTokenOnly();
        $access_token=json_decode($sdk_json)->access_token;
        $send_data='{
                       "touser": "'.$open_id.'",  
                       "mpnews":{              
                                "media_id":"'.$media[0]['media_id'].'"               
                                 },
                       "msgtype":"mpnews" 
                    }';
        $send_url='https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token;
        $common=new common();
        $result=$common->https_request($send_url,$send_data);
        print_r("<script>alert('手机震动了,发送成功')</script>");

         $news_sql="select * from send_message order by sort";
        $news=$database->query($news_sql)->fetchAll();
        $show_html='点击链接有惊喜';
        foreach ($news as $key => $value) {
           $show_html .='<a href="?newsid='.$value['id'].'&openid='.$open_id.'">'.$value['title'].'</a><br/>';
        }
        print_r($show_html);
    }

    
    
    
