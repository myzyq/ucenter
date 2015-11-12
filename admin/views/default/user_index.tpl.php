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
        用户管理
        <a class="sgbtn" href="enter.php?m=user&a=add">添加用户</a>
    </h3>
	<div id="tabs" >
		<ul>
			<li><a href="#tabs-1">搜索用户</a></li>
			<li><a href="#tabs-2">添加用户</a></li>
		</ul>
		<div id="tabs-1">
			<form method="get" action="?">
				<input type="hidden" name="page" value="1" />
				<input type="hidden" name="m" value="user" />
				<input type="hidden" name="a" value="index" />				
				<table >
					<tbody>
						<tr>
							<td>用户名:</td>
							<td>
								<input type='text' name='keys[name]' value='<?php echo empty($keys['name']) ? '' : $keys['name'];?>' />
							</td>
							<td>用户组</td>
                            <td>
                                <select name='keys[group_id]'>
                                <option value='-1'>全部</option>
                                 <?php if(!empty($viewData['groups'])){
                                         foreach($viewData['groups'] as $group){  
                                         	$select = isset($keys['group_id']) && $keys['group_id'] == $group['id'] ? " selected='selected'" : "";                                     
                                            echo "<option value='{$group['id']}'  {$select}>{$group['group_name']}</option>";
                                         }
                                 } else{
                                   echo "<option value='0' >默认组</option>";
                                 }?>
                                </select>
                            </td>
							<td>UID:</td>								
							<td>
								<input type='text' name='keys[id]' value='<?php echo empty($keys['id']) ? '' : $keys['id'];?>' />
							</td>
							<td>用户组:</td>								
							<td>
								<input type='text' name='keys[group]' value='<?php echo empty($keys['group']) ? '' : $keys['group'];?>' />
							</td>
							<td>Email:</td>
							<td>
								<input type='text' name='keys[email]' value='<?php echo empty($keys['email']) ? '' : $keys['email'];?>' />
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
			<form method="post" action="?m=user&a=save">
				<table >
					<tbody>
						<tr>
							<td>用户名:</td>
							<td>
								<input type='text' name='info[name]' />
							</td>	
							<td>密码:</td>
                            <td>
                                <input type='text' name='info[password]' />
                            </td>						
							<td>Email:</td>
							<td>
								<input type='text' name='info[email]' />
							</td>
							<td>用户组</td>
							<td>
								<select name='info[group_id]'>
	                             <?php if(!empty($viewData['groups'])){
	                                     foreach($viewData['groups'] as $group){   
	                                     	$select = empty($group['id']) ? " selected = 'selected'" : "";                                     
	                                        echo "<option value='{$group['id']}' $select >{$group['group_name']}</option>";
	                                     }
	                             } else{
	                               echo "<option value='0' selected='selected'>默认组</option>";
	                             }?>
	                            </select>
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
	<h3>用户列表</h3>
	<div class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
		<form id='list_from' method="post"  action="?m=user&a=batch_app">
			<table id='user_data' class="datalist fixwidth" >
				<tbody>	
					<tr>
						<td>
							<input id="chkall" class="checkbox" type="checkbox"  name="chkall"/>
							<label for="chkall">全选</label>
						</td>						
						<td style="width:60px;">用户名</td>
						<td style="width:60px;">用户组</td>
						<td style="width:50px;">状态</td>
						<td>上次登录时间</td>
						<td>上次登录IP</td>
						<td>登录次数</td>
						<td>绑定状态</td>
						<td>编辑</td>
					</tr>
					<?php foreach($data as $item) {
						echo "<tr>\n";
						$flag = empty($item['flag']) ? '可登录' : '已禁用';
						
						echo "<td class='option'><input type='checkbox' class='checkbox' value='{$item['id']}' name='ids[]' /></td>";
						echo "<td >{$item['name']}</td>";
						echo "<td >{$item['group_name']}</td>";
						echo "<td >{$flag}</td>";
						$logintime = date("Y-m-d H:i", strtotime($item['last_login_time']));
						echo "<td >{$logintime}</td>";
						echo "<td >{$item['last_login_ip']}</td>";
						echo "<td >{$item['login_times']}</td>";
						$isband = empty($item['is_band']) ? '关闭' : '开启' ;
						echo "<td >{$isband}</td>"; 
						$ips = str_replace("\n", ";", $item['ip_band']);
						echo "<td ><a href='?m=user&a=edit&id={$item['id']}'>改</a>
						| <a href='?m=user&a=edit_pass&id={$item['id']}' onclick='return confirm(\"修改密码吗？\");'>改密</a>
						  | <a href='?m=user&a=apps&uid={$item['id']}' title='应用绑定'>绑</a> </td>"; 
						echo "</tr>\n";
		}?>
					<tr class="nobg">
						<td colspan="2">
							<input id='batch_mv_btn' class="btn" type="submit" value="移动到"/>
							<select name='group'>
                                 <?php if(!empty($viewData['groups'])){
                                         foreach($viewData['groups'] as $group){                                        
                                            echo "<option value='{$group['id']}' >{$group['group_name']}</option>";
                                         }
                                 } else{
                                      echo "<option value='0' selected='selected'>默认组</option>";
                                 }?>
                                </select> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id='batch_del_btn' class="btn" type="submit" value="批量删除"/>
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
			width:300,
		
			bgiframe: true
			});
		//$('#mem_name').click(dialog_mem);
		$('#chkall').click(function(){
			var table = $(this).parents('.datalist').eq(0);
			var check = $(this).attr('checked');
			table.find(':checkbox').attr('checked',check);
		});
		
		$('#batch_mv_btn').click(function(){
			//批量修改
			if( confirm('您确认要用户的移动到目标组吗？')){
				$('#list_from').attr('action', '?m=user&a=batch_mv');
				return true;
			}else{
				return false;
			}
			
		});
		$('#batch_del_btn').click(function(){
			//批量修改
			if( confirm('该操作不可恢复，您确认要删除这些用户吗？')){
				$('#list_from').attr('action', '?m=user&a=bath_delete');
				return true;
			}else{
				return false;
			}
			
		});
		tableOver('user_data', 'trhover');
	});
	function selected_mem(data) {
		$('#mem_name').val(data.name);
		$('#mem_id').val(data.id);
		dialog.dialog('close');
	}
	function dialog_mem() {
		dialog.dialog('option', 'title', '选择员工');
		url = 'enter.php?m=dialog&a=search_member';
		dialog.load(url);
		dialog.dialog('open');
	}
</script>
<?php  view('footer');?>