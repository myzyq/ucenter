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
	<br />
	<h3 class="marginbot">
		编辑用户
		<a class="sgbtn" href="enter.php?m=user&a=index">返回用户列表</a>
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=user&a=save_user">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">用户名:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[name]'   />						
						</td>
						<td>用户名</td>
					</tr>
					<tr><th colspan="2">密码:</th></tr>
                    <tr>
                        <td>
                            <input type='text' name='info[password]'   />                       
                        </td>
                        <td>如果希望系统自动生成密码，这项就不要写任何东西</td>
                    </tr>
					<tr><th colspan="2">Email:</th></tr>
					<tr>
						<td><input type="text" name='info[email]'  /></td>
						<td>请正确填写EMAIL</td>			
					</tr>
					<tr><th colspan="2">IP绑定状态:</th></tr>
					<tr>
						<td>
							<label ><input type="radio" name="info[is_band]" value="1" />开启</label>
							<label ><input type="radio" name="info[is_band]" value="0" checked="checked" />关闭</label>
						</td>
						<td>是否开启IP绑定</td>
					</tr>				
					<tr><th colspan="2">IP绑定:</th></tr>
					<tr>
						<td><textarea rows="4" cols="16" name="info[ip_band]"></textarea></td>
						<td>IP与IP间用";"分隔，开启IP绑定后，这些IP将被允许该用户访问</td>
					</tr>
					 <tr><th colspan="2">IP组:</th></tr>
                    <tr>
                        <td>
                           <?php $ipgroup = empty($viewData['ipgroup']) ? array() : $viewData['ipgroup'];?>
                           <select name="info[ip_group]">
                               <option value="0" <?php if(empty($ipgroup)) { echo "selected = 'selected'";  } ?>>未指定</option>
                               <?php foreach($ipgroup as $ip){                                   
                                    echo "<option value='{$ip['id']}' >{$ip['title']}</option>\n"; 
                               }?>
                           </select>
                        </td>
                        <td>IP组</td>
                    </tr>   
					<tr><th colspan="2">备注:</th></tr>
					<tr>
						<td>
						  <textarea name='info[memo]'></textarea>
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">用户组:</th></tr>
					<tr>
						<td>
							<select name='info[group_id]'>
							 <?php if(!empty($viewData['groups'])){
							         foreach($viewData['groups'] as $group){
							         	$selected = empty($group['id']) ? " selected='selected' " : "";
							         	echo "<option value='{$group['id']}' {$selected}>{$group['group_name']}</option>";
							         }
							 } else{
							   echo "<option value='0' selected='selected'>默认组</option>";
							 }?>
							</select>
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">应用:<label><input type="checkbox" id='check_all_app' />全选</label></th></tr>
					<tr>
						<td colspan="2">
							<?php 
								if(!empty($viewData['apps'])) {
									$apps = $viewData['apps'];									
									foreach($apps as $app) {										
										echo "<label style='float: left; width: 140px; padding-left: 5px;'><input type='checkbox' id='app_{$app['id']}' class='app_cbx' name='info[apps][]' value='{$app['id']}' />{$app['app_name']}</label>"; 
									}
								}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<br/>
			<div class="opt">				
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>
<div id="dialog" style='display:none' title='处理中...'>
	<div style='text-align:center'>
			<img src='<?php echo THEME_RESOURCE;?>loading.gif' style='border:0' /> 
	</div>
</div>
<script type="text/javascript" >
	$().ready(function(){
		$('#dialog').dialog({
			autoOpen : false,
			width : 300,
			height : 80,
			modal: true,
			dialogClass: "my-dialog"
		});
		
		$('#check_all_app').click(function(){
			check = $(this).attr('checked');
			$('.app_cbx').attr('checked', check);
		});
		
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
