<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');?>
<div class="container">
	<?php if(!empty($viewData['data'])) {
            $links =     $viewData['data'];    
            foreach ($links as $cat=>$data){
            
        ?>     
     <h3><?php echo $cat;?></h3> 
	<div class="mainbox">		
		<table  class="datalist fixwidth" >
				<tbody>	
					<tr>						
						<th>LINK名</th>
						<th>地址</th>
						<th>进入</th>						
						
					</tr>
					<?php foreach($data as $item) {
						echo "<tr>\n";			
						echo "<td ><a href='{$item['url']}' target='_blank'>{$item['title']}</a></td>";
						echo "<td><a href='{$item['url']}' target='_blank'>{$item['url']}</a></td>"		;				
						echo "<td ><a href='{$item['url']}' target='_blank'>点击进入</a></td>"; 
						echo "</tr>\n";
		              }?>
					<tr class="nobg">
						<td>&nbsp;</td>
						<td class="tdpage" colspan="8"></td>
					</tr>
				</tbody>
			</table>

	</div>
	<?php }
            }?>
</div>

<div id='dialog'  style='display:none;' ></div>
<?php echo script_link('ui.datepicker-zh-CN.js', JS_ROOT . 'jquery/ui/i18n/');?>
<?php echo script_link('common.js', JS_ROOT);?>
<script type='text/javascript' >
	$().ready(function(){
		// Datepicker
		$(".datalist").each(function(){
			var obj = this;
			tableOver2(obj, 'trhover');
		});
	});
</script>
<?php  view('footer');?>
