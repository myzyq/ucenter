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
		编辑应用绑定用户IP
		<a class="sgbtn" href="enter.php?m=app&a=index">返回应用列表</a>
		<?php if(!empty($viewData['app_id'])){?>
		<a class="sgbtn" href="enter.php?m=app&a=appuser&keys[app_id]=<?php echo $viewData['app_id'];?>">返回绑定列表</a>
		<a class="sgbtn" href="enter.php?m=app&a=edit_appuserip&app_id=<?php echo $viewData['app_id'];?>">添加绑定</a>
		<?php }?>
	</h3>
	<div class="mainbox" style="height:300px;overflow:scroll;">
		<table id='user_data' class="datalist fixwidth" >
				<tbody>	
					<tr>
						<th>id</th>	
						<th>用户名</th>						
						<th>EMAIL</th>
						<th>编辑</th>
					</tr>
					<?php if(!empty($viewData['users'])) {
						foreach($viewData['users'] as $user) {
							echo "<tr>\n";					
							echo "<td >{$user['id']}</td>";
							echo "<td >{$user['name']}</td>";
							echo "<td >{$user['email']}</td>";
							echo "<td ><a href='?m=app&a=edit_appuserip&app_id={$viewData['app_id']}&user_id={$user['id']}'>绑定</a></td>"; 
							echo "</tr>\n";
						}
					}?>
				</tbody>
		</table>					
	</div>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=app&a=save_appuserip">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">应用名:</th></tr>
					<tr>
						<td>
							<input type='text' name='app_name' disabled='disabled'  value='<?php echo empty($data['app_name']) ? '' : $data['app_name']; ?>' />	
							<input type='hidden' name='info[app_id]' value='<?php echo empty($data['app_id']) ? '' : $data['app_id'];?>' />					
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">用户:</th></tr>
					<tr>
						<td>
						  <input type="text" name='user_name'  value='<?php echo empty($data['user_name']) ? '' : $data['user_name'];?>' />
						  <input type="hidden" name='info[user_id]'  value='<?php echo empty($data['user_id']) ? '' : $data['user_id'];?>' />
						</td>
						<td></td>			
					</tr>					
					<tr><th colspan="2">IP绑定状态:</th></tr>
					<tr>
						<td>
							<label ><input type="radio" name="info[is_band]" value="1" <?php echo !empty($data['is_band']) ? 'checked="checked"' : ''; ?> />开启</label>
							<label ><input type="radio" name="info[is_band]" value="0" <?php echo empty($data['is_band']) ? 'checked="checked"' : ''; ?> />关闭</label>
						</td>
						<td>是否开启IP绑定</td>
					</tr>				
					<tr><th colspan="2">IP绑定:</th></tr>
					<tr>
						<td><textarea rows="4" cols="16" name="info[ip_band]"><?php echo isset($data['ip_band']) ? $data['ip_band'] : ''; ?></textarea></td>
						<td>每一行只写一个IP，开启IP绑定后，这些IP在该用户将被允许访问该应用</td>
					</tr>	
					<tr><th colspan="2">IP组:</th></tr>
                    <tr>
                        <td>
                           <?php $ipgroup = empty($viewData['ipgroup']) ? array() : $viewData['ipgroup'];?>
                           <select name="info[ip_group]">
                               <option value="0" <?php if(empty($ipgroup) || empty($data['ip_group'])) { echo "selected = 'selected'";  } ?>>未指定</option>
                               <?php foreach($ipgroup as $ip){
                                    $sele = !empty($data["ip_group"]) && $ip["id"] == $data["ip_group"] ? "selected='selected'" : "";
                                    echo "<option value='{$ip['id']}' $sele >{$ip['title']}</option>\n"; 
                               }?>
                           </select>
                        </td>
                        <td>IP组</td>
                    </tr>   
					<tr><th colspan="2">是否启用:</th></tr>
					<tr>
						<td>
							<label ><input type="radio" name="info[flag]" value="1" <?php echo !empty($data['flag']) ? 'checked="checked"' : ''; ?> />禁用</label>
							<label ><input type="radio" name="info[flag]" value="0" <?php echo empty($data['flag']) ? 'checked="checked"' : ''; ?> />启用</label>
						</td>
						<td>如果禁用，用户将会被禁止访问该应用</td>
					</tr>											
				</tbody>
			</table>
			<br />
			<div class="opt">				
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>
<div id='dialog'  style='display:none;' ></div>
<?php echo script_link('common.js', JS_ROOT);?>
<script type="text/javascript" >
 $().ready(function(){
	tableOver('user_data', 'trhover');
 });
</script>
<?php  view('footer');?>