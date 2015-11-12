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
	
       	<div style="float:left;margin-left:3px;width:250px;width:250px\9;BORDER-RIGHT: #9EBECB 1px solid;BORDER-LEFT: #9EBECB 1px solid;"  class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
                $count =1;
		?>
		<h3>工具类应用列表</h3>
			<table id='app_data1'  class="datalist fixwidth" >
				<tbody>	
					<tr>
						
						<th>应用名</th>
						<th>权限管理</th>
												
						
					</tr>
					<?php foreach($data as $item) {
						 if($item['app_type'] ==1){
                            $url = $item['uc_sign']=='tp_rbac'?$item['api_addr']."/uc_login":$item['app_url'];
                            $count = $count +1;
                            echo "<tr>\n";			
                            echo "<td ><a href='{$url}' target='_blank'>{$item['app_name']}</a></td>";
                            echo "<td >{$item['pri_manager']}</td>";
                            echo "</tr>\n";
                        }
                    }
                    for($j=0;$j<21-$count;$j++){
                        echo "<tr>\n";			
						echo "<td >&nbsp;</td>";
						echo "<td >&nbsp;</td>";
										
						echo "</tr>\n";
                    }
                    ?>
					<tr class="nobg">
						<td>&nbsp;</td>
						<td class="tdpage" colspan="8"></td>
					</tr>
				</tbody>
			</table>
					
		<?php 
        }?>
	</div>
    
	<div style="float:left;margin-left:3px;width:250px;width:250px\9;BORDER-RIGHT: #9EBECB 1px solid;BORDER-LEFT: #9EBECB 1px solid;" class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
                 $count =1;
		?>
		<h3>管理类应用列表</h3>
			<table id='app_data2'  class="datalist fixwidth" >
				<tbody>	
					<tr>
						
						<th>应用名</th>
						<th>权限管理</th>
												
						
					</tr>
					<?php foreach($data as $item) {
                        if($item['app_type'] ==2){
                            $url = $item['uc_sign']=='tp_rbac'?$item['api_addr']."/uc_login":$item['app_url'];
                            $count = $count +1;
                            echo "<tr>\n";			
                            echo "<td ><a href='{$url}' target='_blank'>{$item['app_name']}</a></td>";
                            echo "<td >{$item['pri_manager']}</td>";
                            echo "</tr>\n";
                        }
                    }
                     for($j=0;$j<21-$count;$j++){
                        echo "<tr>\n";			
						echo "<td >&nbsp;</td>";
						echo "<td >&nbsp;</td>";
										
						echo "</tr>\n";
                    }?>
					<tr class="nobg">
						<td>&nbsp;</td>
						<td class="tdpage" colspan="8"></td>
					</tr>
				</tbody>
			</table>
					
		<?php }?>
	</div>
    
 
    	<div style="float:left;margin-left:3px;width:250px;width:250px\9;BORDER-RIGHT: #9EBECB 1px solid;BORDER-LEFT: #9EBECB 1px solid;"  class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
                $count =1;
		?>
		<h3>数据类应用列表</h3>
			<table id='app_data3' class="datalist fixwidth" >
				<tbody>	
					<tr>
						
						<th>应用名</th>
						<th>权限管理</th>
												
						
					</tr>
					<?php foreach($data as $item) {
						 if($item['app_type'] ==3){
                            $url = $item['uc_sign']=='tp_rbac'?$item['api_addr']."/uc_login":$item['app_url'];
                            $count = $count +1;
                            echo "<tr>\n";			
                            echo "<td ><a href='{$url}' target='_blank'>{$item['app_name']}</a></td>";
                            echo "<td >{$item['pri_manager']}</td>";
                            echo "</tr>\n";
                        }
                    }
                    for($j=0;$j<21-$count;$j++){
                        echo "<tr>\n";			
						echo "<td >&nbsp;</td>";
						echo "<td >&nbsp;</td>";
										
						echo "</tr>\n";
                    }
                    ?>
					<tr class="nobg">
						<td>&nbsp;</td>
						<td class="tdpage" colspan="8"></td>
					</tr>
				</tbody>
			</table>
					
		<?php }?>
	</div>

   	<div style="float:left;margin-left:3px;width:250px;width:250px\9;BORDER-RIGHT: #9EBECB 1px solid;BORDER-LEFT: #9EBECB 1px solid;"  class="mainbox">
		<?php if(isset($viewData['data'])) {
				$data = $viewData['data'];
                $count =1;
		?>
		<h3>监控类应用列表</h3>
			<table id='app_data4' class="datalist fixwidth" >
				<tbody>	
					<tr>
						
						<th>应用名</th>
						<th>权限管理</th>
												
						
					</tr>
					<?php foreach($data as $item) {
						 if($item['app_type'] ==4){
                            $url = $item['uc_sign']=='tp_rbac'?$item['api_addr']."/uc_login":$item['app_url'];
                            $count = $count +1;
                            echo "<tr>\n";			
                            echo "<td ><a href='{$url}' target='_blank'>{$item['app_name']}</a></td>";
                            echo "<td >{$item['pri_manager']}</td>";
                            echo "</tr>\n";
                        }
                    }
                    for($j=0;$j<21-$count;$j++){
                        echo "<tr>\n";			
						echo "<td >&nbsp;</td>";
						echo "<td >&nbsp;</td>";
						echo "</tr>\n";
                    }
                    ?>
					<tr class="nobg">
						<td>&nbsp;</td>
						<td class="tdpage" colspan="8"></td>
					</tr>
				</tbody>
			</table>
				
		<?php }?>
	</div>    
</div>
<div id='dialog'  style='display:none;' ></div>
<?php echo script_link('ui.datepicker-zh-CN.js', JS_ROOT . 'jquery/ui/i18n/');?>
<?php echo script_link('common.js', JS_ROOT);?>
<script type='text/javascript' >
	$().ready(function(){
		tableOver('app_data1', 'trhover');
        tableOver('app_data2', 'trhover');
        tableOver('app_data3', 'trhover');
        tableOver('app_data4', 'trhover');
	});
</script>
<?php  view('footer');?>
