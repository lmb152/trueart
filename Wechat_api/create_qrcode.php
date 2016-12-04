<?php
	require_once "config.php";
	require_once "wx_sdk.php";
	require_once "common.php";
	$sdk = new WX_SDK($appid, $secret);
    $sdk_json=$sdk->getAccessTokenOnly();
    $access_token=json_decode($sdk_json)->access_token;
    $contet_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
    $arg=array(
    	'action_name' => 'QR_LIMIT_STR_SCENE',
	    'action_info' => array
	        (
	            'scene' => array
	                (
	                    'scene_str' => 'trueart'
	                )

	        )
    );
    $ticket="gQE18DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0VuV2hfX2JsTmhkUGlOWEc1MTJnAAIEtUZJVwMEAAAAAA==";
    $ticket_url='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
?>
<img src='<?php print $ticket_url; ?>'/>