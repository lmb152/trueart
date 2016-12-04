<?php
require_once 'medoo.php';
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="css/skin.css" rel="stylesheet" type="text/css" />
<link href="css/admin.css" rel="stylesheet" type="text/css">

<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
</head>

<body>
<?php if($_GET['action'] == 'menu'){ ?>
<div id="main">
	<div class="content">
		
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">新建菜单</div>
				</td>
			</tr>
		</table>
		<form action="api.php" method="post" >
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" valign="top" width="15%" align="center">菜单标题</td>
					<td class="tit2" valign="top" width="15%" align="center">上级菜单</td>
					<td class="tit2" valign="top" width="15%" align="center">菜单链接</td>
					<td class="tit2" valign="top" width="15%" align="center">发送图文</td>
					<td class="tit2" valign="top" width="15%" align="center">排序</td>
				</tr>
				<tr>
					<td valign="top" align="center">
						<input type="text" name="menutitle" />
					</td>
					<td valign="top" align="center">
						<select name="parentmenu">
							<option value="0">一级菜单</option>
							<?php 
								$parent='select * from menu where parentid=0';
								$parent_menu=$database->query($parent)->fetchAll();
								foreach ($parent_menu as $key => $value) {
							?>
								<option value="<?php echo $value['menuid']?>"><?php echo $value['menutitle']?></option>
							<?php
								}
							 ?>
						</select>
						
					</td>
					<td valign="top" align="center">
						<input type="text" name="linkurl" />
					</td>
					<td valign="top" align="center">
						<select name="newsid" style="max-width:200px;">
							<?php 
								$news_all="select * from wechat_news order by update_time desc";
								$allnews=$database->query($news_all)->fetchAll();
							?>
							<option value="0">网页链接不用填写</option>
							<?php foreach ($allnews as $key => $value) {?>
								<option value="<?php echo $value['news_id']?>"><?php echo $value['news_title']?></option>
							<?php }?>
						</select>
					</td>
					<td valign="top" align="center">
						<input type="text" name="menusort" />
					</td>
					
				</tr>
				<tr>
					<td colspan="5" style="text-align:center;">
						<input type="submit" value="保存" />
						<input type="hidden" value="addmenu" name="action"/>
					</td>
				</tr>
			</table>
		</form>
		
	</div>
</div>
<?php }elseif($_GET['action'] == 'menumanage'){
	$menuid=$_GET['id']?$_GET['id']:0;
	if(!$menuid){
		header("Location:list.php?action=menu");
	}
	$sql="select * from menu where menuid=".$menuid;
	$datas = $database->query($sql)->fetchAll();
	$menudata=$datas[0];
?>
<div id="main">
	<div class="content">
		
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">新建菜单</div>
				</td>
			</tr>
		</table>
		<form action="api.php" method="post" >
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" valign="top" width="15%" align="center">菜单标题</td>
					<td class="tit2" valign="top" width="15%" align="center">上级菜单</td>
					<td class="tit2" valign="top" width="15%" align="center">菜单链接</td>
					<td class="tit2" valign="top" width="15%" align="center">发送图文</td>
					<td class="tit2" valign="top" width="15%" align="center">排序</td>
				</tr>
				<tr>
					<td valign="top" align="center">
						<input type="text" value="<?php echo $menudata['menutitle']?>" name="menutitle" />
					</td>
					<td valign="top" align="center">
						<select name="parentmenu" style="max-width: 200px;">
							<option selected="selected" value="<?php echo $menudata['parentid']?>">
								<?php 
									if($menudata['parentid']){
										$parent='select * from menu where menuid='.$menudata['parentid'];
										$parent_menu=$database->query($parent)->fetchAll();
										echo $parent_menu[0]['menutitle'];
									}else{
										echo '一级菜单';
									}
									
								?>
							</option>
							<option value="0">一级菜单</option>
							<?php 
								$parent='select * from menu where parentid=0';
								$parent_menu=$database->query($parent)->fetchAll();
								foreach ($parent_menu as $key => $value) {
							?>
								<option value="<?php echo $value['menuid']?>"><?php echo $value['menutitle']?></option>
							<?php
								}
							 ?>
						</select>
						
					</td>
					<td valign="top" align="center">
						<input type="text" value="<?php echo $menudata['linkurl']?>" name="linkurl" />
					</td>
					<td valign="top" align="center">
						<select name="newsid" style="max-width:200px;">
							<?php 
								$news_sql="select * from wechat_news where news_id=".$menudata['news_id']." order by update_time desc";
								$newsid=$database->query($news_sql)->fetchAll();
								$news_all="select * from wechat_news where news_id!= ".$menudata['news_id']." order by update_time desc";
								$allnews=$database->query($news_all)->fetchAll();
							?>
							<?php if($menudata['news_id']!= 0){ ?>
								<option value="<?php echo $menudata['news_id']?>"><?php $echo=$newsid[0]['news_title']; echo $echo;?></option>	
							<?php } ?>
							<option value="0">链接网页不用填写</option>		
							<!-- <option value="999">链接网页不用填写</option> -->
							<?php foreach ($allnews as $key => $value) {?>
								<option value="<?php echo $value['news_id']?>"><?php echo $value['news_title']?></option>
							<?php }?>
						</select>
					</td>
					<td valign="top" align="center">
						<input type="text" value="<?php echo $menudata['sort']?>" name="menusort" />
					</td>
					
				</tr>
				<tr>
					<td colspan="5" style="text-align:center;">
						<input type="submit" value="保存" />
						<input type="hidden" value="<?php echo $menuid;?>" name="menuid"/>
						<input type="hidden" value="editmenu" name="action"/>
					</td>
				</tr>
			</table>
		</form>
		
	</div>
</div>
		<!-- 编辑smg -->
<?php }elseif($_GET['action'] == 'smgmanage'){
	$sql="select * from send_message where id=".$_GET['id'];
	$datas = $database->query($sql)->fetchAll();
	$list_item=$datas[0];
?>
<div id="main">
	<div class="content">
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">图文推管理</div>
				</td>
				<td class="tit2" valign="top" align="center">
					<a href="manage.php?action=smg">添加图文推</a>
				</td>
			</tr>
		</table>
		<form action="api.php" method="post">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" valign="top" width="15%" align="center">回复名称</td>
					<td class="tit2" valign="top" width="15%" align="center">微信图文标题</td>
				</tr>

				<tr>
					<td valign="top" align="center">
						<input type="text" name="title" value="<?php print $list_item["title"]; ?>" />
					</td>
					<td valign="top" align="center">
						<input type="text" name="sort" value="<?php print $list_item['sort']; ?>" />
					</td>
					<td valign="top" align="center">
						<?php 
							$wechat_sql_single='select * from wechat_news where news_id='.$list_item['news_id'];
							$wechat_single=$database->query($wechat_sql_single)->fetch();
							$wechat_sql_all='select * from wechat_news order by update_time desc';
							$wechat=$database->query($wechat_sql_all)->fetchAll();
						?>
						<select name="wechat" style="max-width:200px;">
							<option value="<?php print $wechat_single['news_id']; ?>"><?php print $wechat_single['news_title'] ;?></option>
							<option value="0">链接网页不用填写</option>
							<?php foreach ($wechat as $key => $value) { ?>
							<option value="<?php echo $value['news_id']; ?>"><?php echo $value['news_title']; ?></option>

							<?php } ?>

						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top" align="center">
						<input type="submit" value="保存"/>
						<input type="hidden" name="action" value="editsmg"/>
						<input type="hidden" name="smgid" value="<?php print $_GET['id']?>"/>
					</td>
					
				</tr>
			</table>
		</form>
		
	</div>
</div>
<?php
}elseif($_GET['action'] == 'smg'){
?>
<div id="main">
	<div class="content">
		<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td class="tit2" valign="top" align="left">
					<div class="title">图文推管理</div>
				</td>
				<td class="tit2" valign="top" align="center">
					<a href="manage.php?action=smg">添加图文推</a>
				</td>
			</tr>
		</table>
		<form action="api.php" method="post">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" valign="top" width="15%" align="center">回复名称</td>
					<td class="tit2" valign="top" width="15%" align="center">排序</td>
					<td class="tit2" valign="top" width="15%" align="center">微信图文标题</td>
				</tr>

				<tr>
					<td valign="top" align="center">
						<input type="text" name="title" value="" />
					</td>
					<td valign="top" align="center">
						<input type="text" name="sort" value="" />
					</td>
					<td valign="top" align="center">
						<?php 
							$wechat_sql_all='select * from wechat_news order by update_time desc';
							$wechat=$database->query($wechat_sql_all)->fetchAll();
							// print_r($wechat);
						?>
						<select name="wechat" style="max-width:200px;">
							<?php foreach ($wechat as $key => $value) {?>
								<option value="<?php print $value['news_id']; ?>"><?php print $value['news_title']; ?></option>
							<?php }?>
							

						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top" align="center">
						<input type="submit" value="保存"/>
						<input type="hidden" name="action" value="addsmg"/>
					</td>
					
				</tr>
			</table>
		</form>
		
	</div>
</div>
<?php
}elseif($_GET['action'] == 'qrcode'){
?>
<div id="main">
	<div class="content">
		<form action="api.php" method="post">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" valign="top" width="15%" align="center">场景区分</td>
				</tr>

				<tr>
					<td valign="top" align="center">
						<input type="text" name="sence_name" value="" />
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top" align="center">
						<input type="submit" value="保存"/>
						<input type="hidden" name="action" value="addqrcode"/>
					</td>
					
				</tr>
			</table>
		</form>
		
	</div>
</div>
<?php
}elseif($_GET['action'] == 'keywords'){
?>
<div id="main">
	<div class="content">
		<form action="api.php" method="post">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" colspan='2' valign="top" width="15%" align="center">新建关键字</td>
				</tr>

				<tr>
					<td valign="top" align="center">
						关键字
					</td>
					<td valign="top" align="center">
						<input type="text" name="keyword" value="" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="center">
						回复类型
					</td>
					<td valign="top" align="center">
						<select id="select_type" name='type'>
							<option value='1'>文本类型</option>
							<option value='2'>微信图文推</option>
							<option value='3'>链接</option>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top" align="center">
						回复内容
					</td>
					<td valign="top" align="center">
						<textarea id='content' type="text" name="content"></textarea>
						<input style='display: none;' id='linkurl' type='text' name='linkurl'/>
						<select style='display: none;' id='related_article' name='related_article'>
							<?php 
								$news_all="select * from wechat_news order by update_time desc";
								$allnews=$database->query($news_all)->fetchAll();
							?>
							<?php foreach ($allnews as $key => $value) {?>
								<option value="<?php echo $value['media_id']?>"><?php echo $value['news_title']?></option>
							<?php }?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top" align="center">
						<input type="submit" value="保存"/>
						<input type="hidden" name="action" value="addkeywords"/>
					</td>
					
				</tr>
			</table>
		</form>
		
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#select_type').change(function(){
			var type=$(this).val();
			if(type==1){
				$('#content').css('display','block');
				$('#linkurl').css('display','none');
				$('#related_article').css('display','none');
			}else if(type==2){
				$('#related_article').css('display','block');
				$('#linkurl').css('display','none');
				$('#content').css('display','none');
			}else if(type==3){
				$('#linkurl').css('display','block');
				$('#related_article').css('display','none');
				$('#content').css('display','none');
			}
		});
	})
</script>
<?php
}elseif($_GET['action'] == 'editkeywords'){
$key_id=$_GET['key_id'];
if(empty($key_id)){
	header("Location:list.php?action=keywords");
}
$sql="select * from keywords where id=".$key_id;
$data = $database->query($sql)->fetch();
?>
<div id="main">
	<div class="content">
		<form action="api.php" method="post">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" colspan='2' valign="top" width="15%" align="center">编辑关键字</td>
				</tr>

				<tr>
					<td valign="top" align="center">
						关键字
					</td>
					<td valign="top" align="center">
						<input type="text" name="keyword" value="<?php echo $data['keyword'];?>" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="center">
						回复类型
					</td>
					<td valign="top" align="center">
						<select id="select_type" name='type'>
							<option value='<?php echo $data['type'];?>'>
								<?php 
									switch ($data['type']) {
							            case '1':
							                $type='文本类型';
							                break;
							            case '2':
							                $type='图文推';
							                break;
							            case '3':
							                $type='链接';
							                break;
									}
									echo $type;
								?>
							</option>
							<option value='1'>文本类型</option>
							<option value='2'>微信图文推</option>
							<option value='3'>链接</option>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="top" align="center">
						回复内容
					</td>
					<td valign="top" align="center">
						<textarea id='content' <?php if($data['type']!='1'):?> style='display: none;'<?php endif;?>  type="text" name="content" ><?php echo $data['content'];?></textarea>
						<input <?php if($data['type']!='3'):?> style='display: none;'<?php endif;?> id='linkurl' type='text' name='linkurl' value='<?php echo $data['linkurl'];?>'/>
						<select <?php if($data['type']!='2'):?> style='display: none;'<?php endif;?> id='related_article' name='related_article'>
							<?php 
								$news_all="select * from wechat_news order by update_time desc";
								$allnews=$database->query($news_all)->fetchAll();
								$new_sql="select * from wechat_news where media_id='".$data['related_article']."'";
								$new=$database->query($new_sql)->fetch();
							?>
							<?php if($data['type']=='2'):?>
							<option value='<?php echo $data['related_article'];?>'><?php echo $new['news_title']?></option>
							<?php endif;?>
							<?php foreach ($allnews as $key => $value) {?>
								<option value="<?php echo $value['media_id']?>"><?php echo $value['news_title']?></option>
							<?php }?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top" align="center">
						<input type="submit" value="保存"/>
						<input type="hidden" name="keyword_id" value="<?php echo $key_id;?>"/>
						<input type="hidden" name="action" value="editkeywords"/>
					</td>
					
				</tr>
			</table>
		</form>
		
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#select_type').change(function(){
			var type=$(this).val();
			if(type==1){
				$('#content').css('display','block');
				$('#linkurl').css('display','none');
				$('#related_article').css('display','none');
			}else if(type==2){
				$('#related_article').css('display','block');
				$('#linkurl').css('display','none');
				$('#content').css('display','none');
			}else if(type==3){
				$('#linkurl').css('display','block');
				$('#related_article').css('display','none');
				$('#content').css('display','none');
			}
		});
	})
</script>
<?php }?>
</body>
</html>