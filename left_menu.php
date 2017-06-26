<?php session_start();?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <link href="css/reset.css" rel="stylesheet" />
    <link href="css/user.css" rel="stylesheet" />
</head>
<body style="background-color: #EEF2FB;">

    <div>

        <section class="user-sidebar">

            <div class="user-sidebar-box user-account-nav">
                <ul class="user-account-nav-list clearfix">
                    <li class="user-account-nav-list-item"><a class="user-account-nav-item"><i class="user-nav-main"></i><span>管理类别</span> </a></li>
                    <li class="user-account-nav-list-item">
                        <div class="user-account-nav-item">
                            <i class="user-nav-message-manage"></i><span>菜单管理</span>
                        </div>
                        <ul class="clearfix">
                            <li><a href="list.php?action=menu" target="main" title="">菜单管理</a></li>
                        </ul>
                    </li>
                   <!--  <li class="user-account-nav-list-item">
                        <div class="user-account-nav-item">
                            <i class="user-nav-message-manage"></i><span>发送管理</span>
                        </div>
                        <ul class="clearfix">
                            <li><a href="list.php?action=smg" target="main" title="">发送管理</a> </li>
                            <li><a href="wechat_api/get_news.php?action=syn" target="main" title="">同步图文数据</a></li>
                        </ul>
                    </li> -->
                    <li class="user-account-nav-list-item">
                        <div class="user-account-nav-item">
                            <i class="user-nav-message-manage"></i><span>其他功能</span>
                        </div>
                        <ul class="clearfix">
                            <li><a href="list.php?action=keywords" target="main" title="">关键字自动回复</a></li>
                            <li><a href="list.php?action=subscribe" target="main" title="">关注自动回复</a> </li>
                            <li><a href="list.php?action=qrcode" target="main" title="">场景二维码生成</a></li>
                        </ul>
                    </li>
                <?php if($_SESSION['role']=='1'):?>
                    <li class="user-account-nav-list-item">
                        <div class="user-account-nav-item">
                            <i class="user-nav-message-manage"></i><span>用户管理</span>
                        </div>
                        <ul class="clearfix">
                            <li><a href="user.php?action=userlist" target="main" title="">用户列表</a></li>
                        </ul>
                    </li>
                <?php endif;?>
                </ul>
            </div>
        </section>

    </div>

    <script src="js/jquery.js"></script>
    <script src="js/user-nav.js"></script>
</body>
</html>
