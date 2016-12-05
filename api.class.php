<?php
// 渲染头部ut8格式
header("Content-type: text/html; charset=utf-8"); 
class api{
    protected $database = '';
    // 构造函数
    public function __construct($database){
        $this->database = $database;
        $this->appid = 'wxcf102de002227700';
        $this->secret='985030841dc69c20e7394fe0d9267979';
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
        $sql='select * from qrcode_content where user_id="'.$_SESSION['uid'].'" and qrcode_id='.$_POST['qrcode_id'];
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
        $sql='select * from subscribe where user_id=1';
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
        $sql='select * from keywords where keyword="'.$keyword.'"';
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
            default:
                $insert_data['content']='暂时不知你需要什么';
                break;
        }
        $keywords_id=$this->database->insert('keywords',$insert_data);
        if($keywords_id){
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
}

?> 