<?php
function trash($trash_func)
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	$query = 'SELECT * FROM orders ORDER BY date_order';
	$res = mysqli_query($db, $query);
	$orders_num = mysqli_num_rows($res);
	
	$page = '
<div id="left_menu">
	<div class="left_menu_button" id="clear_red_orders">Очистить таблицу orders</div>
</div>
<div id="right_page">


	<div class="small_title">Все заказы (красные - заказы, которые нужно удалить)</div>
	<div class="empty"></div>
	<table class="orders">
		<tr class="header_tr">
			<td class="header_td">№ Заказа</td>
			<td class="header_td">id пользователя</td>
			<td class="header_td">Дата заказа</td>
			<td class="header_td">Сумма заказа</td>
		</tr>';
			
	for($i = 0; $i < $orders_num; $i++){
		$orders = mysqli_fetch_assoc($res);
		
		//если дата более 3 дней назад, то заказ красный
	if(((strtotime(date("Y-m-d")) - strtotime($orders['date_order'])) / 86400) > 3)
		$tr_class = 'red_tr';
	else
		$tr_class = 'green_tr';
	
		
		$page .= '<tr class="' . $tr_class . '">
					<td class = "oip_td">' . $orders['id_order'] . '</td>
					<td class = "oip_td">' . $orders['id_user'] . '</td>
					<td class = "oip_td">' . $orders['date_order'] . '</td>
					<td class = "oip_td">' . $orders['sum'] . 'р</td>';
					
					if($tr_class == 'red_tr')
						$page .= '<td class="but_td"><div class="red_del_butt">удалить заказ</div></td>';
					
		$page .= '
				</tr>';
	}
	
	$page .= '</tr>
	</table>
	
	
</div>';

	return $page;
}
?>