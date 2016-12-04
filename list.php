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
<?php if($_GET['action'] == 'menu'){ 
	$sql="select * from menu order by parentid asc, sort asc";
	$datas = $database->query($sql)->fetchAll();
?>
<div id="main">
	<div class="content">
		
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">菜单</div>
				</td>
				<td class="tit2" valign="top" align="center">
					<a href="manage.php?action=menu" target="main">新建菜单</a>
				</td>
				<td class="tit2" valign="top" align="center">
					<a href="wechat_api/menu.php" target="main">生成微信菜单</a>
				</td>
			</tr>
		</table>
		<table class="table table-striped" width="100%"  cellspacing="0" cellpadding="0">
			<tr>
				<td>菜单标题</td>
				<td>上级菜单</td>
				<td>菜单链接</td>
				<td>所选图文</td>
				<td>排序</td>
				<td>创建时间</td>
				<td>操作</td>
			</tr>

			<?php 
			foreach ($datas as $list_item){	 
			?>
			<tr>
				<td valign="top" align="center">
					<?php echo $list_item['menutitle']?>
				</td>
				<td>
					<?php 
						if($list_item['parentid']):
							$parent='select menutitle from menu where menuid='.$list_item['parentid'];
							$parent_title=$database->query($parent)->fetch;
							echo $parent_title['menutitle'];
						else:
							echo '一级菜单';
						endif;
					 ?>
				</td>
				<td style="vertical-align: middle;text-align: center;max-width: 350px;overflow: scroll;">
					<?php echo $list_item["linkurl"];?>
				</td>
				<td>
					<?php
						$news_sql="select * from wechat_news where news_id =".$list_item['news_id'];
						$news=$database->query($news_sql)->fetchAll();
						if(count($news)){
							echo $news[0]['news_title'];
						}else{
							echo '网页链接不用此项';
						}
					?>
				</td>
				<td>
					<?php echo $list_item["sort"];?>
				</td>
				<td>
					<?php echo date('Y-m-d H:i:s',$list_item["created"]);?>
				</td>
				<td>
					<a href="manage.php?action=menumanage&id=<?php echo $list_item["menuid"]?>">编辑</a>
					<!-- 没有引用则可以删除 -->
					<?php
						$child_sql="select count(*) as num from menu where parentid=".$list_item["menuid"];
						$count=$database->query($child_sql)->fetchAll();
						if(!$count[0]['num']){
					?>
					<a href="api.php?action=menudelete&id=<?php echo $list_item["menuid"]?>">删除</a>
					<?php
						}
					?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>
<?php }elseif($_GET['action'] == 'subscribe'){
$sql="select * from subscribe where user_id=1 order by id desc";
$datas = $database->query($sql)->fetchAll();
?>
<div id="main">
	<div class="content">
		<form action="api.php" method="post" enctype="multipart/form-data">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" colspan='4' valign="top" width="15%" align="center">自动回复图文内容</td>
				</tr>
				<tr>
					<td>外链地址</td>
					<td class="tit2" colspan='3' valign="top" width="15%" align="center">
						<input type='text' name='linkurl' value="<?php print_r($datas[0]['linkurl']);?>" />
					</td>
				</tr>
				<tr>
					<td>标题</td>
					<td class="tit2" colspan='3'  valign="top" width="15%" align="center">
						<input type='text' name='title' value="<?php print_r($datas[0]['title']);?>"/>
					</td>
				</tr>
				<tr>
					<td>描述</td>
					<td class="tit2" colspan='3'  valign="top" width="15%" align="center">
						<textarea type='text' name='description'><?php print_r($datas[0]['description']);?></textarea>
					</td>
				</tr>
				<tr>
					<td>图文中间的图片</td>
					<td valign="top" align="center">
						<input type='file' name='content' />
					</td>

					<td colspan='2'>
						<?php if(!empty($datas[0]['content']) && $datas[0]['content']){
							$imgsrc=unserialize($datas[0]['content']);
						?>
							<img width='200px;' src="<?php echo $imgsrc[0];?>">
						<?php }?>
					</td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="center">
						<input type="submit" value="保存"/>
						<input type="hidden" name="action" value="addsubscribe"/>
					</td>
					
				</tr>
			</table>
		</form>
		
	</div>
</div>
<?php }elseif($_GET['action'] == 'qrcode'){
$sql="select * from sence_qrcode";
$datas = $database->query($sql)->fetchAll();
?>
	<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">场景二维码</div>
				</td>
				<td class="tit2" valign="top" align="center">
					<a href="manage.php?action=qrcode" target="main">新建二维码</a>
				</td>
			</tr>
		</table>
	<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="tit2" valign="top" width="15%" align="center">场景</td>
			<td class="tit2" valign="top" width="15%" align="center">二维码</td>
			<td class="tit2" valign="top" width="15%" align="center">编辑图文推</td>
			<td class="tit2" valign="top" width="15%" align="center">删除</td>
		</tr>

		<?php 
		foreach ($datas as $list_item){
		?>
		<tr>
			<td valign="top" align="center">
				<?php print $list_item["sence_name"] ?>
			</td>
			<td valign="top" align="center">
				<?php if(!empty($list_item['ticket'])): ?><img style='width:100px;' src="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=<?php print $list_item['ticket'];?>"/><?php endif;?>
			</td>
			<td valign="top" align="center">
				<a href='list.php?action=editqrcode&qrcode_id=<?php echo $list_item["id"];?>'>编辑</a>
			</td>
			<td valign="top" align="center">
				<a href='api.php?action=delqrcode&del_id=<?php echo $list_item["id"];?>'>删除</a>
			</td>
		</tr>
		<?php } ?>
	</table>
<?php }elseif($_GET['action'] == 'editqrcode'){
$sql="select * from qrcode_content where user_id=1 and qrcode_id=".$_GET['qrcode_id']." order by id desc";
$datas = $database->query($sql)->fetchAll();
?>
<div id="main">
	<div class="content">
		<form action="api.php" method="post" enctype="multipart/form-data">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" colspan='4' valign="top" width="15%" align="center">自动回复图文内容</td>
				</tr>
				<tr>
					<td>外链地址</td>
					<td class="tit2" colspan='3' valign="top" width="15%" align="center">
						<input type='text' name='linkurl' value="<?php print_r($datas[0]['linkurl']);?>" />
					</td>
				</tr>
				<tr>
					<td>标题</td>
					<td class="tit2" colspan='3'  valign="top" width="15%" align="center">
						<input type='text' name='title' value="<?php print_r($datas[0]['title']);?>"/>
					</td>
				</tr>
				<tr>
					<td>描述</td>
					<td class="tit2" colspan='3'  valign="top" width="15%" align="center">
						<textarea type='text' name='description'><?php print_r($datas[0]['description']);?></textarea>
					</td>
				</tr>
				<tr>
					<td>图文中间的图片</td>
					<td valign="top" align="center">
						<input type='file' name='content' />
					</td>

					<td colspan='2'>
						<?php if(!empty($datas[0]['content']) && $datas[0]['content']){
							$imgsrc=unserialize($datas[0]['content']);
						?>
							<img width='200px;' src="<?php echo $imgsrc[0];?>">
						<?php }?>
					</td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="center">
						<input type="submit" value="保存"/>
						<input type="hidden" name="qrcode_id" value="<?php echo $_GET['qrcode_id']?>"/>
						<input type="hidden" name="action" value="editqrcode"/>
					</td>
					
				</tr>
			</table>
		</form>
		
	</div>
</div>
<?php }elseif($_GET['action'] == 'smg'){
	$sql="select * from send_message";
	$datas = $database->query($sql)->fetchAll();
	?>
<div id="main">
	<div class="content">
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">自动回复图文</div>
				</td>
				<td class="tit2" valign="top" align="center">
					<a href="manage.php?action=smg">新建自动回复</a>
				</td>
			</tr>
		</table>
		
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" width="15%" align="center">回复名称</td>
				<td class="tit2" valign="top" width="15%" align="center">微信图文标题</td>
				<td class="tit2" valign="top" width="15%" align="center">排序</td>
				<td class="tit2" valign="top" width="15%" align="center">更新时间</td>
				<td class="tit2" valign="top" width="15%" align="center">操作</td>
			</tr>

			<?php 
			foreach ($datas as $list_item){	 
				$wechat_sql="select * from wechat_news where news_id=".$list_item['news_id'];
				$wechat_data=$database->query($wechat_sql)->fetchAll();
				$wechat_data=$wechat_data[0];
			?>
			<tr>
				<td valign="top" align="center">
					<?php print $list_item["title"] ?>
				</td>
				<td valign="top" align="center">
					<?php print $wechat_data['news_title'] ?>
				</td>
				<td valign="top" align="center">
					<?php print $list_item['sort'] ?>
				</td>
				<td valign="top" align="center">
					<?php print date('Y-m-d H:i:s',$wechat_data['update_time']) ?>
				</td>
				<td valign="top"  align="center">
					<a href="manage.php?action=smgmanage&id=<?php echo $list_item["id"];?>">编辑</a>
					<a href="api.php?action=smgdelete&id=<?php echo $list_item["id"];?>">删除</a>
				</td>
			</tr>
			<?php $i=$i+1;} ?>
		</table>
	</div>
</div>
<?php }elseif($_GET['action'] == 'keywords'){
	$sql="select * from keywords";
	$datas = $database->query($sql)->fetchAll();
	?>
<div id="main">
	<div class="content">
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">关键字自动回复</div>
				</td>
				<td class="tit2" valign="top" align="center">
					<a href="manage.php?action=keywords">新建关键字</a>
				</td>
			</tr>
		</table>
		
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" width="15%" align="center">关键字</td>
				<td class="tit2" valign="top" width="15%" align="center">回复类型</td>
				<td class="tit2" valign="top" width="15%" align="center">操作</td>
			</tr>

			<?php 
			foreach ($datas as $list_item){	 
				// $wechat_sql="select * from wechat_news where news_id=".$list_item['news_id'];
				// $wechat_data=$database->query($wechat_sql)->fetchAll();
				// $wechat_data=$wechat_data[0];
			?>
			<tr>
				<td valign="top" align="center">
					<?php print $list_item["keyword"] ?>
				</td>
				<td valign="top" align="center">
					<?php 
						switch ($list_item["type"]) {
				            case '1':
				                $output='文本类型';
				                break;
				            case '2':
				                $output='图文推';
				                break;
				            case '3':
				                $output='链接';
				                break;
				            default:
				                $output='文本类型';
				                break;
				        };
				        echo $output;
					?>
				</td>
				<td valign="top"  align="center">
					<a href="manage.php?action=editkeywords&key_id=<?php echo $list_item["id"];?>">编辑</a>
					<a href="api.php?action=deletekeywords&keyword_id=<?php echo $list_item["id"];?>">删除</a>
				</td>
			</tr>
			<?php $i=$i+1;} ?>
		</table>
	</div>
</div>
<?php
}
?>
</body>
<script src="/js/jquery.js"></script>
<!-- <script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript">
	var ue = UE.getEditor('content',{
        initialFrameWidth: 1000,
        initialFrameHeight:500
    });
</script> -->

</html>