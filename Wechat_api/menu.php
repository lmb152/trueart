<?php
/*
*设置自定义菜单
*/
date_default_timezone_set('Asia/Chongqing');
require_once "wx_sdk.php";
require_once "common.php";
require_once "../medoo.php";
$uid=$_SESSION['uid'];
$user_sql="select * from user where uid='".$uid."'";
$userinfo=$database->query($user_sql)->fetch();
$appid=$userinfo['appid'];
$secret=$userinfo['secret'];

$sdk = new WX_SDK($appid, $secret);
$common=new common();

$sql="select * from menu where parentid=0 and uid='".$uid."' order by sort asc";
$datas = $database->query($sql)->fetchAll();

$menu_content='{"button":[';
foreach ($datas as $key => $value) {
    $child_sql="select *  from menu where parentid=".$value["menuid"] ." order by sort asc";
    $child_data=$database->query($child_sql)->fetchAll();

    if(!count($child_data)){
      $menu_content.='{  
                  "type":"view",
                  "name":"'.$value['menutitle'].'",
                  "url":"'.$value['linkurl'].'"
              },';
    }else{
      $menu_content.='{
           "name":"'.$value['menutitle'].'",
           "sub_button":[';
      foreach ($child_data as $ckey => $cvalue) {
        if($cvalue['msg_id']!='0'){
          $menu_content.='{  
                  "type":"media_id",
                  "name":"'.$cvalue['menutitle'].'",
                  "media_id":"'.$cvalue['msg_id'].'"
              },';

        }else{
          $menu_content.='{  
                  "type":"view",
                  "name":"'.$cvalue['menutitle'].'",
                  "url":"'.$cvalue['linkurl'].'"
              },';
        }
        
      }
      $menu_content.=']}';

    }
}
$menu_content.=']}';
$menu_content=str_replace('},]', '}]', $menu_content);

$access_token=json_decode($sdk->getAccessTokenOnly())->access_token;
$menu_url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;

$result=$common->https_request($menu_url,$menu_content);
$result=json_decode($result);
if(!$result->errcode){
    echo '生成微信菜单成功';
}else{
    echo '生成出错，请确认公众号获取该权限后稍后再试';
}
 		
?>