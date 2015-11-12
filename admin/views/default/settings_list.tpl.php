<?php defined('U_CENTER') or exit('Access Denied');?>
<?php  view('header');
$keys = empty($viewData['keys']) ? array() : $viewData['keys'] ;
?>
<?php echo css_link('ui.css', THEME_RESOURCE);?>
<div class="container">
    <?php if(!empty($viewData['msg'])){
        //echo $viewData['rs']['flag'];
        $class = $viewData['msg']['flag'] == true  ? 'correctmsg' : 'errormsg';
        echo "<div class='{$class}'>
                <p>{$viewData['msg']['msg']}</p>
             </div><br />";
    }?>
    
    <h3 class="marginbot">
        管理配置
        <a class="sgbtn" href="enter.php?m=settings&a=add">添加新配置</a>
    </h3>
    <div id="tabs" >
        <ul>
            <li><a href="#tabs-1">搜索</a></li>
        </ul>
        <div id="tabs-1">
            <form method="get" action="?">
                <input type="hidden" name="page" value="1" />
                <input type="hidden" name="m" value="settings" />
                <input type="hidden" name="a" value="list" />
                <table >
                    <tbody>
                        <tr>
                            <td>配置名:</td>
                            <td>
                                <input type='text' name='keys[k]' value='<?php echo empty($keys['k']) ? '' : $keys['k'];?>' />
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
    <h3>配置列表</h3>
    <div class="mainbox">
        <?php if(isset($viewData['data'])) {
                $data = $viewData['data'];
                //print_r($settings);
                //$arr = explode("\n", $settings['ip_band']['v']);
                //print_r($arr);
        ?>
        <form method="post" onsubmit="return confirm('该操作不可恢复，您确认要删除这些配置吗？');" action="?m=settings&a=batch_delete">
            <table id='app_data'  class="datalist fixwidth" >
                <tbody> 
                    <tr>
                        <th>
                            <input id="chkall" class="checkbox" type="checkbox"  name="chkall"/>
                            <label for="chkall">删除</label>
                        </th>
                        <th>配置名</th>
                        <th>配置说明</th>
                        <th>配置信息</th>                                          
                        <th>编辑</th>
                    </tr>
                    <?php foreach($data as $item) {
                        echo "<tr>\n";
                        echo "<td class='option'><input type='checkbox' class='checkbox' value='{$item['k']}' name='ids[]' /></td>";
                        echo "<td ><a href='?m=settings&a=edit&id={$item['k']}'>{$item['k']}</a></td>";
                        echo "<td >{$item['memo']}</td>";
                        echo "<td >{$item['v']}</td>";                      
                        echo "<td ><a href='?m=settings&a=edit&id={$item['k']}'>查看</a> | <a href='?m=settings&a=edit&id={$item['k']}'>修改</a></td>"; 
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
