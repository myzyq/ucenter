<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
$keys = empty($viewData['keys']) ? array() : $viewData['keys'] ;
?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">
	<?php if(!empty($viewData['msg'])){
		//echo $viewData['rs']['flag'];
		$class = !empty($viewData['msg']['flag']) && $viewData['msg']['flag'] == true  ? 'correctmsg' : 'errormsg';
		echo "<div class='{$class}'>
				<p>{$viewData['msg']['msg']}</p>
			 </div>";
	}?>
	<br />
	<h3 class="marginbot">
		LINK管理		
	</h3>
	<div id="tabs" >
		<ul>
			<li><a href="#tabs-1">搜索</a></li>
			<li><a href="#tabs-2">添加</a></li>
		</ul>
		<div id="tabs-1">
			<form method="get" action="?">
				<input type="hidden" name="page" value="1" />
				<input type="hidden" name="m" value="link" />
				<input type="hidden" name="a" value="index" />
				<table >
					<tbody>
						<tr>
							<td>LINK名:</td>
							<td>
								<input type='text' name='keys[title]' value='<?php echo empty($keys['title']) ? '' : $keys['title'];?>' />
							</td>
							<td>URL:</td>                             
                            <td>
                                <input type='text' name='keys[url]' style='width:420px;'  value='<?php echo empty($keys['url']) ? '' : $keys['url'];?>' />
                            </td>   
							<td>分类:</td>								
							<td>
								<input type='text' name='keys[category]' value='<?php echo empty($keys['category']) ? '' : $keys['category'];?>' />
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
            <form method="post" action="?m=link&a=save">               
                <table >
                    <tbody>
                        <tr>
                            <td>LINK名:</td>
                            <td>
                                <input type='text' name='info[title]' value='<?php echo empty($info['title']) ? '' : $info['title'];?>' />
                            </td>
                            <td>URL:</td>                             
                            <td>
                                <input type='text' name='info[url]'  style='width:420px;' value='<?php echo empty($info['url']) ? '' : $info['url'];?>' />
                            </td>   
                            <td>分类:</td>                                
                            <td>
                                <input type='text' name='info[category]'  value='<?php echo empty($info['category']) ? '' : $info['category'];?>' />
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
	<h3>应用列表</h3>
	<div class="mainbox">
		<?php if(!empty($viewData['data'])) {
				$data = $viewData['data'];
				//print_r($settings);
				//$arr = explode("\n", $settings['ip_band']['v']);
				//print_r($arr);
		?>
		<form method="post" onsubmit="return confirm('该操作不可恢复，您确认要删除这些LINK吗？');" action="?m=link&a=batch_delete">
			<table id='app_data'  class="datalist fixwidth" >
				<tbody>	
					<tr>
						<th>
							<input id="chkall" class="checkbox" type="checkbox"  name="chkall"/>
							<label for="chkall">删除</label>
						</th>
						<th>Link名</th>
						<th>URL</th>
						<th>分类</th>		
						<th>编辑</th>
					</tr>
					<?php foreach($data as $item) {
						echo "<tr>\n";
						echo "<td class='option'><input type='checkbox' class='checkbox' value='{$item['id']}' name='ids[]' />{$item['id']}</td>";
						echo "<td ><a href='{$item['url']}' target='_blank'>{$item['title']}</a></td>";
						echo "<td ><a href='{$item['url']}' target='_blank'>{$item['url']}</a></td>";
						echo "<td >{$item['category']}</td>";						
						echo "<td ><a href='?m=link&a=edit&id={$item['id']}'>修改</a></td>"; 
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
<?php echo script_link('ui.datepicker-zh-CN.js', JS_ROOT . 'jquery/ui/i18n/');?>
<?php echo script_link('common.js', JS_ROOT);?>
<script type='text/javascript' >
	$().ready(function(){
		// Datepicker
		$('#tabs').tabs();
		
		$('#chkall').click(function(){
			var table = $(this).parents('.datalist').eq(0);
			var check = $(this).attr('checked');
			table.find(':checkbox').attr('checked',check);
		});
		tableOver('app_data', 'trhover');
	});
</script>
<?php  view('footer');?>
