<?php
require_once 'medoo.php';
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="css/skin.css" rel="stylesheet" type="text/css" />
<!-- <link href="css/admin.css" rel="stylesheet" type="text/css"> -->
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap-theme.min.css">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<style type="text/css">
	.table table-striped tr td{
		vertical-align:middle;
		text-align:center;
	}
</style>
</head>

<body>
<?php if($_GET['action']=='resetpwd'):?>
	<form method = "post" action = "admin.php"class="passport-form passport-form-sign" id="update">
		<div class="">
		    <div class="">
		        <input autocomplete="off" name="password_a"  placeholder="输入新密码" type="password">
			</div>
		</div>
		<div class="">
		    <div class="">
		        <input autocomplete="off" name="password_b"  placeholder="确认新密码" type="password">
		    </div>
		</div>
		<div class="form-item">
		    <div class="form-cont">
		        <input type="submit" value="修改" id="update" class="passport-btn passport-btn-def xl w-full"/>
		    </div>
		</div>
		<input name="action" type="hidden" value="update" >

	</form>
<?php elseif($_GET['action']=='userlist'&& $_SESSION['role']=='1'): ?>
	<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="tit2" valign="top" align="left">
				用户管理
			</td>
			<td class="tit2" valign="top" align="center">
				<a href="user.php?action=adduser" target="main">新建用户</a>

				

			</td>
		</tr>
	</table>
<?php 
	$user_sql="select * from user where uid!='".$_SESSION['uid']."'";
	$users=$database->query($user_sql)->fetchAll();
	echo "<pre>";
	foreach ($users as $key => $value) {
		print_r($users);
	}
?>
<?php elseif($_GET['action']=='adduser'&& $_SESSION['role']=='1'): ?>
	<form action="adduser" method="post">
		<input type="text" name="username" placeholder="输入账号"><br>
		<input type="password" name="password" placeholder="输入密码"><br>
		<input type="submit" value="确认">
	</form>
<?php endif;?>
</body>
    
</html>