<?php
    session_start();
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>

</title>
        <style type="text/css">
        #PopupMsg {
            padding: 2px 10px;
            text-align: center;
            border: 1px solid #C66;
            background: #fc3;
            color: #666;
            display: none;
            position: absolute;
            left: 45%;
            top: 20%;
        }
        a {
            color: #FFFFFF;
        }
    </style>
    <link href="css/admin.css" rel="stylesheet" /></head>
<body>
    <div class="admin_top" style="background: #fed993;">
        <div style="float: right;padding-right:40px;margin-top:10px">
            <a href="admin.php?action=logout" style="color: #000000">登出</a>
            <a href="user.php?action=resetpwd" target="main" style="color: #000000">修改密码</a>
        </div>
        <div style="float: left; margin-left: 5%">
           <span style="font-family: Microsoft YaHei;font-size: 20px;color: #000000;line-height: 50px;"><?php echo $_SESSION['brand'];?>微信公众平台管理系统 </span>
        </div>
	    <div id="PopupMsg"></div>
    </div>

</body>
</html>