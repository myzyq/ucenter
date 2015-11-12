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
		管理应用
		<a class="sgbtn" href="enter.php?m=app&a=index">返回应用列表</a>
		<a class="sgbtn" href="enter.php?m=app&a=edit">添加新应用</a>
	</h3>
	<div class="mainbox">
		<?php 
				$data = empty($viewData['data']) ? array() : $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=app&a=save">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">应用名:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[app_name]'  value='<?php echo empty($data['app_name']) ? '' : $data['app_name']; ?>' />						
						</td>
						<td>应用名应尽量不重复</td>
					</tr>
					<tr><th colspan="2">应用URL:</th></tr>
					<tr>
						<td>
							<input type='text' name='info[app_url]' style='width:430px;' value='<?php echo empty($data['app_url']) ? '' : $data['app_url']; ?>' />						
						</td>
						<td>应用的URL 用于显示用户访问列表</td>
					</tr>
					<tr><th colspan="2">回调API地址:</th></tr>
					<tr>
						<td><input type="text" name='info[api_addr]' style='width:430px;' value='<?php echo empty($data['api_addr']) ? '' : $data['api_addr'];?>' /></td>
						<td>应用为系统开放的接口</td>			
					</tr>
					<tr><th colspan="2">UCENTER标识:</th></tr>
					<tr>
						<td>
							<select dir="ltr" name="info[uc_sign]">
							<?php if(!empty($viewData['ucsign'])) {
							     	foreach($viewData['ucsign'] as $sign){
							     		$select = !empty($data['uc_sign']) && $sign == $data['uc_sign'] ? "selected='selected'" : "";
							     		echo "<option $select value='{$sign}'>$sign</option>";
							     	}
							     }?>
							</select>
						</td>
						<td>这个标识决定信息由哪个UCENTER同步</td>
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
						<td>IP与IP之间用";"分隔，开启IP绑定后，这些IP将被允许访问该应用</td>
					</tr>
					 <tr><th colspan="2">IP组:</th></tr>
                    <tr>
                        <td>
                           <?php $ipgroup = empty($viewData['ipgroup']) ? array() : $viewData['ipgroup'];?>
                           <select name="info[ip_group]">
                               <option value="0" <?php if(empty($ipgroup) || empty($data['ip_group'])) { echo "selected = 'selected'";  } ?>>未指定</option>
                               <?php foreach($ipgroup as $ip){
                                    $sele = !empty($data["ip_group"]) && $ip["id"] ==  $data["ip_group"] ? "selected='selected'" : "";
                                    echo "<option value='{$ip['id']}' $sele >{$ip['title']}</option>\n"; 
                               }?>
                           </select>
                        </td>
                        <td>IP组</td>
                    </tr>
					
					<tr><th colspan="2">负责人:</th></tr>
					<tr>
						<td><input type="text" name='info[pri_manager]' style='width:430px;' value='<?php echo empty($data['pri_manager']) ? '' : $data['pri_manager'];?>' /></td>
						<td>应用负责人</td>
					</tr>
					<tr><th colspan="2">应用类型:</th></tr>
					<tr>
						<td>
							<select  name="info[app_type]">
							   <?php $type = $viewData['data']['app_type'];
									if(!empty($type)){
										switch ($type){
											case "1":
												echo "<option value='1' selected='selected' >1</option>
													  <option value='2'>2</option>
											          <option value='3'>3</option>
											          <option value='4'>4</option>";	
												break;
											case "2":
												echo "<option value='1'>1</option>
													  <option value='2' selected='selected' >2</option>
											          <option value='3'>3</option>
											          <option value='4'>4</option>"; 
												break;
											case "3":
												echo "<option value='1'>1</option>
													  <option value='2'>2</option>
											          <option value='3' selected='selected' >3</option>
											          <option value='4'>4</option>";
												break;
											case "4":
												echo "<option value='1'>1</option>
													  <option value='2'>2</option>
											          <option value='3'>3</option>
											          <option value='4' selected='selected' >4</option>";
												break;
										}
									}else{
										echo "<option value='1'>1</option>
											  <option value='2'>2</option>
											  <option value='3'>3</option>
											  <option value='4'>4</option>";
									}
                               ?>
							</select>
						</td>
						<td>1：工具类 2：管理类 3：数据类 4：监控类</td>
					</tr>
					
					<tr><th colspan='2'>状态:</th></tr>
					<tr>
					<td>
					<?php $flag = !empty($viewData['data']['flag'])?$viewData['data']['flag']:"";
						if($flag=='1'){
							echo "
							<select  name='info[flag]'>
								<option value='0'>0</option>
								<option value='1' selected='selected' >1</option>
							</select>
						";	
						}else{
							echo "
							<select  name='info[flag]'>
								<option value='0' selected='selected' >0</option>
								<option value='1'>1</option>
							</select>
							";
						}
					?>
					</td>
					<td>0：正常 1：已删除</td>
					</tr>
					
					<tr><th colspan="2">备注:</th></tr>
					<tr>
						<td><textarea name='info[memo]'><?php echo empty($data['memo']) ? '' : $data['memo'] ;?></textarea></td>
						<td></td>
					</tr>
					
				</tbody>
			</table>
			<br />
			<div class="opt">
				<input type='hidden' name='info[id]' value='<?php echo empty($data['id']) ? '' : $data['id'];?>' />
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>
<div id='dialog'  style='display:none;' ></div>
<script type='text/javascript' >
	$().ready(function(){
		$('#check_all').click(function(){
			check = $(this).attr('checked');
			$('.groups_cbx').attr('checked', check);
		});
	});	
</script>
<?php  view('footer');?>
