<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
?>
<div class="container">
		
	<h3>系统信息</h3>
	<ul class="memlist fixwidth"> 
		<?php if(!empty($viewData['sys'])) {
			foreach($viewData['sys'] as $item) {
				echo "<li><em>{$item['title']}:</em>{$item['info']}</li>";
			}	
		}			
		?>
	</ul>
	<h3>建议使用firfox和chrome浏览器</h3>
	
</div>

<?php  view('footer');?>