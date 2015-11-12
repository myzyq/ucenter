<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
$keys = empty($viewData['keys']) ? array() : $viewData['keys'];
?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">
	<?php if(isset($viewData['msg'])){
		//echo $viewData['rs']['flag'];
		//print_r($viewData['msg']);
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
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=user&a=batch_saveapp">
				<table class="opt">
				<tbody>	
					<tr><th colspan="2">用户名:</th></tr>
					<tr>
						<td>
							<?php 
							if(!empty($viewData['users'])) {
								foreach($viewData['users'] as $u) {
									echo "{$u['name']};<input type='hidden' name='uid[]' value='{$u['id']}' />";
								}	
							}
							?>						
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">用户组:<label><input type="checkbox" id='check_all' />全选</label></th></tr>
					<tr>
						<td>
							<?php 
								$group = empty($viewData['group']) ? array() : $viewData['group'];
								
								foreach($group as $g ) {									
									echo "<label><input type='checkbox' class='group_cbx' name='info[group][]'  value='{$g['id']}' />{$g['group_name']}</label>";
								}
							?>
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">应用:<label><input type="checkbox" id='check_all_app' />全选</label></th></tr>
					<tr>
						<td>
							<?php 
								if(!empty($viewData['apps'])) {
									$apps = $viewData['apps'];								
									
									foreach($apps as $app) {
										
										echo "<label><input type='checkbox' id='app_{$app['id']}' class='app_cbx' name='info[apps][]'  value='{$app['id']}' />{$app['app_name']}</label>"; 
									}
								}
							?>
						</td>
					</tr>
				</tbody>
			</table>		
			<br />
			<div class="opt">				
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>
<div id='dialog'  style='display:none;' ></div>
<script type="text/javascript" >
	$().ready(function(){
		$('#dialog').dialog({
			autoOpen : false,
			width : 300,
			height : 80,
			modal: true,
			dialogClass: "my-dialog"
		});
		$('#check_all').click(function(){
			check = $(this).attr('checked');
			$('.group_cbx').attr('checked', check);
			getAppsOfGroups();
		});
		$('#check_all_app').click(function(){
			check = $(this).attr('checked');
			$('.app_cbx').attr('checked', check);
		});
		$('.group_cbx').click(getAppsOfGroups);
	});

	function getAppsOfGroups(){
		$('#dialog').dialog('open');
		var url = 'enter.php?m=ajax&a=apps_of_groups';
		$('.group_cbx').each(function(){
			t = $(this);
			if(t.attr('checked')) {
				url += '&gids[]=' + t.val();
			}	
		});
		url += '&callback=freshapps';
		$.getScript(url);
	}

	function freshapps(data){
		if(data.flag == 'ok'){
			$('.app_cbx').attr('checked', false);
			var app = data.data;
			for(i = 0 ; i < app.length; i++) {
				id = app[i].id;
			//	alert(id);
				$('#app_' + id).attr('checked', true);
			}
		}else{
			alert(data.msg);
		}
		$('#dialog').dialog('close');
	}
</script>
<?php  view('footer');?>
