<?php
	date_default_timezone_set('Asia/Chongqing');
    require_once "config.php";
    require_once "wx_sdk.php";
    require_once "common.php";
    $common=new common();

    $open_id='oZG2QjrKB_h3rXlbc_xajvVgsHvE';
    $sdk = new WX_SDK($appid, $secret);
    $sdk_json=$sdk->getAccessTokenOnly();
    $access_token=json_decode($sdk_json)->access_token;
    $contet_url='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$access_token;
    $contet_data=array(
    	'type'=>'news',
		'offset'=>'0',
		'count' =>'10'
	);
    $result=$common->https_request($contet_url, json_encode($contet_data));
    $media_array=json_decode($result)->item;
    foreach ($media_array as $key => $value) {
    	print_r($value->media_id);
    	echo '<br/>';
    }
    print_r($common->httpGet('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$open_id.'&lang=zh_CN')); 


    
    file_put_contents('token.txt', $access_token);
    $send_data='{
                   "touser": "'.$open_id.'", 
                   "mpnews":{              
                            "media_id":"FQEtc_9aqyELsXONjoxmAM9FfX9QY6BKVz30jIfpkAg"               
                             },
                   "msgtype":"mpnews" 
                }';
    print_r($send_data);
    $send_url='https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token='.$access_token;
    $result=$common->https_request($send_url,$send_data);
    print_r($result);
?>