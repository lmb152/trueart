<?php
// 渲染头部ut8格式
header("Content-type: text/html; charset=utf-8"); 
class api{
    protected $database = '';
    // 构造函数
    public function __construct($database){
        $this->database = $database;
        $user_sql="select * from user where uid='".$_SESSION['uid']."'";
        $userinfo=$database->query($user_sql)->fetch();
        $this->appid = $userinfo['appid'];
        $this->secret=$userinfo['secret'];
    }

    // 新建menu
    public function addmenu(){
        $menu['menutitle']=trim($_POST['menutitle']);
        $menu['parentid']=$_POST['parentmenu'];
        $menu['created']=time();
        $menu['msg_id']=$_POST['newsid']=="0"?:$_POST['newsid'];
        $menu['sort']=trim($_POST['menusort']);
        $menu['linkurl']=trim($_POST['linkurl']);
        $menu['uid']=$_SESSION['uid'];
        $menuid=$this->database->insert('menu',$menu);
        if($menuid){
            header("Location:list.php?action=menu");
        }else{
            return array('msg' => '添加失败', 'status'=>'0');
        }  
    }

    // 更新menu
    public function editmenu(){
        $menuid=$_POST['menuid'];
        if(!$menuid){
            exit("暂无更新项");
        }
        $menu['menutitle']=trim($_POST['menutitle']);
        $menu['parentid']=$_POST['parentmenu'];
        $menu['created']=time();
        $menu['msg_id']=$_POST['newsid'];
        $menu['sort']=trim($_POST['menusort']);
        $menu['linkurl']=trim($_POST['linkurl']);
        $menu['uid']=$_SESSION['uid'];
        $menuid=$this->database->update('menu',$menu,array('menuid=' => $menuid ));
        if($menuid){
            header("Location:list.php?action=menu");
        }else{
            return array('msg' => '添加失败', 'status'=>'0');
        }  
    }

    // 删除menu
    public function menudelete(){
        $menuid=$_GET['id'];
        if(!$menuid){
            exit("暂无删除项");
        }
        $this->database->delete('menu',array('menuid='=>$menuid));
        header("Location:list.php?action=menu");
    }
    
    // 新建menu
    public function addsmg(){
        $smg['title']=trim($_POST['title']);
        $smg['news_id']=$_POST['wechat'];
        $smg['sort']=$_POST['sort'];
        $smg['uid']=$_SESSION['uid'];
        $smgid=$this->database->insert('send_message',$smg);
        if($smgid){
            header("Location:list.php?action=smg");
        }else{
            return array('msg' => '添加失败', 'status'=>'0');
        }  
    }

    // 更新menu
    public function editsmg(){
        $smgid=$_POST['smgid'];
        if(!$smgid){
            exit("暂无更新项");
        }
        $smg['title']=trim($_POST['title']);
        $smg['news_id']=$_POST['wechat'];
        $smg['sort']=$_POST['sort'];
        $smg['uid']=$_SESSION['uid'];
        $smgid=$this->database->update('send_message',$smg,array('id=' => $smgid ));
        if($smgid){
            header("Location:list.php?action=smg");
        }else{
            return array('msg' => '添加失败', 'status'=>'0');
        }  
    }

    // 删除menu
    public function smgdelete(){
        $smgid=$_GET['id'];
        $this->database->delete('send_message',array('id='=>$smgid));
        header("Location:list.php?action=smg");
    }
    // 生成场景二维码
    public function addqrcode(){
        // $sdk = new WX_SDK($appid, $secret);
        $access_token_url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
        $access_token=json_decode($this->https_request($access_token_url))->access_token;
        $ticket_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        $scene_str=time();
        $arg=array(
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => array
                (
                    'scene' => array
                        (
                            'scene_str' => $scene_str
                        )

                )
        );
        $ticket=$this->https_request($ticket_url,json_encode($arg));
        $qrcode['ticket']=json_decode($ticket)->ticket;
        $qrcode['sence_name']=$_POST['sence_name'];
        // $qrcode['linkurl']=$_POST['linkurl'];

        $qrcode['sence_id']=$scene_str;
        $qrcode['uid']=$_SESSION['uid'];
        $qrcodeid=$this->database->insert('sence_qrcode',$qrcode);
        if($qrcodeid){

            header("Location:list.php?action=qrcode");
        }else{
            return array('msg' => '添加失败', 'status'=>'0');
        } 
    }
    // 生成临时二维码
    public function add_tempqrcode(){
        // // $sdk = new WX_SDK($appid, $secret);
        // echo "<pre>";
        // print_r($_POST);exit;
        $qt_name=$_POST['qt_name'];
        $qt_type=$_POST['qt_type'];
        $qt_linkurl=isset($_POST['qt_linkurl'])?$_POST['qt_linkurl']:'';
        $qt_imgsrc=!empty($_POST['qt_imgsrc'])?$_POST['qt_imgsrc']:'http://s.trueart.com/v2/img/ad/tuwentui_default.jpg';
        $qt_description=$_POST['qt_description'];
        $qt_mid=$_POST['qt_mid'];
        $qt_infoid=$_POST['qt_infoid'];
        // 查询是否存在该二维码且并未过期
        $query_sql="select * from qrcode_temp where qt_mid='".$qt_mid."' and qt_infoid='".$qt_infoid."' and qt_type='".$qt_type."'";
        $result=$this->database->query($query_sql)->fetch();
        if($result && $result['expired']>time()){
            echo json_encode(array('url' => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$result['qt_ticket']));
            exit;
        }else{
            // 重新生成二维码
            $appid='wxcf102de002227700';
            $secret='985030841dc69c20e7394fe0d9267979';
            $access_token_url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
            $access_token=json_decode($this->https_request($access_token_url))->access_token;
            $ticket_url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
            $scene_str=time();
            $expired_time=time()+2592000;
            $arg=array(
                'expire_seconds'=>2592000,
                'action_name' => 'QR_SCENE',
                'action_info' => array
                    (
                        'scene' => array
                            (
                                'scene_str' => $scene_str
                            )

                    )
            );
            $ticket=$this->https_request($ticket_url,json_encode($arg));
            $qrcode['qt_ticket']=json_decode($ticket)->ticket;
            $qrcode['linkurl']=$_POST['linkurl'];

            $qrcode['qt_name']=$qt_name;
            $qrcode['qt_type']=$qt_type;
            $qrcode['expired']=$expired_time;
            $qrcode_data=array(
                    'qt_ticket'=>$qrcode['qt_ticket'],
                    'qt_name'=>$qrcode['qt_name'],
                    'qt_type'=>$qrcode['qt_type'],
                    'expired'=>$qrcode['expired'],
                    'qt_mid'=>$qt_mid,
                    'qt_infoid'=>$qt_infoid
                );
            $qrcodeid=$this->database->insert('qrcode_temp',$qrcode_data);
            // 根据post参数生成对应的content
            if($qt_type=='1'){
                $img_src=$this->saveImage($qt_imgsrc);
                if(!isset($img_src)){
                    $img_src=$this->saveImage('http://s.trueart.com/v2/img/ad/tuwentui_default.jpg');
                }
                
                $postMedia['access_token']=$access_token;
                $postMedia['media']= $_SERVER['DOCUMENT_ROOT'].'/'.$img_src;
                // $exec='curl -F media=@'.$postMedia['media'].' "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'].'"';
                // exec($exec,$output,$return_val);
                // print_r($return_val);exit;
                $rt1=$this->https_request('https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'],array('media'=>'@'.$postMedia['media']));
                if($rt1){
                    $file_src_wechat=json_decode($rt1)->url;
                }
                $file_src_all[]=$file_src;
                $file_src_all[]=$file_src_wechat;
                $data=array(
                        'content'=>serialize($file_src_all),
                        'user_id'=>1,
                        'linkurl'=>$qt_linkurl,
                        'title'=>$qt_name,
                        'description'=>$qt_description,
                        'qrcode_id'=>$qrcodeid,
                        'qrcode_type'=>1
                    );
            }else{
                $data=array(
                        'content'=>'',
                        'user_id'=>1,
                        'linkurl'=>$qt_linkurl,
                        'title'=>$qt_name,
                        'description'=>$qt_description,
                        'qrcode_id'=>$qrcodeid,
                        'qrcode_type'=>1
                    );
            }
            $this->database->insert('qrcode_content',$data);
            if($qrcodeid){
                echo json_encode(array('url' => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$qrcode['qt_ticket']));
                exit;
            }else{
                echo  json_encode(array('msg' => '添加失败', 'status'=>'0'));
                exit;
            } 
        }
        
    }
    // 编辑场景二维码
    public function editqrcode(){
        if(isset($_FILES["content"]) && !empty($_FILES["content"]["tmp_name"])){

            if (($_FILES["content"]["type"] == "image/jpeg") && ($_FILES["content"]["size"] < 2048000)){
                if ($_FILES["content"]["error"] > 0){
                    echo "Return Code: " . $_FILES["content"]["error"] . "<br />";
                }
                else{
                    $extension=pathinfo($_FILES["content"]["name"]);
                    $extension=$extension['extension'];
                    $file_new_name=time().'.'.$extension;
                    $file_src="upload/" . $file_new_name;
                    $upload_result=move_uploaded_file($_FILES["content"]["tmp_name"],$file_src);
                    if($upload_result){
                        $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
                        $json=json_decode($this->https_request($url_get));
                        $postMedia['access_token']=$json->access_token;
                        $postMedia['media']= $_SERVER['DOCUMENT_ROOT'].'/'.$file_src;
                        // $exec='curl -F media=@'.$postMedia['media'].' "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'].'"';
                        // exec($exec,$output,$return_val);
                        // print_r($return_val);exit;
                        $rt1=$this->https_request('https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'],array('media'=>'@'.$postMedia['media']));
                        if($rt1){
                            $file_src_wechat=json_decode($rt1)->url;
                        }
                    }
                    
                }
                $file_src_all[]=$file_src;
                $file_src_all[]=$file_src_wechat;
                $qrcode_content['content']=serialize($file_src_all);
            }else{
                echo '文件格式不正确或者文件过大';
            }
        }
        $qrcode_content['user_id']=$_SESSION['uid'];
        $qrcode_content['linkurl']=$_POST['linkurl'];
        $qrcode_content['title']=$_POST['title'];
        $qrcode_content['description']=$_POST['description'];
        $qrcode_content['qrcode_id']=$_POST['qrcode_id'];
        $qrcode_content['qrcode_type']=0;
        $sql='select * from qrcode_content where user_id="'.$_SESSION['uid'].'" and qrcode_type=0 and qrcode_id='.$_POST['qrcode_id'];
        $tmp=$this->database->query($sql)->fetchAll();
        if(!empty($tmp)){
            $qrcode_contentid=$this->database->update('qrcode_content',$qrcode_content,array('id=' => $tmp[0]['id']));
        }else{
            $qrcode_contentid=$this->database->insert('qrcode_content',$qrcode_content);
        }
        if($qrcode_contentid){
            header("Location:list.php?action=qrcode");
        }else{
            return array('msg' => '添加失败', 'status'=>'0');
        } 
    }
    // 删除场景二维码
    public function delqrcode(){
        $del_id=$_GET['del_id'];
        $result=$this->database->delete('sence_qrcode',array('id='=>$del_id));
        if($result){
            header("Location:list.php?action=qrcode");
        }else{
            return array('msg' => '删除失败', 'status'=>'0');
        }
    }
    // 自动回复
    public function addsubscribe(){
        // $subscribe['content']=$_POST['content'];
        // 若存在图片
        echo "<pre>";
        $type=$_POST['type'];
        $subscribe['type']=$type;
        if($type=='1'){
            $subscribe['user_id']=$_SESSION['uid'];
            $subscribe['content_des']=$_POST['content_des'];
        }elseif($type=='2'){
            $subscribe['user_id']=$_SESSION['uid'];
            $subscribe['related_article']=$_POST['related_article'];
        }elseif($type=='3'){
            $subscribe['user_id']=$_SESSION['uid'];
            $subscribe['linkurl']=$_POST['linkurl1'];
        }elseif($type=='4'){
            if(isset($_FILES["content"]) && !empty($_FILES["content"]["tmp_name"])){
                if (($_FILES["content"]["type"] == "image/jpeg") && ($_FILES["content"]["size"] < 2048000)){
                    if ($_FILES["content"]["error"] > 0){
                        echo "Return Code: " . $_FILES["content"]["error"] . "<br />";
                    }
                    else{
                        $extension=pathinfo($_FILES["content"]["name"]);
                        $extension=$extension['extension'];
                        $file_new_name=time().'.'.$extension;
                        $file_src="upload/" . $file_new_name;
                        $upload_result=move_uploaded_file($_FILES["content"]["tmp_name"],$file_src);
                        if($upload_result){
                            $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
                            $json=json_decode($this->https_request($url_get));
                            $postMedia['access_token']=$json->access_token;
                            $postMedia['media']= $_SERVER['DOCUMENT_ROOT'].'/'.$file_src;
                            // $exec='curl -F media=@'.$postMedia['media'].' "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'].'"';
                            // exec($exec,$output,$return_val);
                            // print_r($return_val);exit;
                            $rt1=$this->https_request('https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'],array('media'=>'@'.$postMedia['media']));
                            if($rt1){
                                $file_src_wechat=json_decode($rt1)->url;
                            }
                        }
                        
                    }
                    $file_src_all[]=$file_src;
                    $file_src_all[]=$file_src_wechat;
                    $subscribe['content']=serialize($file_src_all);
                }else{
                    echo '文件格式不正确或者文件过大';
                }
            }
            
            $subscribe['user_id']=$_SESSION['uid'];
            $subscribe['linkurl']=$_POST['linkurl'];
            $subscribe['title']=$_POST['title'];
            $subscribe['description']=$_POST['description'];
        }
        $sql='select * from subscribe where user_id="'.$_SESSION['uid'].'"';
        $tmp=$this->database->query($sql)->fetchAll();
        if(!empty($tmp)){
            $subscribeid=$this->database->update('subscribe',$subscribe,array('id=' => $tmp[0]['id']));
        }else{
            $subscribeid=$this->database->insert('subscribe',$subscribe);
        }
        if($subscribeid){
            header("Location:list.php?action=subscribe");
        }else{
            return array('msg' => '添加失败', 'status'=>'0');
        } 
    }
    // 添加关键字
    public function addkeywords(){
        $keyword=trim($_POST['keyword']);
        $sql='select * from keywords where keyword="'.$keyword.'" and uid="'.$_SESSION['uid'].'"';
        $tmp=$this->database->query($sql)->fetchAll();
        if(count($tmp)>0){
            return array('msg' => '添加失败', 'status'=>'0');
        }
        $type=$_POST['type'];
        $insert_data=array(
                'keyword'=>$keyword,
                'type'=>$type,
                'uid'=>$_SESSION['uid']
            );
        switch ($type) {
            case '1':
                $insert_data['content']=$_POST['content'];
                break;
            case '2':
                $insert_data['related_article']=$_POST['related_article'];
                break;
            case '3':
                $insert_data['linkurl']=$_POST['linkurl'];
                break;
            case '4':
                if(isset($_FILES["file_content"]) && !empty($_FILES["file_content"]["tmp_name"])){
                    if (($_FILES["file_content"]["type"] == "image/jpeg") && ($_FILES["file_content"]["size"] < 2048000)){
                        if ($_FILES["file_content"]["error"] > 0){
                            echo "Return Code: " . $_FILES["file_content"]["error"] . "<br />";
                        }
                        else{
                            $extension=pathinfo($_FILES["file_content"]["name"]);
                            $extension=$extension['extension'];
                            $file_new_name=time().'.'.$extension;
                            $file_src="upload/" . $file_new_name;
                            $upload_result=move_uploaded_file($_FILES["file_content"]["tmp_name"],$file_src);
                            if($upload_result){
                                $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
                                $json=json_decode($this->https_request($url_get));
                                $postMedia['access_token']=$json->access_token;
                                $postMedia['media']= $_SERVER['DOCUMENT_ROOT'].'/'.$file_src;
                                // $exec='curl -F media=@'.$postMedia['media'].' "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'].'"';
                                // exec($exec,$output,$return_val);
                                // print_r($return_val);exit;
                                $rt1=$this->https_request('https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'],array('media'=>'@'.$postMedia['media']));
                                if($rt1){
                                    $file_src_wechat=json_decode($rt1)->url;
                                }
                            }
                            
                        }
                        $file_src_all[]=$file_src;
                        $file_src_all[]=$file_src_wechat;
                        $keywords['content']=serialize($file_src_all);
                    }else{
                        echo '文件格式不正确或者文件过大';
                    }
                }
                $update_content=array(
                    'content'=>$keywords['content'],
                    'linkurl'=>$_POST['linkurl'],
                    'title'=>$_POST['title'],
                    'description'=>$_POST['description'],
                    'keywords_id'=>0,
                    'uid'=>$_SESSION['uid']
                );
                $keywords_content_id=$this->database->insert('keywords_content',$update_content);
                break;
            default:
                $insert_data['content']='暂时不知你需要什么';
                break;
        }
        $keywords_id=$this->database->insert('keywords',$insert_data);
        if($keywords_id){
            $this->database->update('keywords_content',array('keywords_id'=>$keywords_id),array('id='=>$keywords_content_id));
            header("Location:list.php?action=keywords");
        }else{
            return array('msg' => '添加失败', 'status'=>'0');
        } 

    }
    // 编辑关键字
    public function editkeywords(){
        $keyword_id=trim($_POST['keyword_id']);
        $keyword=$_POST['keyword'];
        $type=$_POST['type'];
        $update_data=array(
                'keyword'=>$keyword,
                'type'=>$type,
                'uid'=>$_SESSION['uid']
            );
        switch ($type) {
            case '1':
                $update_data['content']=$_POST['content'];
                break;
            case '2':
                $update_data['related_article']=$_POST['related_article'];
                break;
            case '3':
                $update_data['linkurl']=$_POST['linkurl'];
                break;
            case '4':
                if(isset($_FILES["file_content"]) && !empty($_FILES["file_content"]["tmp_name"])){
                    if (($_FILES["file_content"]["type"] == "image/jpeg") && ($_FILES["file_content"]["size"] < 2048000)){
                        if ($_FILES["file_content"]["error"] > 0){
                            echo "Return Code: " . $_FILES["file_content"]["error"] . "<br />";
                        }
                        else{
                            $extension=pathinfo($_FILES["file_content"]["name"]);
                            $extension=$extension['extension'];
                            $file_new_name=time().'.'.$extension;
                            $file_src="upload/" . $file_new_name;
                            $upload_result=move_uploaded_file($_FILES["file_content"]["tmp_name"],$file_src);
                            if($upload_result){
                                $url_get='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->secret;
                                $json=json_decode($this->https_request($url_get));
                                $postMedia['access_token']=$json->access_token;
                                $postMedia['media']= $_SERVER['DOCUMENT_ROOT'].'/'.$file_src;
                                // $exec='curl -F media=@'.$postMedia['media'].' "https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'].'"';
                                // exec($exec,$output,$return_val);
                                // print_r($return_val);exit;
                                $rt1=$this->https_request('https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token='.$postMedia['access_token'],array('media'=>'@'.$postMedia['media']));
                                if($rt1){
                                    $file_src_wechat=json_decode($rt1)->url;
                                }
                            }
                            
                        }
                        $file_src_all[]=$file_src;
                        $file_src_all[]=$file_src_wechat;
                        $keywords['content']=serialize($file_src_all);
                    }else{
                        echo '文件格式不正确或者文件过大';
                    }
                }
                $update_content=array(
                    'linkurl'=>$_POST['linkurl'],
                    'title'=>$_POST['title'],
                    'description'=>$_POST['description'],
                    'keywords_id'=>$keyword_id,
                    'uid'=>$_SESSION['uid']
                );
                if($keywords['content']){
                    $update_content['content']=$keywords['content'];
                }
                if($_POST['keywords_content_id']){
                    $this->database->update('keywords_content',$update_content,array('id='=>$_POST['keywords_content_id']));
                }else{
                    echo $this->database->insert('keywords_content',$update_content);
                }
                break;
            default:
                $update_data['content']='暂时不知你需要什么';
                break;
        }
        $result=$this->database->update('keywords',$update_data,array('id=' => $keyword_id));
        if($result){
            header("Location:list.php?action=keywords");
        }else{
            return array('msg' => '编辑失败', 'status'=>'0');
        }
    }
    // 删除关键字
    public function deletekeywords(){
        $keyword_id=$_GET['keyword_id'];
        if(!$keyword_id){
            exit("暂无删除项");
        }
        $this->database->delete('keywords',array('id='=>$keyword_id));
        header("Location:list.php?action=keywords");
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
    public function curlPost($url, $data,$showError=1){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        $errorno=curl_errno($ch);
        if ($errorno) {
            return array('rt'=>false,'errorno'=>$errorno);
        }else{
            $js=json_decode($tmpInfo,1);
            if (intval($js['errcode']==0)){
                return array('rt'=>true,'errorno'=>0,'media_id'=>$js['media_id'],'msg_id'=>$js['msg_id']);
            }else {
                if ($showError){
                    return array('msg'=>'发生了Post错误：错误代码'.$js['errcode'].',微信返回错误信息：'.$js['errmsg']);
                }
            }
        }
    }
    // 下载并保存图片
    public function saveImage($path) {
        if(!preg_match('/\/([^\/]+\.[a-z]{3,4})$/i',$path,$matches))
        die('Use image please');
        $image_name = 'upload/'.strToLower($matches[1]);
        $ch = curl_init ($path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $img = curl_exec ($ch);
        curl_close ($ch);
        $fp = fopen($image_name,'w');
        fwrite($fp, $img);
        fclose($fp);
        return $image_name;
    }
}

?> 