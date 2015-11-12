<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
//print_r($viewData);
$keys = empty($viewData['keys']) ? array() : $viewData['keys'] ;
?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">	
	<h3 class="marginbot">
		<?php echo empty($viewData['title']) ? "提示" :  $viewData['title'];?>		
	</h3>

	<div class="mainbox" >
		<?php echo empty($viewData['message']) ? '' : $viewData['message'];		?>
		 <a href="#" id="a_go">如果没有跳转，请点这里</a> 
	</div>
</div>

<script type='text/javascript' >
    function go_forward(){
        <?php 
         echo !empty($viewData['forward']) ? "location.href='{$viewData['forward']}'" 
         : "history.back()";
         ?>
    }
	$().ready(function(){
		var t = <?php echo empty($viewData['expri'])? 5000: $viewData['expri']?> ;
		$("#a_go").click(go_forward);
		setTimeout("go_forward();", t);
	});
</script>
<?php  view('footer');?>
