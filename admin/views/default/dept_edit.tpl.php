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
		编辑部门
		<a class="sgbtn" href="enter.php?m=dept&a=index">返回部门列表</a>
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=dept&a=update">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">部门名:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[name]' value='<?php echo empty($data['name']) ? '' : $data['name']; ?>' />						
						</td>
						<td>部门要有名称，尽量不要重名</td>
					</tr>				
					<tr><th colspan="2">上层部门:</th></tr>
					<tr>
						<td>
							<ul id='icons' class='ui-widget ui-helper-clearfix'>
								<li>
									<input type='text' name='parent' id='parent_name' value="<?php echo empty($data['parent_name']) ? '' : $data['parent_name'];?>"  />
									<input type='hidden' name='info[parent_id]' value='<?php echo empty($data['parent_id']) ? 0 : intval($data['parent_id']);?>' id='parent_id'  />
								</li>
								<li class='ui-state-default ui-corner-all' title='清除'><span class='ui-icon ui-icon-trash' onclick="clear_dept();"></span></li>
							</ul>	</td>
						<td>如果没有上级部门，请点一下清除按钮</td>
					</tr>
					<tr><th colspan="2">负责人:</th></tr>
					<tr>
						<td>
							<ul id='icons' class='ui-widget ui-helper-clearfix'>
									<li>
										<input type='text' name='direname' id='dire_name' value='<?php echo empty($data['director_name']) ? '' : $data['director_name'] ;?>' />
										<input type='hidden' name='info[director]' value='<?php echo empty($data['director']) ? 0 : intval($data['director']);?>' id='director' />
									</li>
									<li class='ui-state-default ui-corner-all' title='清除'><span class='ui-icon ui-icon-trash' onclick="clear_mem();"></span></li>
								</ul>
						</td>
						<td>如果没有负责人，请点一下清除按钮</td>
					</tr>
					<tr><th colspan="2">备注:</th></tr>
					<tr>
						<td><textarea name='info[memo]'><?php echo empty($data['memo']) ? '' : $data['memo'] ;?></textarea></td>
						<td></td>
					</tr>
				</tbody>
			</table>
			<div class="opt">
				<input type='hidden' name='info[id]' value='<?php echo empty($data['id']) ? '' : $data['id'];?>' />
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>
<div id='dialog'  style='display:none;' ></div>
<script type='text/javascript' >
	var dialog ;
	$().ready(function(){		
		dialog = $('#dialog').dialog({
			autoOpen: false,					
			modal: true,
			bgiframe:true,
			width:300,
		
			bgiframe: true
			});
		$('#parent_name').click(dialog_dep).keypress(dialog_dep);
		
		$('#dire_name').click(dialog_dire).keypress(dialog_dire);
		
	});

	//查询相关	
	//负责人
	function get_select_mem(data) {
		$('#dire_name').val(data.name);
		$('#director').val(data.id);
		dialog.dialog('close');
	}

	function clear_mem() {
		$('#dire_name').val('');
		$('#director').val('0');
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

	function clear_dept() {
		$('#parent_name').val('');
		$('#parent').val('0');
	}

	function dialog_dep() {
		dialog.dialog('option', 'title', '选择上级部门');
		url = 'enter.php?m=dialog&a=search_dept&callback=get_select_dept';
		dialog.load(url);
		dialog.dialog('open');
	}

	
	
	
</script>
<?php  view('footer');?>