/**
 * 常用JS函数库
 * @author d-boy
 * $Id: common.js 230 2010-12-10 03:22:21Z lunzhijun $
 */

/**
 * 表格的鼠标滑过效果
 * @param tabid : table ID
 * @param css : 滑过的效果的CSS类名
 */
function tableOver(tabid, css) {
	$('#' + tabid).find('tr').hover(function(){
						$(this).removeClass(css);
						$(this).addClass(css);
						},
						function (){
							$(this).removeClass(css);
						});
}