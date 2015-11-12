/**
 * 常用JS函数库
 * @author d-boy
 * $Id: common.js 90 2010-03-01 08:17:28Z lunzhijun $
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

/**
 * 表格的鼠标滑过效果
 * @param tb : table object
 * @param css : 滑过的效果的CSS类名
 */
function tableOver2(tb, css) {
	$(tb).find('tr').hover(function(){
						$(this).removeClass(css);
						$(this).addClass(css);
						},
						function (){
							$(this).removeClass(css);
						});
	
}