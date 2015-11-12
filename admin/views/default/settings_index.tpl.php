<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
?>
<div class="container">
	<?php if(isset($viewData['rs'])){
		//echo $viewData['rs']['flag'];
		$class = $viewData['rs']['flag']  ? 'correctmsg' : 'errormsg';
		echo "<div class='{$class}'>
				<p>{$viewData['rs']['msg']}</p>
			 </div>";
	}?>
	 <h3 class="marginbot">
        管理配置
        <a class="sgbtn" href="enter.php?m=settings&a=add">添加新配置</a>
        <a class="sgbtn" href="enter.php?m=settings&a=list">配置列表</a>
    </h3>
	<div class="mainbox nomargin">
		<?php if(isset($viewData['settings'])) {
				$settings = $viewData['settings'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
		<form action="enter.php?m=settings&a=update"  method="post">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">IP绑定状态:</th></tr>
					<tr>
						<td>
							<label ><input type="radio" name="info[is_band]" value="1" <?php echo !empty($settings['is_band']['v']) ? 'checked="checked"' : ''; ?> />开启</label>
							<label ><input type="radio" name="info[is_band]" value="0" <?php echo empty($settings['is_band']['v']) ? 'checked="checked"' : ''; ?> />关闭</label>
						</td>
						<td>是否开启IP绑定</td>
					</tr>				
					<tr><th colspan="2">IP绑定:</th></tr>
					<tr>
						<td><textarea rows="4" cols="16" name="info[ip_band]"><?php echo isset($settings['ip_band']) ? $settings['ip_band']['v'] : ''; ?></textarea></td>
						<td>每一行只写一个IP，开启IP绑定后，这些IP将被允许访问UCENTER</td>
					</tr>
				</tbody>
			</table>
			<div class="opt">
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>
		</form>
		<?php }?>
	</div>
</div>

<?php  view('footer');?>