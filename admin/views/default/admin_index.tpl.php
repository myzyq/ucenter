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
	<div id="tabs" >
		<ul>
			<li><a href="#tabs-1">搜索管理员</a></li>
			<li><a href="#tabs-2">添加管理员</a></li>
		</ul>
		<div id="tabs-1">
			<form method="get" action="?">
				<input type="hidden" name="page" value="1" />
				<input type="hidden" name="m" value="admin" />
				<input type="hidden" name="a" value="index" />				
				<table >
					<tbody>
						<tr>
							<td>用户名:</td>
							<td>
								<input type='text' name='keys[admin_name]' value='<?php echo empty($keys['admin_name']) ? '' : $keys['admin_name'];?>' />
							</td>
							<td>UID:</td>								
							<td>
								<input type='text' name='keys[id]' value='<?php echo empty($keys['id']) ? '' : $keys['id'];?>' />
							</td>							
							<td>Email:</td>
							<td>
								<input type='text' name='keys[email]' value='<?php echo empty($keys['email']) ? '' : $keys['email'];?>' />
							</td>
							<td>
								<input class="btn" type="submit" value="提 交"/>
							</td>
						</tr>						
					</tbody>
				</table>
			</form>
		</div>

		<div id="tabs-2">
			<form method="post" action="?m=admin&a=save">
				<table >
					<tbody>
						<tr>
							<td>用户名:</td>
							<td>
								<input type='text' name='admin_name' />
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
	<h3>管理员列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
		<form method="post" onsubmit="return confirm('该操作不可恢复，您确认要删除这些管理员吗？');" action="?m=admin&a=batch_delete">
			<table id='admin_data' class="datalist fixwidth" >
				<tbody>	
					<tr>
						<th>
							<input id="chkall" class="checkbox" type="checkbox"  name="chkall"/>
							<label for="chkall">删除</label>
						</th>
						<th>用户名</th>
						<th>Email</th>
						<th>上次登录时间</th>
						<th>上次登录IP</th>
						<th>登录次数</th>
						<th>IP绑定状态</th>
						<th>编辑</th>
					</tr>
					<?php foreach($data as $item) {
						echo "<tr>\n";
						echo "<td class='option'><input type='checkbox' class='checkbox' value='{$item['id']}' name='ids[]' /></td>";
						echo "<td >{$item['name']}</td>";
						echo "<td >{$item['email']}</td>";
						echo "<td >{$item['last_login_time']}</td>";
						echo "<td >{$item['last_login_ip']}</td>";
						echo "<td >{$item['login_times']}</td>";
						$isband = empty($item['is_band']) ? '关闭' : '开启' ;
						echo "<td >{$isband}</td>"; 
						$ips = str_replace("\n", ";", $item['ip_band']);
						echo "<td ><a href='?m=user&a=edit&id={$item['id']}'>修改</a> | <a href='?m=user&a=reset_pwd&id={$item['id']}' onclick='return confirm(\"要重新生成密码吗？\");'>改密码</a></td>"; 
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
<?php echo script_link('common.js', JS_ROOT);?>
<script type='text/javascript' >
	$().ready(function(){
		$('#tabs').tabs();
		
		$('#chkall').click(function(){
			var table = $(this).parents('.datalist').eq(0);
			var check = $(this).attr('checked');
			table.find(':checkbox').attr('checked',check);
		});
		tableOver('admin_data', 'trhover');
	});

</script>
<?php  view('footer');?>