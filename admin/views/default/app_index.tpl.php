<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
$keys = empty($viewData['keys']) ? array() : $viewData['keys'] ;
?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">
	<?php if(!empty($viewData['msg'])){
		//echo $viewData['rs']['flag'];
		$class = $viewData['msg']['flag'] == true  ? 'correctmsg' : 'errormsg';
		echo "<div class='{$class}'>
				<p>{$viewData['msg']['msg']}</p>
			 </div>";
	}?>
	<br />
	<h3 class="marginbot">
		应用管理
		<a class="sgbtn" href="enter.php?m=app&a=edit">添加应用</a>
	</h3>
	<div id="tabs" >
		<ul>
			<li><a href="#tabs-1">搜索应用</a></li>
		</ul>
		<div id="tabs-1">
			<form method="get" action="?">
				<input type="hidden" name="page" value="1" />
				<input type="hidden" name="m" value="app" />
				<input type="hidden" name="a" value="index" />
				<table >
					<tbody>
						<tr>
							<td>应用名:</td>
							<td>
								<input type='text' name='keys[app_name]' value='<?php echo empty($keys['app_name']) ? '' : $keys['app_name'];?>' />
							</td>
							<td>创建时间:</td>								
							<td>
								<input type='text' name='keys[create_date]' id='createdate' value='<?php echo empty($keys['create_date']) ? '' : $keys['create_date'];?>' />
							</td>							
							<td>
								<input class="btn" type="submit" value="提 交"/>
							</td>
						</tr>						
					</tbody>
				</table>
			</form>
		</div>
	</div>

	<br />
	<h3>应用列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
		<form method="post" onsubmit="return confirm('该操作不可恢复，您确认要删除这些应用吗？');" action="?m=app&a=batch_delete">
			<table id='app_data'  class="datalist fixwidth" >
				<tbody>	
					<tr>
						<th>
							<input id="chkall" class="checkbox" type="checkbox"  name="chkall"/>
							<label for="chkall">删除</label>
						</th>
						<th>应用名</th>
						<th>应用口令</th>
						<th>UC标识</th>
						<th>创建日期</th>						
						<th>IP绑定状态</th>						
						<th>编辑</th>
					</tr>
					<?php foreach($data as $item) {
						echo "<tr>\n";
						echo "<td class='option'><input type='checkbox' class='checkbox' value='{$item['id']}' name='ids[]' />{$item['id']}</td>";
						echo "<td ><a href='{$item['app_url']}' target='_blank'>{$item['app_name']}</a></td>";
						echo "<td >{$item['token_content']}</td>";
						echo "<td >{$item['uc_sign']}</td>";
						echo "<td >{$item['create_date']}</td>";
						$isband = empty($item['is_band']) ? '关闭' : '开启' ;
						echo "<td >{$isband}</td>"; 
						$ips = str_replace("\n", ";", $item['ip_band']);
						echo "<td ><a href='?m=app&a=band_users&id={$item['id']}'>绑定用户</a> | <a href='?m=app&a=appuser&keys[app_id]={$item['id']}'>查看绑定</a> | <a href='?m=app&a=edit&id={$item['id']}'>修改</a> | <a href='enter.php?m=app&a=sync_users_view&id={$item['id']}'>推用户</a></td>"; 
						echo "</tr>\n";
		}?>
					<tr class="nobg">
						<td>
							<input class="btn" type="submit" value="提 交"/>
						</td>
						<td class="tdpage" colspan="8"> <?php echo empty($viewData['pager']) ? '' : $viewData['pager']?></td>
					</tr>
				</tbody>
			</table>
			
		</form>
		<?php }?>
	</div>
</div>
<div id='dialog'  style='display:none;' ></div>
<?php echo script_link('ui.datepicker-zh-CN.js', JS_ROOT . 'jquery/ui/i18n/');?>
<?php echo script_link('common.js', JS_ROOT);?>
<script type='text/javascript' >
	$().ready(function(){
		// Datepicker
		$('#tabs').tabs();
		$('#createdate').datepicker({
			inline: true,
			location : 'zh-CN',
			changeMonth: true,
			changeYear: true
		});
		$('#chkall').click(function(){
			var table = $(this).parents('.datalist').eq(0);
			var check = $(this).attr('checked');
			table.find(':checkbox').attr('checked',check);
		});
		tableOver('app_data', 'trhover');
	});
</script>
<?php  view('footer');?>
