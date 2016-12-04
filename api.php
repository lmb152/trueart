<?php
header("Content-type: text/html; charset=utf-8"); 
header('Content-type: application/json');
require_once 'medoo.php';
require_once 'api.class.php';
$api = new api($database);
if($_POST['action'] == 'addmenu'){
	// 添加菜单
	$api->addmenu();
}

if($_POST['action'] == 'editmenu'){
	// 更新菜单
	$api->editmenu();
}


if($_GET['action'] == 'menudelete'){
	// 删除菜单
	$api->menudelete();
}

if($_POST['action'] == 'addsmg'){
	// 添加图文发送
	$api->addsmg();
}

if($_POST['action'] == 'editsmg'){
	// 更新图文发送
	$api->editsmg();
}


if($_GET['action'] == 'smgdelete'){
	// 删除图文发送
	$api->smgdelete();
}
if($_POST['action'] == 'addqrcode'){
	// 新增场景二维码
	$api->addqrcode();
}
if($_POST['action'] == 'addsubscribe'){
	// 自动回复
	$api->addsubscribe();
}
if($_GET['action'] == 'delqrcode'){
	//删除场景二维码
	$api->delqrcode();
}
if($_POST['action'] == 'editqrcode'){
	// 编辑场景二维码图文推
	$api->editqrcode();
}
if($_POST['action'] == 'addkeywords'){
	// 添加关键字
	$api->addkeywords();
}
if($_GET['action'] == 'deletekeywords'){
	// 删除关键字
	$api->deletekeywords();
}
if($_POST['action'] == 'editkeywords'){
	// 编辑关键字
	$api->editkeywords();
}
?>