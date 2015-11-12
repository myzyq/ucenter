<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
$keys = empty($viewData['keys']) ? array() : $viewData['keys'];
?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">
	<?php if(!empty($viewData['msg'])){
		//echo $viewData['rs']['flag'];
		//print_r($viewData['msg']);
		$class = !empty($viewData['msg']['flag']) && $viewData['msg']['flag'] == true  ? 'correctmsg' : 'errormsg';
		echo "<div class='{$class}'>
				<p>{$viewData['msg']['msg']}</p>
			 </div>";
	}?>
	<br />
	<h3 class="marginbot">
		编辑用户密码
		<a class="sgbtn" href="enter.php?m=user&a=edit">修改用户信息</a>
		<a class="sgbtn" href="enter.php?m=user&a=index">返回用户列表</a>
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=user&a=reset_pwd">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">用户名:</th></tr>
					<tr>
						<td>
							<?php echo empty($data['name']) ? '' : $data['name']; ?>						
						</td>
						<td>用户名不能修改</td>
					</tr>
					<tr><th colspan="2">新密码:</th></tr>
					<tr>
						<td><input type="password" name='pwd' value='' /></td>
						<td>如果不填写，系统会自动生成</td>			
					</tr>
					<tr><th colspan="2">确认密码:</th></tr>
                    <tr>
                        <td><input type="password" name='rpwd' value='' /></td>
                        <td></td>           
                    </tr>
					
				</tbody>
			</table>
			<br/>
			<div class="opt">
				<input type='hidden' name='id' value='<?php echo empty($data['id']) ? '' : $data['id'];?>' />
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
