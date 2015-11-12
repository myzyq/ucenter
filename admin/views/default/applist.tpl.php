<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
 $keys = empty($viewData['keys']) ? array() : $viewData['keys'];

?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">
	<?php if(isset($viewData['msg'])){
		//echo $viewData['rs']['flag'];
		$class = $viewData['msg']['flag'] == true  ? 'correctmsg' : 'errormsg';
		echo "<div class='{$class}'>
				<p>{$viewData['msg']['msg']}</p>
			 </div>";
	}?>

	<h3>应用列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
			<table id='app_data' class="datalist fixwidth"  >
				<tbody>	
					<tr>
						<th>应用名</th>
						<th>URL</th>						
					</tr>
					<?php foreach($data as $item) {						
						echo "<tr>\n";
						echo "<td>{$item['app_name']}</td>";
						echo "<td><a href='{$item['app_url']}' target='_blank'>{$item['app_url']}</a></td>";
						echo "</tr>\n";
		}?>
					<tr class="nobg">
						<td>
						
						</td>
						<td class="tdpage" colspan="8"></td>
					</tr>
				</tbody>
			</table>
		<?php }?>
	</div>
</div>
<?php echo script_link('common.js', JS_ROOT);?>
<script type="text/javascript" >
	$().ready(function(){
		tableOver('app_data', 'trhover');
	});
</script>
tableOver('app_data', 'trhover');
<?php  view('footer');?>