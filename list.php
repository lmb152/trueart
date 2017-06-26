<?php
session_start();
if(!$_SESSION['uid']){
	exit('请先登录');
}
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
	$sql="select * from menu where uid='".$_SESSION['uid']."' order by parentid asc, sort asc";
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
						if($list_item['parentid']){
							$parent='select menutitle from menu where menuid='.$list_item['parentid'];
							$parent_title=$database->query($parent)->fetch();
							echo $parent_title['menutitle'];
						}else{
							echo '一级菜单';
						}
					 ?>
				</td>
				<td style="vertical-align: middle;text-align: center;max-width: 350px;overflow: scroll;">
					<?php 
						if($list_item['linkurl']){
							echo $list_item["linkurl"];
						}else{
							echo '图文推无需填写';
						}
					?>
				</td>
				<td>
					<?php
						$news_sql="select * from wechat_news where media_id ='".$list_item['msg_id']."'";
						$news=$database->query($news_sql)->fetch();
						if($news){
							echo $news['news_title'];
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
<?php }elseif($_GET['action'] == 'qrcode'){
$sql="select * from sence_qrcode where uid='".$_SESSION['uid']."'";
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
$sql="select * from qrcode_content where user_id='".$_SESSION['uid']."' and qrcode_id=".$_GET['qrcode_id']." and qrcode_type=0 order by id desc";
$datas = $database->query($sql)->fetch();
?>
<div id="main">
	<div class="content">
		<form action="api.php" method="post" enctype="multipart/form-data">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" colspan='4' valign="top" align="center">自动回复图文内容</td>
				</tr>
				<tr>
					<td>外链地址<span style='color:red;'>*</span></td>
					<td class="tit2" colspan='3' valign="top" align="center">
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" name='linkurl' value="<?php print_r($datas['linkurl']);?>" />
						</div>
					</td>
				</tr>
				<tr>
					<td>标题<span style='color:red;'>*</span></td>
					<td class="tit2" colspan='3'  valign="top" align="center">
						<div class="form-group col-sm-9">
							<input type='text' class="form-control" name='title' value="<?php print_r($datas['title']);?>"/>
						</div>
					</td>
				</tr>
				<tr>
					<td>描述</td>
					<td class="tit2" colspan='3'  valign="top" align="center">
						<div class="form-group col-sm-9">
							<textarea type='text' class="form-control" name='description'><?php print_r($datas['description']);?></textarea>
						</div>
					</td>
				</tr>
				<tr>
					<td>图文中间的图片<span style='color:red;'>*</span></td>
					<td valign="top" align="center">
						<div class="form-group col-sm-9">
							<input type='file' class="form-control" name='content' />
						</div>
					</td>

					<td colspan='2'>
						<?php if(!empty($datas['content']) && $datas['content']){
							$imgsrc=unserialize($datas['content']);
						?>
							<img width='200px;' src="<?php echo $imgsrc[0];?>">
						<?php }?>
					</td>
				</tr>
				<tr>
					<td colspan="4" valign="top" align="center">
						<input type="submit" class="btn btn-success" value="保存"/>
						<input type="hidden" name="qrcode_id" value="<?php echo $_GET['qrcode_id']?>"/>
						<input type="hidden" name="action" value="editqrcode"/>
					</td>
					
				</tr>
			</table>
		</form>
		
	</div>
</div>
<?php }elseif($_GET['action'] == 'smg'){
	$sql="select * from send_message where uid='".$_SESSION['uid']."'";
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
	$sql="select * from keywords where uid='".$_SESSION['uid']."'";
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
				                $output='微信图文推';
				                break;
				            case '3':
				                $output='链接';
				                break;
				            case '4':
				                $output='自定义图文推';
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
<?php }elseif($_GET['action'] == 'subscribe'){
$sql="select * from subscribe where user_id='".$_SESSION['uid']."' order by id desc";
$data = $database->query($sql)->fetch();
?>
<div id="main">
	<div class="content">
		<form action="api.php" method="post" enctype="multipart/form-data">
			<table class="table table-striped" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="tit2" colspan='4' valign="top" align="center">自动回复图文内容</td>
				</tr>
				<tr>
					<td valign="top" align="center">
						回复类型
					</td>
					<td valign="top" align="center">
						<div class="form-group col-sm-5">
							<select id="select_type" class="form-control" name='type'>
								<option value='<?php echo $data['type'];?>'>
									<?php 
										switch ($data['type']) {
								            case '1':
								                $type='文本类型';
								                break;
								            case '2':
								                $type='微信图文推';
								                break;
								            case '3':
								                $type='链接';
								                break;
								            case '4':
								                $type='自定义图文推';
								                break;
										}
										echo $type;
									?>
								</option>
								<option value='1'>文本类型</option>
								<option value='2'>微信图文推</option>
								<option value='3'>链接</option>
								<option value='4'>自定义图文推</option>
							</select>
						</div>
						
					</td>
				</tr>
				<tr>
					<td valign="top" align="center">
						回复内容
					</td>
					<td valign="top" align="center">
						<div class="form-group col-sm-6">
							<textarea class="form-control" id='content' <?php if($data['type']!='1'):?> style='display: none;'<?php endif;?>  type="text" name="content_des" ><?php echo $data['content_des'];?></textarea>
							<input class="form-control" <?php if($data['type']!='3'):?> style='display: none;'<?php endif;?> id='linkurl' type='text' name='linkurl1' value='<?php echo $data['linkurl'];?>'/>
							<select class="form-control" <?php if($data['type']!='2'):?> style='display: none;'<?php endif;?> id='related_article' name='related_article'>
								<?php 
									$news_all="select * from wechat_news where uid='".$_SESSION['uid']."' order by update_time desc";
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

							<div <?php if ($data['type']!='4'):?>style='display: none;'<?php endif;?> id="releated_content">
								<div class="form-group col-sm-12">
									<div class="form-group col-sm-3">外链地址:</div>
									<div class="form-group col-sm-9">
										<input type="text" class="form-control" name="linkurl" value="<?php echo $data['linkurl'];?>">
									</div>
								</div>
								<div class="form-group col-sm-12">
									<div class="form-group col-sm-3">标题:</div>
									<div class="form-group col-sm-9">
										<input type="text" class="form-control" name="title" value="<?php echo $data['title'];?>">
									</div>
								</div>
								<div class="form-group col-sm-12">
									<div class="form-group col-sm-3">描述:</div>
									<div class="form-group col-sm-9">
										<textarea type="text" class="form-control" name="description"><?php echo $data['description'];?></textarea>
									</div>
								</div>
								<div class="form-group col-sm-12">
									<div class="form-group col-sm-3">图文中间的图片:</div>
									<div class="form-group col-sm-9">
										<input type="file" class="form-control" name="content">
									</div>
								</div>
							</div>
						</div>
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
<script type="text/javascript">
	$(document).ready(function(){
		$('#select_type').change(function(){
			var type=$(this).val();
			if(type==1){
				$('#content').css('display','block');
				$('#linkurl').css('display','none');
				$('#related_article').css('display','none');
				$('#releated_content').css('display','none');
			}else if(type==2){
				$('#related_article').css('display','block');
				$('#linkurl').css('display','none');
				$('#content').css('display','none');
				$('#releated_content').css('display','none');
			}else if(type==3){
				$('#linkurl').css('display','block');
				$('#related_article').css('display','none');
				$('#content').css('display','none');
				$('#releated_content').css('display','none');
			}else if(type==4){
				$('#linkurl').css('display','none');
				$('#related_article').css('display','none');
				$('#content').css('display','none');
				$('#releated_content').css('display','block');
			}
		});
	})
</script>
<?php
}
?>
</body>

</html>