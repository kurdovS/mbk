<?php
//выводим таблицу maintance
function main_maintance()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//шаблон по заменам для текста для вывода в список статей поле текст вместе с тегами
	$search = array("<", ">");
	$replace = array("&lt", "&gt");
	
	$maintance = mysqli_query($db, 'SELECT * FROM `maintance`');
	mysqli_close($db);
	$maintance_num = mysqli_num_rows($maintance);
	$maintance = mysqli_fetch_assoc($maintance);
	
	$html_code = '
	<div class="small_title">Причина по которой не работает служба доставки</div>
		<table class="page_table">
			<tr class="table_title">
				<td class="first_td"></td>
				<td class="td">mt_header</td>
				<td class="td">mt_description</td>
				<td class="td">deadline</td>
			</tr>
			<tr>
				<td class="first_td">' . $maintance['id_maintance'] . '</td>
				<td class="td">' . $maintance['mt_header'] . '</td>
				<td class="td">' . str_replace($search, $replace, $maintance['mt_description']) . '</td>
				<td class="td">' . $maintance['deadline'] . '</td>
			</tr>
		</table>
	</div>';
			
	return $html_code;
}
?>