<?php defined('U_CENTER') or exit('Access Denied');?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<?php
	$callback = $viewData['callback'];
?>
<script type='text/javascript' >
	function get_result(name,id) {
		var data = {'name':name,'id':id};
		<?php echo $callback . '(data);';?>
	}
</script>
<table class="datalist fixwidth" >
	<tbody>
		<tr>
			<th>选择</th>
			<th>部门名</th>
			<th>上层部门</th>
			<th>负责人</th>
			
		</tr>
		<?php  if(isset($viewData['data'])) {
			
			foreach($viewData['data'] as $item) {
				$name = iconv('GBK','UTF8',$item['name']);
				$parent_name = iconv('GBK', 'UTF8', $item['parent_name']);
				$director_name = iconv('GBK', 'UTF8', $item['director_name']);
				echo "<tr>\n";
				echo "<td><input type='button' value='选择' onclick='get_result(\"{$name}\", {$item['id']});' /></td>\n";
				echo "<td>{$name}</td>\n";
				echo "<td>{$parent_name}</td>\n"; 
				echo "<td>{$director_name}</td>\n"; 
				 
				echo "</tr>\n";
			}
		}?>
		<tr class="nobg">
		<td class="tdpage" colspan="4"> </td>
		</tr>
	</tbody>
</table>
