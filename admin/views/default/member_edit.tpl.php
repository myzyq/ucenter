<?php defined('U_CENTER') or exit('Access Denied');?>
<?php 
 view('header');
?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">
	<?php if(isset($viewData['msg'])){
		
		$class = $viewData['msg']['flag'] == true  ? 'correctmsg' : 'errormsg';
		echo "<div class='{$class}'>
				<p>{$viewData['msg']['msg']}</p>
			 </div>";
	}?>
	<br />
	<h3 class="marginbot">
		编辑员工
		<a class="sgbtn" href="enter.php?m=member&a=index">返回员工列表</a>
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=member&a=update">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">姓名:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[real_name]' disabled='disabled' value='<?php echo empty($data['real_name']) ? '' : $data['real_name']; ?>' />						
						</td>
						<td>不要重名，重名要加前缀</td>
					</tr>	
					<tr><th colspan="2">性别:</th></tr>
					<tr>
						<td>
							<label><input  type='radio' name='info[sex]' value='0' <?php echo empty($data['sex']) ? 'checked="checked"' : '';?> />女</label>
							<label><input  type='radio' name='info[sex]' value='1' <?php echo !empty($data['sex']) ? 'checked="checked"' : '';?>  />男</label>
						</td>
						<td></td>
					</tr>		
					<tr><th colspan="2">出生日期:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[birthday]' value='<?php echo empty($data['birthday']) ? '' : $data['birthday'];?>' />
						</td>
						<td>格式 yyy-mm-dd</td>
					</tr>	
					<tr><th colspan="2">部门:</th></tr>
					<tr>
						<td>
							<ul id='icons' class='ui-widget ui-helper-clearfix'>
								<li>
									<input type='text' name='dept_name' id='dep_name' value="<?php echo empty($data['dep_name']) ? '' : $data['dep_name'];?>"  />
									<input type='hidden' name='info[dep_id]' value='<?php echo empty($data['dep_id']) ? 0 : intval($data['dep_id']);?>' id='dept_id'  />
								</li>
								<li class='ui-state-default ui-corner-all' title='清除'><span class='ui-icon ui-icon-trash' onclick="clear_dept();"></span></li>
							</ul>	</td>
						<td>如果没有部门，请点一下清除按钮</td>
					</tr>
					<tr><th colspan="2">电话:</th></tr>
					<tr>
							<td><input type='text' name='info[tel]' value='<?php echo empty($data['tel']) ? '' : $data['tel'] ;?>' /></td>
							<td></td>
					</tr>
					<tr><th colspan="2">手机:</th></tr>
					<tr>
							<td><input type='text' name='info[mobile]' value='<?php echo empty($data['mobile']) ? '' : $data['mobile'];?>' /></td>
							<td></td>
					</tr>	
					<tr><th colspan="2">职务:</th></tr>
					<tr>
							<td><input type='text' name='info[position]' value='<?php echo empty($data['position']) ? '' : $data['position'] ;?>' /></td>
							<td></td>
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
		$('#dep_name').click(dialog_dep).keypress(dialog_dep);
		
	});


	//部门
	function get_select_dept(data) {
		$('#dep_name').val(data.name);
		$('#dept_id').val(data.id);
		dialog.dialog('close');
	}

	function clear_dept() {
		$('#dep_name').val('');
		$('#dept_id').val('0');
	}

	function dialog_dep() {
		dialog.dialog('option', 'title', '选择上级部门');
		url = 'enter.php?m=dialog&a=search_dept&callback=get_select_dept';
		dialog.load(url);
		dialog.dialog('open');
	}

	
	
	
</script>
<?php  view('footer');?>