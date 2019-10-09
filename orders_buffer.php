<?php
function orders_buffer()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();

	$query = 'SELECT * FROM orders_buffer ORDER BY id DESC';
	$res = mysqli_query($db, $query);
	$orders_num = mysqli_num_rows($res);

	$html = '
	<div class="small_title">Неоформленные заказы</div>
	<div class="empty"></div>
	<table class="orders_in_process">
		<tr class="header_tr">
			<td class="header_td">№ Заказа</td>
			<td class="header_td">Имя клиента</td>
			<td class="header_td">Телефон</td>
			<td class="header_td">Не звонить</td>
			<td class="header_td">Когда доставить</td>
			<td class="header_td">Адрес</td>
			<td class="header_td">Способ оплаты</td>
			<td class="header_td">Сумма заказа</td>
			<td class="header_td">Без сдачи</td>
			<td class="header_td">Сдача с</td>
			<td class="header_td">Дата заказа</td>
			<td class="header_td">Оплачен</td>';

	for($i = 0; $i < $orders_num; $i++){
		$orders = mysqli_fetch_assoc($res);

		if($orders['dont_call'])
			$dc = 'Да';
		else
			$dc = 'Нет';

		if($orders['cash_or_card'] == 0)
			$cc = 'онлайн';
		else
			$cc = 'кэш';

		if($orders['with_change'])
			$wc = 'со сдачей';
		else
			$wc = 'без сдачи';

		if($orders['paid'])
			$pp = 'Да';
		else
			$pp = 'Нет';

		$html .= '<tr>
					<td class = "oip_td">' . $orders['id_order'] . '</td>
					<td class = "oip_td">' . $orders['client_name'] . '</td>
					<td class = "oip_td">' . $orders['phone_number'] . '</td>
					<td class = "oip_td">' . $dc . '</td>
					<td class = "oip_td">' . $orders['delivery_time'] . '</td>
					<td class = "oip_td">' . $orders['delivery_address'] . '</td>
					<td class = "oip_td">' . $cc . '</td>
					<td class = "oip_td">' . $orders['order_sum'] . ',00P</td>
					<td class = "oip_td">' . $wc . '</td>
					<td class = "oip_td">' . $orders['change_from'] . '</td>
					<td class = "oip_td">' . $orders['order_date'] . '</td>
					<td class = "oip_td">' . $pp . '</td>
					<td><div class="buffer_del_buttons">Удалить</div></td>
				</tr>';
	}

	$html .= '</tr>
	</table>';

	return $html;
}
?>
