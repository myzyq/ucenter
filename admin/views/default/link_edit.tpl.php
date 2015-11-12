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
		$class = $viewData['msg']['flag'] == true  ? 'correctmsg' : 'errormsg';
		echo "<div class='{$class}'>
				<p>{$viewData['msg']['msg']}</p>
			 </div>  <br />";
	}?>
	<h3 class="marginbot">
		管理Link
		<a class="sgbtn" href="enter.php?m=link&a=index">返回LINK列表</a>
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
	
		?>
		<form class="dataform" method="post" action="?m=link&a=save">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">LINK名:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[title]'  value='<?php echo empty($data['title']) ? '' : $data['title']; ?>' />						
						</td>
						<td>LINK名应尽量不重复</td>
					</tr>
					<tr><th colspan="2">URL:</th></tr>
					<tr>
						<td>
							 <input type='text' name='info[url]'  style='width:420px;' value='<?php echo empty($data['url']) ? '' : $data['url'];?>' />						
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">回调API地址:</th></tr>
					<tr>
						<td><input type='text' name='info[category]'  value='<?php echo empty($data['category']) ? '' : $data['category'];?>' /></td>
						<td>显示的时候分组</td>			
					</tr>					
					<tr><th colspan="2">备注:</th></tr>
					<tr>
						<td><textarea name='info[memo]'><?php echo empty($data['memo']) ? '' : $data['memo'] ;?></textarea></td>
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

<?php  view('footer');?>
