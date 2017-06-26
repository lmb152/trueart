<?php
  require_once "wx_sdk.php";
  $appId='wx79471f40b33ae84f';
  $secret='0891d9d89fb012683565031b5a85b731';
  $jssdk = new WX_SDK($appId, $secret);
  $url='http://wechat.trueart.com'.$_SERVER['REQUEST_URI'];
  $access_token=json_decode($jssdk->getAccessTokenOnly());
  $access_token=$access_token->access_token;
  $signPackage = $jssdk->GetSignPackage($url,$access_token);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>微信JS-SDK Demo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
   <link rel="stylesheet" href="http://demo.open.weixin.qq.com/jssdk/css/style.css?ts=1420774989">    
</head>
<body>
<div class="wxapi_container">
    <div class="lbox_close wxapi_form">
      <h3 id="menu-voice">音频接口</h3>
      开始录音接口
      <button class="btn btn_primary" id="startRecord">startRecord</button>
      停止录音接口
      <button class="btn btn_primary" id="stopRecord">stopRecord</button>
      播放语音接口
      <button class="btn btn_primary" id="playVoice">playVoice</button>
      暂停播放接口
      <button class="btn btn_primary" id="pauseVoice">pauseVoice</button>
      停止播放接口
      <button class="btn btn_primary" id="stopVoice">stopVoice</button>
      上传语音接口
      <button class="btn btn_primary" id="uploadVoice">uploadVoice</button>
    <!--   下载语音接口
      <button class="btn btn_primary" id="downloadVoice">downloadVoice</button> -->

      <h3 id="menu-smart">智能接口</h3>
      识别音频并返回识别结果接口
      <button class="btn btn_primary" id="translateVoice">translateVoice</button>

    </div>
  </div>
</body>
<!--步骤二：引入JS文件  -->
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="../js/jquery.js"></script>
<script>
 
    //步骤三：通过config接口注入权限验证配置
  wx.config({
      debug: false,
      appId: '<?php echo $signPackage["appId"];?>',
      timestamp: <?php echo $signPackage["timestamp"];?>,
      nonceStr: '<?php echo $signPackage["nonceStr"];?>',
      signature: '<?php echo $signPackage["signature"];?>',
      jsApiList: [
        'checkJsApi',
        'translateVoice',
        'startRecord',
        'stopRecord',
        'onRecordEnd',
        'playVoice',
        'pauseVoice',
        'stopVoice',
        'uploadVoice',
        'downloadVoice',
      ]
  });
</script>
<script> 

//步骤四：通过ready接口处理成功验证

wx.ready(function () {
  // 3 智能接口
  var voice = {
    localId: '',
    serverId: ''
  };
  // 3.1 识别音频并返回识别结果
  $('#translateVoice').click(function () {
    if (voice.localId == '') {
      alert('请先使用 startRecord 接口录制一段声音');
      return;
    }
    wx.translateVoice({
      localId: voice.localId,
      complete: function (res) {
        if (res.hasOwnProperty('translateResult')) {
          alert('识别结果：' + res.translateResult);
        } else {
          alert('无法识别');
        }
      }
    });
  });

  // 4 音频接口
  // 4.2 开始录音
  $('#startRecord').click(function () {
    wx.startRecord({
      cancel: function () {
        alert('用户拒绝授权录音');
      }
    });
  });

  // 4.3 停止录音
  $('#stopRecord').click(function () {
    wx.stopRecord({
      success: function (res) {
        voice.localId = res.localId;
        // 上传语音
        wx.uploadVoice({
          localId: voice.localId,
          success: function (res) {
            // 下载语音到本地服务器
            voice.serverId = res.serverId;
            $.post("record_save.php",{media_id:voice.serverId,access_token:'<?php echo $access_token;?>'},function(result){
              alert(result);
            });
            // wx.downloadVoice({
            //   serverId: voice.serverId,
            //   success: function (res) {
            //     alert('下载语音成功，localId 为' + res.localId);
            //     voice.localId = res.localId;
            //   }
            // });
            
          }
        });
      },
      fail: function (res) {
        alert(JSON.stringify(res));
      }
    });
  });

  // 4.4 监听录音自动停止
  wx.onVoiceRecordEnd({
    complete: function (res) {
      voice.localId = res.localId;
      alert('录音时间已超过一分钟');
    }
  });

  // 4.5 播放音频
  $('#playVoice').click(function(){
    if (voice.localId == '') {
      alert('请先使用 startRecord 接口录制一段声音');
      return;
    }
    wx.playVoice({
      localId: voice.localId
    });
  });

  // 4.6 暂停播放音频
  $('#pauseVoice').click(function () {
    wx.pauseVoice({
      localId: voice.localId
    });
  });

  // 4.7 停止播放音频
  $('#stopVoice').click(function () {
    wx.stopVoice({
      localId: voice.localId
    });
  });

  // 4.8 监听录音播放停止
  wx.onVoicePlayEnd({
    complete: function (res) {
      alert('录音（' + res.localId + '）播放结束');
    }
  });

  // 4.8 上传语音
  $('#uploadVoice').click(function () {
    if (voice.localId == '') {
      alert('请先使用 startRecord 接口录制一段声音');
      return;
    }
    wx.uploadVoice({
      localId: voice.localId,
      success: function (res) {
        voice.serverId = res.serverId;
        $.post("record_save.php",{media_id:voice.serverId,access_token:'<?php echo $access_token;?>'},function(result){
              alert(result);
            });
      }
    });
  });

  // 4.9 下载语音
  // $('#downloadVoice').click(function () {
  //   if (voice.serverId == '') {
  //     alert('请先使用 uploadVoice 上传声音');
  //     return;
  //   }
  //   wx.downloadVoice({
  //     serverId: voice.serverId,
  //     success: function (res) {
  //       alert('下载语音成功，localId 为' + res.localId);
  //       voice.localId = res.localId;
  //     }
  //   });
  // });

  var shareData = {
    title: '微信JS-SDK Demo',
    desc: '微信JS-SDK,帮助第三方为用户提供更优质的移动web服务',
    link: 'http://demo.open.weixin.qq.com/jssdk/',
    imgUrl: 'http://mmbiz.qpic.cn/mmbiz/icTdbqWNOwNRt8Qia4lv7k3M9J1SKqKCImxJCt7j9rHYicKDI45jRPBxdzdyREWnk0ia0N5TMnMfth7SdxtzMvVgXg/0'
  };
  wx.onMenuShareAppMessage(shareData);
  wx.onMenuShareTimeline(shareData);
});

wx.error(function (res) {
  alert(res.errMsg);
});
</script>
</html>