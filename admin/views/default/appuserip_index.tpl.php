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
		应用绑定用户IP管理
		<a class="sgbtn" href="enter.php?m=app&a=index">应用列表</a>
		<?php if(!empty($keys['app_id'])){?>
		<a class="sgbtn" href="enter.php?m=app&a=edit_appuserip&app_id=<?php echo  $keys['app_id'];?>">添加绑定</a>
		<?php }?>
	</h3>
	<div id="tabs" >
		<ul>
			<li><a href="#tabs-1">搜索</a></li>
		</ul>
		<div id="tabs-1">
			<form method="get" action="?">
				<input type="hidden" name="page" value="1" />
				<input type="hidden" name="m" value="app" />
				<input type="hidden" name="a" value="appuser" />				
				<table >
					<tbody>
						<tr>
							<td>用户名:</td>
							<td>
								<input type='text' name='keys[user_name]' value='<?php echo empty($keys['user_name']) ? '' : $keys['user_name'];?>' />
							</td>							
							<td>用户组:</td>								
							<td>
								<input type='text' name='keys[group]' value='<?php echo empty($keys['group']) ? '' : $keys['group'];?>' />
							</td>							
							<td>
								<input type='hidden' name='keys[app_id]' value='<?php echo empty($keys['app_id']) ? '' : $keys['app_id'];?>' />
								<input class="btn" type="submit" value="提 交"/>
							</td>
						</tr>						
					</tbody>
				</table>
			</form>
		</div>
	</div>

	<br />
	<h3>用户IP绑定列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($data);
		?>		
			<table id='ip_data' class="datalist fixwidth" >
				<tbody>	
					<tr>
						<th>应用</th>						
						<th>用户名</th>						
						<th>IP绑定状态</th>
						<th>IP</th>
						<th>是否启用</th>
						<th>编辑</th>
					</tr>
					<?php foreach($data as $item) {
						echo "<tr>\n";			
						echo "<td >{$item['app_name']}</td>";		
						echo "<td >{$item['user_name']}</td>";									
						$isband = empty($item['is_band']) ? '关闭' : '开启' ;
						echo "<td >{$isband}</td>"; 
						$ips = str_replace("\n", ";", $item['ip_band']);
						echo "<td >{$ips}</td>"; 
						$enabled = empty($item['flag']) ? '启用' : '禁用';
						echo "<td >{$enabled}</td>"; 
						echo "<td ><a href='?m=app&a=edit_appuserip&app_id={$item['app_id']}&uer_id={$item['user_id']}'>修改</a></td>"; 
						echo "</tr>\n";
		}?>
					<tr class="nobg">
						<td>
							
						</td>
						<td class="tdpage" colspan="8"> <?php echo empty($viewData['pager']) ? '' : $viewData['pager']?></td>
					</tr>
				</tbody>
			</table>
			
		<?php }?>
	</div>
</div>
<?php echo script_link('common.js', JS_ROOT);?>
<script type='text/javascript' >
	
	$().ready(function(){
		$('#tabs').tabs();	
		tableOver('ip_data', 'trhover');	
	});
	
</script>
<?php  view('footer');?>