<?php
	require_once "medoo.php";
	require_once "controller.class.php";
	$controller = new controller($database);
	
	if($_POST['action'] == "login"){
		// 传递登陆参数跳转url
		$controller->login();
	}
	if ($_POST['action']=="update") {
		$controller->update();
	}
	if ($_POST['action']=="edituser") {
		$controller->edituser();
	}
	if ($_POST['action']=="adduser") {
		$controller->adduser();
	}
	if($_POST['action'] == 'export'){
		// 导出数据
		$controller->export();
	}else if($_GET['action']=='clean'){
		// 清空数据
		$controller->clean();
	}else if($_GET['action']=='logout'){
		
		$controller->logout();
	}else if($_GET['action']=='single_delete'){
		
		$controller->single_delete();
	}

?>