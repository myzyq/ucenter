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
			 </div>";
	}?>
	<div id="tabs" >
		<ul>
			<li><a href="#tabs-1">搜索部门</a></li>
			<li><a href="#tabs-2">添加部门</a></li>
		</ul>
		<div id="tabs-1">
			<form method="get" action="?">
				<input type="hidden" name="page" value="1" />
				<input type="hidden" name="m" value="dept" />
				<input type="hidden" name="a" value="index" />
				<table >
					<tbody>
						<tr>
							<td>部门名:</td>
							<td>
								<input type='text' name='keys[name]' value='<?php echo empty($keys['name']) ? '' : $keys['name'] ;?>' />
							</td>
							<td>上层部门:</td>								
							<td>					
								<ul id='icons' class='ui-widget ui-helper-clearfix'>
									<li>
										<input type='text' name='keys[parent_name]' id='parent_name' value='<?php echo empty($keys['parent_name']) ? '' : $keys['parent_name'] ;?>' />
										<input type='hidden' name='keys[parent]' id='parent' value='<?php echo empty($keys['parent']) ? '' : $keys['parent'] ;?>' />
									</li>
									<li class='ui-state-default ui-corner-all' title='清除'><span class='ui-icon ui-icon-trash' onclick="clear_k_parent();"></span></li>
								</ul>
								
							</td>
							<td>&nbsp;负责人:</td>
							<td>
								<ul id='icons' class='ui-widget ui-helper-clearfix'>
									<li>
										<input type='text' name='keys[direname]' id='dire_name' value='<?php echo empty($keys['direname']) ? '' : $keys['direname'] ;?>' />
										<input type='hidden' name='keys[director]' id='director' value='<?php echo empty($keys['director']) ? '' : $keys['director'] ;?>' />
									</li>
									<li class='ui-state-default ui-corner-all' title='清除'><span class='ui-icon ui-icon-trash' onclick="clear_k_dire();"></span></li>
								</ul>		
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
			<form method="post" action="?m=dept&a=save">
				<table >
					<tbody>
						<tr>
							<td>部门名:</td>
							<td>
								<input type='text' name='info[name]' />
							</td>
							<td>上层部门:</td>								
							<td>
								<ul id='icons' class='ui-widget ui-helper-clearfix'>
									<li>
										<input type='text' name='parent' id='parent_name1' />
										<input type='hidden' name='info[parent_id]' value='0' id='parent_id' />
									</li>
									<li class='ui-state-default ui-corner-all' title='清除'><span class='ui-icon ui-icon-trash' onclick="clear_a_parent();"></span></li>
								</ul>	
							</td>
							<td>&nbsp;负责人:</td>
							<td>
								<ul id='icons' class='ui-widget ui-helper-clearfix'>
									<li>
										<input type='text' name='direname' id='dire_name_a'  />
										<input type='hidden' name='info[director]' value='0' id='director_a' />
									</li>
									<li class='ui-state-default ui-corner-all' title='清除'><span class='ui-icon ui-icon-trash' onclick="clear_a_dire();"></span></li>
								</ul>
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
	<h3>部门列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
		<form method="post" onsubmit="return confirm('该操作不可恢复，您确认要删除这些部门吗？');" action="?m=dept&a=bath_delete">
			<table class="datalist fixwidth" id="dep_data" >
				<tbody>	
					<tr>
						<th>
							<input id="chkall" class="checkbox" type="checkbox"  name="chkall"/>
							<label for="chkall">删除</label>
						</th>
						<th>部门名</th>
						<th>上层部门</th>
						<th>负责人</th>						
						<th>备注</th>
						<th>操作</th>
					</tr>
					<?php foreach($data as $item) {
						echo "<tr>\n";
						echo "<td class='option'><label><input type='checkbox' class='checkbox' value='{$item['id']}' name='ids[]' />{$item['id']}</label></td>";
						echo "<td >{$item['name']}</td>";
						echo "<td >{$item['parent_name']}</td>";
						echo "<td >{$item['director_name']}</td>";						
						echo "<td >{$item['memo']}</td>";
						echo "<td ><a href='?m=dept&a=index&keys[parent_name]={$item['name']}&keys[parent]={$item['id']}'>子部门</a>|<a href='?m=dept&a=edit&id={$item['id']}'>修改</a></td>"; 
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
	var dialog ;
	$().ready(function(){
		$('#tabs').tabs();
		dialog = $('#dialog').dialog({
			autoOpen: false,					
			modal: true,
			bgiframe:true,
			width:400,
		
			bgiframe: true
			});
		$('#parent_name').click(dialog_dep1).keypress(dialog_dep1);
		$('#parent_name1').click(dialog_dep2).keypress(dialog_dep2);
		$('#dire_name').click(dialog_dire).keypress(dialog_dire);
		$('#dire_name_a').click(dialog_dire1).keypress(dialog_dire1);
		$('#chkall').click(function(){
			var table = $(this).parents('.datalist').eq(0);
			var check = $(this).attr('checked');
			table.find(':checkbox').attr('checked',check);
		});
		tableOver('dep_data', 'trhover');
	});

	//查询相关	
	//负责人
	function get_select_mem(data) {
		$('#dire_name').val(data.name);
		$('#director').val(data.id);
		dialog.dialog('close');
	}

	function clear_k_dire() {
		$('#dire_name').val('');
		$('#director').val('');
	}

	function dialog_dire() {
		dialog.dialog('option', 'title', '选择负责人');
		url = 'enter.php?m=dialog&a=search_member&callback=get_select_mem';
		dialog.load(url);
		dialog.dialog('open');
	}
	//部门
	function get_select_dept(data) {
		
		$('#parent_name').val(data.name);
		$('#parent').val(data.id);
		dialog.dialog('close');
	}

	function clear_k_parent() {
		$('#parent_name').val('');
		$('#parent').val('');
	}

	function dialog_dep1() {
		dialog.dialog('option', 'title', '选择上级部门');
		url = 'enter.php?m=dialog&a=search_dept&callback=get_select_dept';
		dialog.load(url);
		dialog.dialog('open');
	}

	//插入相关
	//负责人
	function get_select_mem1(data) {
		$('#dire_name_a').val(data.name);
		$('#director_a').val(data.id);
		dialog.dialog('close');
	}
	function clear_a_dire() {
		$('#dire_name_a').val('');
		$('#director_a').val('0');
	}
	function dialog_dire1() {
		dialog.dialog('option', 'title', '选择负责人');
		url = 'enter.php?m=dialog&a=search_member&callback=get_select_mem1';
		dialog.load(url);
		dialog.dialog('open');
	}
	//部门
	function get_select_dept1(data) {
		$('#parent_name1').val(data.name);
		$('#parent_id').val(data.id);
		dialog.dialog('close');
	}
	function clear_a_parent() {
		$('#parent_name1').val('');
		$('#parent_id').val('0');
	}
	function dialog_dep2(){
		dialog.dialog('option', 'title', '选择上级部门');
		url = 'enter.php?m=dialog&a=search_dept&callback=get_select_dept1';
		dialog.load(url);
		dialog.dialog('open');
	}
	
	
	
	
</script>
<?php  view('footer');?>