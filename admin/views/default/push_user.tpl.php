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
				$data = empty($viewData['app']) ? array() : $viewData['app'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
	
		?>
		<form class="dataform" method="post" action="?m=app&a=sync_users">
			<table class="opt">
				<tbody>	
					<tr><th colspan="2">应用名:</th></tr>
					<tr>
						<td>
						    <input type="hidden" name="appid" value="<?php echo empty($data['id']) ? '' : $data['id'];?>" />
							<?php echo empty($data['app_name']) ? '' : $data['app_name']; ?>					
						</td>
						<td></td>
					</tr>
					<tr><th colspan="2">地址:</th></tr>
                    <tr>
                        <td>
                            <?php echo empty($data['app_url']) ? '' : $data['app_url']; ?>                    
                        </td>
                        <td></td>
                    </tr>       				
                    <?php 
                        if(!empty($viewData['users'])){
                            $users = $viewData['users'];
                            //$appusers = empty($viewData['appusers']) ? array() : $viewData['appusers'];
                            
                            foreach($users as $k => $item) {
                            	$groupname = $item['name'];
                            	
                    	?>
					<tr><th colspan="2"><?php echo $groupname;?><label><input type="checkbox" id='<?php echo "check_{$k}";?>' class='check_all' />全选</label></th></tr>
					<tr>
						<td >
							<?php
							   foreach($item['users'] as $u){							   	
							        // $select = in_array($u['id'], $appusers) ? " checked='checked'" : "";
							         $select = "";
							         echo "<label style='float:left; width: 140px; padding-left: 5px; '><input type='checkbox' class='users_check_{$k}' name='ids[]' {$select} value='{$u['id']}' />{$u['name']}</label>";
							   }
							   
							?>
						</td>
						<td></td>
					</tr>
					<?php } 
                        }?>
				</tbody>
			</table>
			<br />
			<div class="opt">
				<input type='hidden' name='app' value='<?php echo empty($data['id']) ? '' : $data['id'];?>' />
				<input class="btn" type="submit" tabindex="3" value=" 提 交 " name="submit"/>
			</div>			
		</form>
		
	</div>
</div>
<div id='dialog'  style='display:none;' ></div>
<script type='text/javascript' >
	$().ready(function(){
		$('.check_all').click(function(){
			
			checkall($(this).attr('id'));			
		});
	});	
	
	function checkall(tagid){
		var tag = $("#" + tagid);
		
		check = tag.attr('checked');
		$('.users_' + tagid).attr('checked', check);
	}
</script>
<?php  view('footer');?>
