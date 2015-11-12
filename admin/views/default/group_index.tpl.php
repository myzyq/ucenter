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
	<div id="tabs" >
		<ul>
			<li><a href="#tabs-1">搜索用户组</a></li>
			<li><a href="#tabs-2">添加用户组</a></li>
		</ul>
		<div id="tabs-1">
			<form method="get" action="?">
				<input type="hidden" name="page" value="1" />
				<input type="hidden" name="m" value="group" />
				<input type="hidden" name="a" value="index" />
				<table >
					<tbody>
						<tr>
							<td>组名:</td>
							<td>
								<input type='text' name='keys[group_name]' value='<?php echo empty($keys['group_name']) ? '' : $keys['group_name'];?>' />
							</td>							
							<td>
								<input class="btn" type="submit" value="提 交"/>
							</td>
						</tr>						
					</tbody>
				</table>
			</form>
		</div>

		<div id="tabs-2">
			<form method="post" action="?m=group&a=save">
				<table >
					<tbody>
						<tr>
							<td>组名:</td>
							<td>
								<input type='text' name='info[group_name]' />
							</td>							
							<td>备注:</td>
							<td>
								<input type='text' name='info[memo]' />
							</td>
							<td>
								<input class="btn" type="submit" value="提 交"/>
							</td>
						</tr>						
					</tbody>
				</table>
			</form>
		</div>
		
	</div>

	<br />
	<h3>用户组列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
		<form method="post" onsubmit="return confirm('该操作不可恢复，您确认要删除这些用户组吗？');" action="?m=group&a=batch_delete">
			<table id='group_data' class="datalist fixwidth" >
				<tbody>	
					<tr>
						<th>
							<input id="chkall" class="checkbox" type="checkbox"  name="chkall"/>
							<label for="chkall">删除</label>
						</th>
						<th>用户组名</th>						
						<th>备注</th>
						<th>编辑</th>
					</tr>
					<?php foreach($data as $item) {
						$name = urlencode($item['group_name']);
						echo "<tr>\n";
						echo "<td class='option'><input type='checkbox' class='checkbox' value='{$item['id']}' name='ids[]' /></td>";
						echo "<td ><a href='?m=user&a=index&keys[group]={$name}' title='查看用户'>{$item['group_name']}</a></td>";					
						echo "<td >{$item['memo']}</td>";
						echo "<td ><a href='?m=user&a=index&keys[group]={$name}'>用户</a> | <a href='?m=group&a=edit&id={$item['id']}'>修改</a></td>"; 
						echo "</tr>\n";
		}?>
					<tr class="nobg">
						<td>
							<input class="btn" type="submit" value="提 交"/>
						</td>
						<td class="tdpage" colspan="8"> <?php echo empty($viewData['pager']) ? '' : $viewData['pager']?></td>
					</tr>
				</tbody>
			</table>
			
		</form>
		<?php }?>
	</div>
</div>
<div id='dialog'  style='display:none;' ></div>
<?php echo script_link('common.js', JS_ROOT);?>
<script type='text/javascript' >
	
	$().ready(function(){
		$('#tabs').tabs();
		/*$('#dialog').dialog({
			autoOpen: false,					
			modal: true,
			bgiframe:true,
			width:300,
		
			bgiframe: true
			});*/
		//$('#mem_name').click(dialog_mem);
		$('#chkall').click(function(){
			var table = $(this).parents('.datalist').eq(0);
			var check = $(this).attr('checked');
			table.find(':checkbox').attr('checked',check);
		});
		tableOver('group_data', 'trhover');
	});	
</script>
<?php  view('footer');?>