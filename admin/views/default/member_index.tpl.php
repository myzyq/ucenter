<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
 $keys = empty($viewData['keys']) ? array() : $viewData['keys'];

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
			<li><a href="#tabs-1">搜索员工</a></li>
			<li><a href="#tabs-2">添加员工</a></li>
		</ul>
		<div id="tabs-1">
			<form method="get" action="?">
				<input type="hidden" name="page" value="1" />
				<input type="hidden" name="m" value="member" />
				<input type="hidden" name="a" value="index" />
				<table >
					<tbody>
						<tr>
							<td>姓名:</td>
							<td>
								<input type='text' name='keys[real_name]' value='<?php echo empty($keys['real_name']) ? '' : $keys['real_name']?>' />
							</td>
							<td>UID:</td>								
							<td>
								<input type='text' name='keys[id]' value='<?php echo !isset($keys['id']) || $keys['id'] != 0 ? '' : $keys['id'] ;?>' />
							</td>
							<td>手机:</td>
							<td>
								<input type='text' name='keys[mobile]' value='<?php echo empty($keys['mobile'])? '' : $keys['mobile'];?>' />
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
			<form method="post" action="?m=member&a=save">
				<table width="100%">
					<tbody>
						<tr>
							<td>姓名:</td>
							<td>
								<input type='text' name='info[real_name]' />
							</td>
							<td>性别:</td>								
							<td>
								<label><input  type='radio' name='info[sex]' value='0' />女</label>
								<label><input  type='radio' name='info[sex]' value='1' checked="checked" />男</label>
							</td>
							<td>出生日期:</td>
							<td>
								<input type='text' name='info[birthday]' />
							</td>
							<td>部门:</td>
							<td>
								<input type='text' id='dep_name'  />
								<input type='hidden' id='dep_id' name='info[dep_id]' />
							</td>
							<td>
								<input class="btn" type="submit" value="提 交"/>
							</td>
						</tr>
						<tr>
							<td>电话:</td>
							<td><input type='text' name='info[tel]' /></td>
							<td>手机:</td>
							<td><input type='text' name='info[mobile]' /></td>
							<td>职务:</td>
							<td><input type='text' name='info[position]' /></td>
							<td>用户:</td>
							<td>
							<select name="info[userid]">
							     <option value="0">无</option>
							     <?php if (!empty($viewData['alluser'])) {
							         foreach($viewData['alluser'] as $item) {
							             echo "<option value='{$item['id']}'>{$item['name']}</option>";
							         }
							     }?>
							</select>
							</td>
							<td ></td>
						</tr>										
					</tbody>
				</table>
			</form>
		</div>
		
	</div>

	<br />
	<h3>员工列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
		<form method="post" onsubmit="return confirm('该操作不可恢复，您确认要删除这些员工吗？');" action="?m=member&a=bath_delete">
			<table id='member_data' class="datalist fixwidth"  >
				<tbody>	
					<tr>
						<th>
							<input id="chkall" class="checkbox" type="checkbox"  name="chkall"/>
							<label for="chkall">删除</label>
						</th>
						<th>姓名</th>
						<th>性别</th>
						<th>部门</th>
						<th>出生日期</th>						
						<th>电话</th>
						<th>手机</th>
						<th>职务</th>
						<th>备注</th>
						<th>编辑</th>
					</tr>
					<?php foreach($data as $item) {						
						echo "<tr>\n";
						echo "<td class='option'><label><input type='checkbox' class='checkbox' value='{$item['id']}' name='ids[]' />{$item['id']}</label></td>";
						echo "<td >{$item['real_name']}</td>";
						$sex = empty($item['sex']) ? '女' : '男';
						echo "<td >{$sex}</td>";
						echo "<td >{$item['dep_name']}</td>";
						echo "<td >{$item['birthday']}</td>";
						echo "<td >{$item['tel']}</td>";					
						echo "<td >{$item['mobile']}</td>"; 
						echo "<td >{$item['position']}</td>"; 					
						echo "<td >{$item['memo']}</td>"; 
						echo "<td ><a href='?m=member&a=edit&id={$item['id']}'>修改</a></td>"; 
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
			width:300,
		
			bgiframe: true
			});
		$('#dep_name').click(dialog_dept).keypress(dialog_dept);
		$('#chkall').click(function(){
			var table = $(this).parents('.datalist').eq(0);
			var check = $(this).attr('checked');
			table.find(':checkbox').attr('checked',check);
		});	
		tableOver('member_data', 'trhover');
	});
	function selected_dept(data) {
		$('#dep_name').val(data.name);
		$('#dep_id').val(data.id);
		dialog.dialog('close');
	}
	function dialog_dept() {
		dialog.dialog('option', 'title', '选择部门');
		url = 'enter.php?m=dialog&a=search_dept&callback=selected_dept';
		dialog.load(url);
		dialog.dialog('open');
	}
</script>
<?php  view('footer');?>