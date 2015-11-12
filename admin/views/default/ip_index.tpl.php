<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
$keys = empty($viewData['keys']) ? array() : $viewData['keys'] ;
?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">
	<?php if(!empty($viewData['msg'])){
		//echo $viewData['rs']['flag'];
		$class = $viewData['msg']['flag'] == true  ? 'correctmsg' : 'errormsg';
		echo "<div class='{$class}'>
				<p>{$viewData['msg']['msg']}</p>
			 </div>";
	}?>
	<h3 class="marginbot">
        IP组管理
        <a class="sgbtn" href="enter.php?m=ip&a=add">添加IP组</a>
    </h3>
	<h3>IP组列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
			<table id='ip_data' class="datalist fixwidth" >
				<tbody>	
					<tr>						
						<th>别名</th>
						<th>IP</th>
						<th>备注</th>
						<th>编辑</th>
					</tr>
					<?php foreach($data as $item) {
						echo "<tr>\n";						
						echo "<td >{$item['title']}</td>";
						echo "<td >{$item['ip']}</td>";
						echo "<td >{$item['memo']}</td>";
						echo "<td ><a href='?m=ip&a=edit&id={$item['id']}'>修改</a> | <a href='?m=ip&a=delete&id={$item['id']}' onclick='return confirm(\"要删除吗？\");'>删除</a></td>"; 
						echo "</tr>\n";
		}?>
					
				</tbody>
			</table>

		<?php }?>
	</div>
</div>
<?php echo script_link('common.js', JS_ROOT);?>
<script type='text/javascript' >
	$().ready(function(){	
		tableOver('ip_data', 'trhover');
	});

</script>
<?php  view('footer');?>