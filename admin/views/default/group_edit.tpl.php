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
		编辑用户组
		<a class="sgbtn" href="enter.php?m=group&a=index">返回用户组列表</a>
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=group&a=update">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">用户组名:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[group_name]' disabled='disabled' value='<?php echo empty($data['group_name']) ? '' : $data['group_name']; ?>' />						
						</td>
						<td>用户组名不能修改</td>
					</tr>					
					<tr><th colspan="2">备注:</th></tr>
					<tr>
						<td><textarea name='info[memo]'><?php echo empty($data['memo']) ? '' : $data['memo'] ;?></textarea></td>
						<td></td>
					</tr>
					
				</tbody>
			</table>
			<br/>
			<div class="opt">
				<input type='hidden' name='info[id]' value='<?php echo empty($data['id']) ? '' : $data['id'];?>' />
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>
<script type="text/javascript" >
	$().ready(function(){
		$('#check_all').click(function(){
			check = $(this).attr('checked');
			$('.apps_cbx').attr('checked', check);
		});
	});
</script>
<?php  view('footer');?>