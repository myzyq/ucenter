<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
$keys = empty($viewData['keys']) ? array() : $viewData['keys'] ;
$uid = $viewData['uid'];
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
	<br/>
	<h3 class="marginbot">
		绑定应用
		<a class="sgbtn" href="enter.php?m=user&a=index">返回用户列表</a>
	</h3>
	<h3>应用列表</h3>	
	<div class="mainbox" style="height:200px;overflow:scroll;">
		<table id='app_data' class="datalist fixwidth" >
			<tbody>
				<tr>
					<th>ID</th>
					<th>应用名</th>
					<th>应用URL</th>
					<th>操作</th>
				</tr>
				<?php if(isset($viewData['apps'])) {
					$apps = $viewData['apps'];
					//print_r($settings);
					//$arr = explode("\n", $settings['ip_band']['v']);
					//print_r($arr);
					foreach($apps as $app) {
						echo "<tr>\n" ;
						echo "<td>{$app['id']}</td>\n" ;
						echo "<td>{$app['app_name']}</td>\n" ;
						echo "<td>{$app['app_url']}</td>\n" ;
						echo "<td><a href='?m=user&a=edit_userapp&uid={$uid}&appid={$app['id']}'>添加/编辑</a></td>\n" ;
						echo "</tr>\n";
					}
				}
				?>
			</tbody>
		</table>	
	</div>
	<br />
	<h3>可访问的应用列表</h3>
	<div class="mainbox">
		<table id='myapp_data' class="datalist fixwidth" >
			<tbody>
				<tr>
					<th>用户</th>
					<th>应用</th>
					<th>可访问性</th>
					<th>IP绑定状态</th>
					<th>IP绑定</th>
					<th>操作</th>
				</tr>
				<?php if(!empty($viewData['myapps'])) {
						foreach($viewData['myapps'] as $mapp) {
							echo "<tr>\n";
							echo "<td>{$mapp['user_name']}</td>\n";
							echo "<td>{$mapp['app_name']}</td>\n";
							$stat = empty($mapp['flag']) ? '允许' : '禁止';
							echo "<td>{$stat}</td>\n";
							$ipstat = empty($mapp['is_band']) ? '关闭' : '开启';
							echo "<td>{$ipstat}</td>\n";
							echo "<td style='width:150px;' >{$mapp['ip_band']}</td>\n";
							$opt = empty($mapp['flag']) ? "<a href='?m=user&a=disable_uapp&uid={$mapp['user_id']}&appid={$mapp['app_id']}'>禁止访问</a>" 
														: "<a href='?m=user&a=enable_uapp&uid={$mapp['user_id']}&appid={$mapp['app_id']}'>允许访问</a>" ;
							$ipopt = empty($mapp['is_band']) ? "<a href='?m=user&a=enable_ipband&uid={$mapp['user_id']}&appid={$mapp['app_id']}'>开启IP绑定</a>"  
									: "<a href='?m=user&a=disable_ipband&uid={$mapp['user_id']}&appid={$mapp['app_id']}'>关闭IP绑定</a>";
							echo "<td>{$opt} | {$ipopt} | <a href='?m=user&a=edit_userapp&uid={$mapp['user_id']}&appid={$mapp['app_id']}'>编辑</a></td>\n";
							echo "</tr>\n";
						}					
				}?>
			</tbody>
		</table>		
	</div>
	
</div>
<?php echo script_link('common.js', JS_ROOT);?>
<script type="text/javascript">
	$().ready(function(){
		tableOver('app_data', 'trhover');
		tableOver('myapp_data', 'trhover');
	});
</script>	
<?php  view('footer');?>