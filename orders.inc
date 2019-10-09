<?php
function orders($orders_func)
{
	switch($orders_func)
	{
	case 'orders_in_process':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/orders/orders_types/orders_in_process.php');
		$orders_func = orders_in_process();
		break;
	case 'orders_done':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/orders/orders_types/orders_done.php');
		$orders_func = orders_done();
		break;
	case 'orders_buffer':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/orders/orders_types/orders_buffer.php');
		$orders_func = orders_buffer();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/orders/orders_types/orders_in_process.php');
		$orders_func = orders_in_process();
		break;
	}	

	$page = '
<div id="left_menu">
	<a href="/adminpage/orders/orders_in_process"><div class="left_menu_button">Текущие</div></a>
	<a href="/adminpage/orders/orders_done"><div class="left_menu_button">Завершенные</div></a>
	<a href="/adminpage/orders/orders_buffer"><div class="left_menu_button">Неоформленные</div></a>
</div>
<div id="right_page">'
	. $orders_func . '
</div>';	

	return $page;
}
?>
