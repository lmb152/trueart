<?php
$token_url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxcf102de002227700&secret=985030841dc69c20e7394fe0d9267979';
$access_token=json_decode(https_request($token_url))->access_token;
$post_url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
$openid='oZG2QjrKB_h3rXlbc_xajvVgsHvE';
$template_id='I_j8v0NXl-Ckj6Q62maDtPJBXA5jpOfWNlszJBVclQI';
$post_arr=array(
		'touser' => $openid,
		'template_id' => $template_id, 
		'url' => "http://m.trueart.com/", 
		'data' => array(
				'name'=>array(
						"value" => "恭喜你购买成功！",
	           			"color" => "#173177"
					),
		       "remark"=>array(
		           "value"=>"欢迎再次购买！",
		           "color"=>"#173177"
		       )
			)
	);
// print_r(json_encode($post_arr));exit;
$result=https_request($post_url,json_encode($post_arr));
print_r($result);
function https_request($url, $data = null) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
?>