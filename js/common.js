/**
 * ����JS������
 * @author d-boy
 * $Id: common.js 90 2010-03-01 08:17:28Z lunzhijun $
 */

/**
 * ������껬��Ч��
 * @param tabid : table ID
 * @param css : ������Ч����CSS����
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
 * ������껬��Ч��
 * @param tb : table object
 * @param css : ������Ч����CSS����
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