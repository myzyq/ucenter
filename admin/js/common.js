/**
 * ����JS������
 * @author d-boy
 * $Id: common.js 230 2010-12-10 03:22:21Z lunzhijun $
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