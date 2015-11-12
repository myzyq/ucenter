<?php defined('U_CENTER') or exit('Access Denied');?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<?php if(isset($viewData['script'])) {
echo $viewData['script'];
//print_r($viewData);
}?>
<script type='text/javascript' >
	function get_result(name,id) {
		var data = {'name':name,'id':id};
		<?php echo $viewData['callback'] . '(data);';?>
	}
</script>
<table class="datalist fixwidth" >
	<tbody>
		<tr>
			<th>选择</th>
			<th>姓名</th>
			<th>出生日期</th>
			<th>部门</th>
		</tr>
		<?php  if(isset($viewData['data'])) {
			
			foreach($viewData['data'] as $item) {
				$real_name = iconv('GBK', 'UTF8', $item['real_name']);
				$dep_name = iconv('GBK', 'UTF8', $item['dep_name']);
				echo "<tr>\n";
				echo "<td><input type='button' value='选择' onclick='get_result(\"{$real_name}\", {$item['id']})'</td>\n";
				echo "<td>{$real_name}</td>\n";
				echo "<td>{$item['birthday']}</td>\n"; 
				echo "<td>{$dep_name}</td>\n"; 
				echo "</tr>\n";
			}
		}?>
		<tr class="nobg">

		<td class="tdpage" colspan="4"> <?php echo empty($viewData['pager']) ? '' : iconv('GBK', 'UTF8', $viewData['pager'])?></td>
		</tr>
	</tbody>
</table>
