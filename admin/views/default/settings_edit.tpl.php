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
			 </div>  <br />";
	}?>
	<br />
	<h3 class="marginbot">
		管理配置
        <a class="sgbtn" href="enter.php?m=settings&a=add">添加新配置</a>
        <a class="sgbtn" href="enter.php?m=settings&a=list">配置列表</a>
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['setting']) ? array() : $viewData['setting'];				
		?>
		<form class="dataform" method="post" action="?m=settings&a=save">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">配置名（英文）:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[k]'  value='<?php echo empty($data['k']) ? '' : $data['k']; ?>' />						
						</td>
						<td>配置名不能重复</td>
					</tr>
					<tr><th colspan="2">配置说明:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[memo]'  value='<?php echo empty($data['memo']) ? '' : $data['memo']; ?>' />						
						</td>
						<td>中文说明</td>
					</tr>					
					<tr><th colspan="2">值:</th></tr>
					<tr>
						<td><textarea rows="5" cols="20" name="info[v]"><?php echo isset($data['v']) ? $data['v'] : ''; ?></textarea></td>
						<td>值</td>
					</tr>					
				</tbody>
			</table>
			<br />
			<div class="opt">
				<input type='hidden' name='info[id]' value='<?php echo empty($data['k']) ? '' : $data['k'];?>' />
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>

<?php  view('footer');?>
