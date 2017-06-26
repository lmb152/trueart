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
<div class="container">
	<?php if($_GET['action']=='resetpwd'):?>
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td class="tit2" valign="top" align="left">
						<div class="title">修改密码</div>
					</td>
				</tr>
			</tbody>
		</table>
		<form method = "post" action = "admin.php" class="passport-form passport-form-sign" id="update">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td>输入新密码</td>
					<td>
						<div class="form-group col-sm-9">
							<input autocomplete="off" name="password_a" class="form-control"  type="password">
						</div>
					</td>  
				</tr>
				<tr>
					<td>确认新密码</td>
					<td>
						<div class="form-group col-sm-9">
							<input autocomplete="off" name="password_b" class="form-control"  type="password">
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="5" valign="top" align="center">
						<div class="form-group col-sm-9" style="text-align:center;">
							<input class="btn btn-success" type="submit" value="保存" id="update" />
						</div>
						<input type="hidden" name="action" value="update"/>
					</td>
				</tr>
			</table>
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
		<table class="table table-striped" width="100%"  cellspacing="0" cellpadding="0">
				<tr style="text-align:center;">
				    <td>id</td>
					<td>用户名</td>
					<td colspan="2">开发者配置url</td>
					<td>token</td>
					<td>创建时间</td>
					<td>brand</td>
					<td>操作</td>
				</tr>
				<?php 
	            $user_sql="select * from user";
		        $users=$database->query($user_sql)->fetchAll();
		        // echo "<pre>";
		        foreach ($users as $key=>$value) {
			// print_r($users);
		        
		        ?>
		         <tr>
					<td valign="top" align="center">
						<?php echo $value['uid']?>
					</td>
		
					<td valign="top" align="center">
						<?php echo $value['uname']?>
					</td>
					<td valign="top" align="center" colspan='2'>
						http://wechat.trueart.com/Wechat_api/index.php?uid=<?php echo $value['uid']?>
					</td>
					<td valign="top" align="center">
						wechat_abcd_1234
					</td>
					<td valign="top" align="center">
						<?php echo date('Y-m-d ',$value["created"]);?>
					</td>
					<td valign="top" align="center">
						<?php echo $value['brand']?>
					</td>
					<td valign="top" align="center">
						<a href="user.php?action=edituser&uid=<?php echo $value['uid'];?>">编辑</a>
						<a href="admin.php?action=single_delete&id=<?php echo $value["uid"]?>">删除</a>
					</td>
				</tr>
				<?php } ?>
			</table>


	<?php elseif($_GET['action']=='adduser'&& $_SESSION['role']=='1'): ?>
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td class="tit2" valign="top" align="left">
						<div class="title">新建用户</div>
					</td>
				</tr>
			</tbody>
		</table>
		<form action="admin.php" method="post" id="adduser">
		<!-- <td>用户名</td> -->
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td>输入账号</td>
					<td>
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" name="username" />
						</div>
					</td>
				</tr>
				<tr>
					<td>输入密码</td>
					<td>
						<div class="form-group col-sm-9">
	                        <input type='text' class="form-control" name="password" /> 
						</div>
					</td>
				</tr>
				<tr>
					<td>输入appid</td>
					<td>
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" name="appid" />
						</div>
					</td>
				</tr>
				<tr>
					<td>输入secret</td>
					<td>
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" name="secret" />
						</div>
					</td>
				</tr>
				<tr>
					<td>输入brand</td>
					<td>
						<div class="form-group col-sm-9" style="align=center">
							<input type='text' class="form-control" name="brand" />
						</div>
					</td>
				</tr>
		</table>
    	
                <div class="form-group col-sm-9" style="text-align:center;">
					<input class="btn btn-success" type="submit" value="保存" id="adduser" />
				</div>
				<input type="hidden" name="action" value="adduser"/>
		
		</form>
	<?php elseif($_GET['action']=='edituser'&& $_SESSION['role']=='1'): ?>
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">用户编辑</div>
				</td>
			</tr>
		</table>
		<?php 
			$user_sql="select * from user where uid='".$_GET['uid']."'";
			$user=$database->query($user_sql)->fetch();
		?>
		<form action="admin.php" method="post" >
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td>用户名</td>
					<td>
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" readonly="true" value="<?php echo $user['uname']?>" name="uname"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>appid</td>
					<td>
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" value="<?php echo $user['appid']?>" name="appid"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>secret</td>
					<td>
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" value="<?php echo $user['secret']?>" name="secret"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>brand</td>
					<td>
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" value="<?php echo $user['brand']?>" name="brand"/>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="5" valign="top" align="center">
						<input class="btn btn-success" type="submit" value="保存"/>
						<input type="hidden" name="action" value="edituser"/>
						<input type="hidden" name="uid" value="<?php echo $_GET['uid'];?>"/>
					</td>
					
				</tr>
			</table>
		</form>
	<?php endif;?>
</div>
</body>
    
</html>