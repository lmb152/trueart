<?php
//关键字回复
// if(!empty( $keyword ))
// {
// 	$xml='<xml>
// 		 <ToUserName><![CDATA[toUser]]></ToUserName>
// 		 <FromUserName><![CDATA[fromUser]]></FromUserName>
// 		 <CreateTime>12345678</CreateTime>
// 		 <MsgType><![CDATA[text]]></MsgType>
// 		 <Content><![CDATA[content]]></Content>
// 		 </xml>';
// 	$msgType = "text";
// 	switch ($keyword)
// 	{
// 		case "关键词1";
// 			$contentStr = "自定义回复1";
// 			break;
// 		case "关键词2";
// 			$contentStr = "自定义回复2";
// 			break;
// 		default;
// 			$contentStr = "自定义回复3";
// 	}
// 	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
// 	echo $resultStr;
// }else{
// 	echo "输入点字吧";
// }

// $object=new stdClass();
// $object->FromUserName='lmb152076';
// $object->ToUserName='oZG2QjrKB_h3rXlbc_xajvVgsHvE';
// $content='你好呀';
// print_r( _response_text($object,$content));
$keyword = trim($postObj->Content);

if(!empty( $keyword ))
{
    $contentStr = "微信公众平台-文本回复功能源代码";
    //$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
    $resultStr = _response_text($postObj,$contentStr);
    echo $resultStr;
}else{
    echo "Input something...";
}
function _response_text($object,$content){
    $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>%d</FuncFlag>
                </xml>";
    $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
    return $resultStr;
}


?>