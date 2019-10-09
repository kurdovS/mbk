<?php
function main_clients()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();

	$res = mysqli_query($db, 'SELECT * FROM clients');
	$clients_num = mysqli_num_rows($res);

	$html = '
	<div class="small_title">Клиенты</div>
	<div class="empty"></div>
	<table class="clients_table">
		<tr class="header_tr">
			<td class="header_td">Имя клиента</td>
			<td class="header_td">Телефон</td>
			<td class="header_td">Адрес</td>
			<td class="header_td">Номер последнего заказа</td>
		</tr>';

	for($i = 0; $i < $clients_num; $i++){
		$clients = mysqli_fetch_assoc($res);
		$html .= '<tr>
				<td class="client_td">' . $clients['name'] . '</td>
				<td class="client_td">' . $clients['phone'] . '</td>
				<td class="client_td">' . $clients['adress'] . '</td>
				<td class="client_td">' . $clients['id_order'] . '</td>
				<td class="clients_butt_td"><div class="cl_butt">История заказов</div></td>
			</tr>';
	}
	$html .= '</table>';

	return $html;
}
?>
