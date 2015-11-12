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
		管理IP组
		<a class="sgbtn" href="enter.php?m=ip&a=index">返回IP组列表</a>
	</h3>
	<div class="mainbox">		
		<form class="dataform" method="post" action="?m=ip&a=save">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">别名:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[title]'  value='' />						
						</td>
						<td>别名便于区分IP组</td>
					</tr>
					<tr><th colspan="2">IP列表:</th></tr>
					<tr>
						<td>
							<textarea name='info[ip]' style="width:300px;height:60px;"></textarea>						
						</td>
						<td>ip与ip之间以半角‘;’分隔，不要有回车</td>
					</tr>					
					<tr><th colspan="2">备注:</th></tr>
					<tr>
						<td><textarea name='info[memo]'></textarea></td>
						<td></td>
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
<script type='text/javascript' >
	$().ready(function(){
		$('#check_all').click(function(){
			check = $(this).attr('checked');
			$('.groups_cbx').attr('checked', check);
		});
	});	
</script>
<?php  view('footer');?>
