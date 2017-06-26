<?php
/*
    崇真艺客 http://www.fangbei.org/
    CopyRight 2015 All Rights Reserved
*/

define("TOKEN", "wechat_abcd_1234");


// $result=$wechatObj->wechat_keyword('关键字测试');
// echo "<pre>";
// print_r(unserialize($result['content']['content']));exit;


// echo "<pre>";print_r($content);exit;
if (!isset($_GET['echostr'])) {
    $wechatObj = new wechatCallbackapiTest();
    // $data=$wechatObj->wechat_subscribe();
    // echo "<pre>";
    // print_r($data);exit;
    $wechatObj->responseMsg();
}else{
    $echoStr = $_GET["echostr"];
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $token = TOKEN;
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);
    if($tmpStr == $signature){
        echo $echoStr;
        exit;
    }
    // $wechatObj->valid();
}

class wechatCallbackapiTest
{
    protected $database = '';
    protected $access_token='';
    protected $brand='';
    // 构造函数
    public function __construct(){
        require_once "wx_sdk.php";
        require_once '../api.class.php';
        require_once '../medoo.php';
        $this->database = $database;
        if(isset($_GET['uid'])){
            $uid=$_GET['uid'];
            $user_sql="select * from user where uid='".$uid."'";
            $userinfo=$database->query($user_sql)->fetch();
            $appid=$userinfo['appid'];
            $secret=$userinfo['secret'];
            $this->brand=$userinfo['brand'];
            $sdk = new WX_SDK($appid, $secret);
            $this->access_token = json_decode($sdk->getAccessTokenOnly())->access_token;
        }
    }
    //验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            return $echoStr;
        }
    }

    //响应消息
    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("R \r\n".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            if (($postObj->MsgType == "event") && ($postObj->Event == "subscribe" || $postObj->Event == "unsubscribe")){
                //过滤关注和取消关注事件
            }else{
                
            }
            
            //消息类型分离
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                   if (strstr($postObj->Content, "第三方")){
                        $result = $this->relayPart3("http://www.fangbei.org/test.php".'?'.$_SERVER['QUERY_STRING'], $postStr);
                    }else{
                        $result = $this->receiveText($postObj);
                    }
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;
                    break;
            }
            $this->logger("T \r\n".$result);
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    //接收事件消息
    private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":
                // $this->logger("R \r\n".'123123');
                if(isset($object->Ticket)){
                    $sence_ticket = $object->Ticket;
                    $this->logger("R \r\n".$sence_ticket);
                    $data=$this->wechat_qrcode($sence_ticket);
                    if($data && $data['image']){
                        $this->logger("R \r\n".$data['image']);
                        $content[]=array(
                            'PicUrl' =>$data['image'], 
                            'Title'=>$data['title'],
                            'Description'=>$data['description'],
                            'Url'=>$data['linkurl']
                        );
                    }else{
                        $content = "很抱歉,不清楚您需要些什么，请稍后重试或联系崇真艺客";
                    }
                    
                }else{
                    $data=$this->wechat_subscribe();
                    if($data){
                        switch ($data['type']) {
                            case '1':
                                $content=$data['content'];
                                break;
                            case '2':
                                $data=$data['content']->news_item[0];
                                $content[]=array(
                                    'PicUrl' =>$data->thumb_url, 
                                    'Title'=>$data->title,
                                    'Description'=>$data->description,
                                    'Url'=>$data->url
                                );
                                $this->logger("R \r\n".$data->thumb_url);
                                break;
                            case '3':
                                $content=$data['content'];
                                break;
                            case '4':
                                $content[]=array(
                                    'PicUrl' =>$data['image'], 
                                    'Title'=>$data['title'],
                                    'Description'=>$data['description'],
                                    'Url'=>$data['linkurl']
                                );
                                break;
                            default:
                                $content='欢迎关注本微信公众号';
                                break;
                        }
                        
                    }else{
                        $content = '欢迎关注本微信公众号';
                    }
                }

                // $content[]=array('PicUrl' =>"$data['image']" , 'Title'=>"$data['title']",'Description'=>"$data['description']",'Url'=>"$data['linkurl']");
                // $this->logger("R \r\n".$data['image']);
                // $this->logger("R \r\n".$data['linkurl']);
                // $this->logger("R \r\n".$data['description']);
                // $content .= (!empty($object->EventKey))?("\n来自二维码场景 ".str_replace("qrscene_","",$object->EventKey)):"";
                // $content .='<img src="http://mmbiz.qpic.cn/mmbiz_jpg/77KkicCbGcWVKxpzTh1H1TJoBH90ng0XV4gmeKBjLgg7iaEZ8sSHlXFicFRSOiaYd727ibvJPWiclgicHjT6aNPWSwagw/0"/>';
                break;
            case "unsubscribe":
                $content = "取消关注";
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "COMPANY":
                        
                            $content = array();
                            $content[] = array("Title"=>"崇真艺客", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
                        
                        
                        break;
                    default:
                        $content = "点击菜单：".$object->EventKey;
                        break;
                }
                break;
            case "VIEW":
                $content = "跳转链接 ".$object->EventKey;
                break;
            case "SCAN":
                $sence_ticket = $object->Ticket;
                $data=$this->wechat_qrcode($sence_ticket);
                $this->logger("R \r\n".$data['image']);
                if($data && $data['image']){
                    $content[]=array(
                        'PicUrl' =>$data['image'], 
                        'Title'=>$data['title'],
                        'Description'=>$data['description'],
                        'Url'=>$data['linkurl']
                    );
                }else{
                    $content = "扫描场景 ".$data;
                }
                
                break;
            case "LOCATION":
                $content = "上传位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
                break;
            case "scancode_waitmsg":
                if ($object->ScanCodeInfo->ScanType == "qrcode"){
                    $content = "扫码带提示：类型 二维码 结果：".$object->ScanCodeInfo->ScanResult;
                }else if ($object->ScanCodeInfo->ScanType == "barcode"){
                    $codeinfo = explode(",",strval($object->ScanCodeInfo->ScanResult));
                    $codeValue = $codeinfo[1];
                    $content = "扫码带提示：类型 条形码 结果：".$codeValue;
                }else{
                    $content = "扫码带提示：类型 ".$object->ScanCodeInfo->ScanType." 结果：".$object->ScanCodeInfo->ScanResult;
                }
                break;
            case "scancode_push":
                $content = "扫码推事件";
                break;
            case "pic_sysphoto":
                $content = "系统拍照";
                break;
            case "pic_weixin":
                $content = "相册发图：数量 ".$object->SendPicsInfo->Count;
                break;
            case "pic_photo_or_album":
                $content = "拍照或者相册：数量 ".$object->SendPicsInfo->Count;
                break;
            case "location_select":
                $content = "发送位置：标签 ".$object->SendLocationInfo->Label;
                break;
            default:
                // $content = "receive a new event: ".$object->Event;
                break;
        }
        if(is_array($content)){
            if (isset($content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }
    // 获取微信自动关注回复
    public function wechat_subscribe(){
        $sql='select * from subscribe where user_id="'.$_GET['uid'].'"';
        $data = $this->database->query($sql)->fetch();
        if(count($data)>0){
            $return=array();
            $return['type']=$data['type'];
            if($data['type']=='1'){
                $return['content']=$data['content_des'];
            }else if($data['type']=='2'){
                $access_token=$this->access_token;
                $media_url='https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.$access_token;
                $post_data=array('media_id' =>$data['related_article']);
                $result=json_decode($this->https_request($media_url,json_encode($post_data)));
                $return=array(
                        'type'=>'2',
                        'content'=>$result
                    );
            }else if($data['type']=='3'){
                $return['content']=$data['linkurl'];
            }else if($data['type']=='4'){
                $img_src=unserialize($data['content']);
                $return['image']=$img_src[1];
                $return['title']=$data['title'];
                $return['description']=$data['description'];
                $return['linkurl']=$data['linkurl'];
            }
            return $return;
        }else{
            return false;
        }
    }
    //根据场景二维码ticket获取内容
    public function wechat_qrcode($qrcode_sence_ticket){
        $sql='select * from sence_qrcode where ticket="'.$qrcode_sence_ticket.'"';
        $datas = $this->database->query($sql)->fetch();
        if($datas){
            // 获取推送的图文推内容
            $sql="select * from qrcode_content where qrcode_type=0 and qrcode_id=".$datas['id'];
            $contents=$this->database->query($sql)->fetchAll();
            if(count($contents)>0){
                $img_src=unserialize($contents[0]['content']);
                $contents[0]['image']=$img_src[1];
                return $contents[0];
            }else{
                return false;
            }
            
        }else{
            $sql_temp='select * from qrcode_temp where qt_ticket="'.$qrcode_sence_ticket.'"';
            $temp_datas=$this->database->query($sql_temp)->fetch();
            if($temp_datas){
                // 获取推送的图文推内容
                $sql="select * from qrcode_content where qrcode_type=1 and qrcode_id=".$temp_datas['qt_id'];
                $contents=$this->database->query($sql)->fetchAll();
                if(count($contents)>0){
                    $img_src=unserialize($contents[0]['content']);
                    if(isset($img_src)){
                        $contents[0]['image']=$img_src[1];
                    }else{
                        $contents[0]['image']=$img_src[1];
                    }
                    return $contents[0];
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }
    //按关键字搜索回复的内容
    public function wechat_keyword($keyword){
        $sql='select * from keywords where keyword="'.$keyword.'" and uid="'.$_GET['uid'].'"';
        $data = $this->database->query($sql)->fetch();
        if(count($data)){
            if($data['type']=='1'){
                $output=array(
                        'type'=>'1',
                        'content'=>$data['content']
                    );
            }else if($data['type']=='2'){
                $access_token=$this->access_token;
                $media_url='https://api.weixin.qq.com/cgi-bin/material/get_material?access_token='.$access_token;
                $post_data=array('media_id' =>$data['related_article']);
                $result=json_decode($this->https_request($media_url,json_encode($post_data)));
                $output=array(
                        'type'=>'2',
                        'content'=>$result
                    );
            }else if($data['type']=='3'){
                $output=array(
                        'type'=>'3',
                        'content'=>$data['linkurl']
                    );
            }
            else if($data['type']=='4'){
                $keywords_content_sql="select * from keywords_content where keywords_id='".$data['id']."'";
                $keywords_content=$this->database->query($keywords_content_sql)->fetch();
                $output=array(
                        'type'=>'4',
                        'content'=>$keywords_content
                    );
            }
            return $output;
        }else{
            return false;
        }
    }
    //接收文本消息
    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        $data=$this->wechat_keyword($keyword);
        if($data){
            if($data['type']==2){
                $news_item=$data['content'];
                $content = array();
                foreach ($news_item->news_item as $key => $value) {
                    $content[] = array("Title"=>$value->title, "Description"=>$value->digest, "PicUrl"=>$value->thumb_url, "Url" =>$value->url);
                }
            }else if($data['type']==4){
                $content = array();
                $data=$data['content'];
                $thumb_url=unserialize($data['content']);
                $thumb_url=$thumb_url[1];
                $content[] = array("Title"=>$data['title'], "Description"=>$data['description'], "PicUrl"=>$thumb_url, "Url" =>$data['linkurl']);

            }else{
                $content=$data['content'];
            }
        }else{
            $content = $this->brand."没明白您在说什么";
        }
        //多客服人工回复模式
        // if (strstr($keyword, "请问在吗") || strstr($keyword, "在线客服")){
        //     $result = $this->transmitService($object);
        //     return $result;
        // }

        //自动回复模式
        // if (strstr($keyword, "文本")){
        //     $content = "这是个文本消息";
        // }else if (strstr($keyword, "表情")){
        //     $content = "中国：".$this->bytes_to_emoji(0x1F1E8).$this->bytes_to_emoji(0x1F1F3)."\n仙人掌：".$this->bytes_to_emoji(0x1F335);
        // }else if (strstr($keyword, "单图文")){
        //     $content = array();
        //     $content[] = array("Title"=>"单图文标题",  "Description"=>"单图文内容", "PicUrl"=>"http://s1.trueart.com/20160416/effdabea-50a0-4d54-92fb-5e8fb1c2ca85.jpg", "Url" =>"http://www.trueart.com/");
        // }else if (strstr($keyword, "图文") || strstr($keyword, "多图文")){
        //     $content = array();
        //     $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://s1.trueart.com/20160419/101853339.jpg", "Url" =>"http://www.trueart.com/");
        //     $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://s1.trueart.com/20160416/fb1ed5c9-94b0-4361-9004-3127de66eeb4.jpg", "Url" =>"http://www.trueart.com/");
        //     $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://s1.trueart.com/20160416/effdabea-50a0-4d54-92fb-5e8fb1c2ca85.jpg", "Url" =>"http://www.trueart.com/");
        // }else if (strstr($keyword, "音乐")){
        //     $content = array();
        //     $content = array("Title"=>"最炫民族风", "Description"=>"歌手：凤凰传奇", "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3", "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3"); 
        // }else{
        //     // $content = date("Y-m-d H:i:s",time())."\nOpenID：".$object->FromUserName."\n崇真艺客";
        //     $content = "\n崇真艺客没明白您在说什么";
        // }

        if(is_array($content)){
            if (isset($content[0])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
        return $result;
    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，经度为：".$object->Location_Y."；纬度为：".$object->Location_X."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }
        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)){
            return "";
        }

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);

        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return "";
        }
        $itemTpl = "        <item>
            <Title><![CDATA[%s]]></Title>
            <Description><![CDATA[%s]]></Description>
            <PicUrl><![CDATA[%s]]></PicUrl>
            <Url><![CDATA[%s]]></Url>
        </item>
";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <ArticleCount>%s</ArticleCount>
    <Articles>
$item_str    </Articles>
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        if(!is_array($musicArray)){
            return "";
        }
        $itemTpl = "<Music>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <MusicUrl><![CDATA[%s]]></MusicUrl>
        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
    </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[music]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
        <MediaId><![CDATA[%s]]></MediaId>
    </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[image]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
        <MediaId><![CDATA[%s]]></MediaId>
    </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[voice]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
        <MediaId><![CDATA[%s]]></MediaId>
        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[video]]></MsgType>
    $item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复多客服消息
    private function transmitService($object)
    {
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }


    //回复第三方接口消息
    private function relayPart3($url, $rawData)
    {
        $headers = array("Content-Type: text/xml; charset=utf-8");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    //字节转Emoji表情
    function bytes_to_emoji($cp)
    {
        if ($cp > 0x10000){       # 4 bytes
            return chr(0xF0 | (($cp & 0x1C0000) >> 18)).chr(0x80 | (($cp & 0x3F000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x800){   # 3 bytes
            return chr(0xE0 | (($cp & 0xF000) >> 12)).chr(0x80 | (($cp & 0xFC0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else if ($cp > 0x80){    # 2 bytes
            return chr(0xC0 | (($cp & 0x7C0) >> 6)).chr(0x80 | ($cp & 0x3F));
        }else{                    # 1 byte
            return chr($cp);
        }
    }

    //日志记录
    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 1000000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
    }
    public function https_request($url, $data = null) {
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
}
?>