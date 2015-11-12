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
			 </div><br />";
	}?>
	
	<h3 class="marginbot">
		修改密码
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=user&a=savepwd">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">旧密码:</th></tr>
					<tr>
						<td>
							<input type='password' name='oldpwd'   />						
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">新密码:</th></tr>
					<tr>
						<td>
							<input type='password' name='pwd'  />						
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">确认密码:</th></tr>
					<tr>
						<td><input type="password" name='rpwd' /></td>
						<td></td>			
					</tr>
					
				</tbody>
			</table>
			<br />
			<div class="opt">
				<input type='hidden' name='info[id]' value='<?php echo empty($data['id']) ? '' : $data['id'];?>' />
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>
<div id='dialog'  style='display:none;' ></div>
<script type='text/javascript' >
	$().ready(function(){
		$('#check_all').click(function(){
			check = $(this).attr('checked');
			$('.groups_cbx').attr('checked', check);
		});
	});	
</script>
<?php  view('footer');?>
