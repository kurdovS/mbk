<?php
function edit_catalogs()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	$query = 'SELECT * FROM `mbk_restoraunts`';
	$res = mysqli_query($db, $query);
	$restoraunts_num = mysqli_num_rows($res);
	
	
	$html = '
	<div id="restoraunts">
		<div class="small_title">Рестораны</div>
		<table id="restoraunts_table">
			<tr class="title_tr">
				<td class="title_td"></td>
				<td class="title_td">Название</td>
				<td class="title_td">Ассортимент</td>
				<td class="title_td">Файл стилей</td>
				<td class="title_td">Число категорий</td>
				<td class="title_td">Число продуктов</td>
			</tr>';
			//для каждого ресторана
			for($i = 0; $i < $restoraunts_num; $i++){
				$restoraunt = mysqli_fetch_assoc($res);
				$html .= '<tr>
					<td class="regular_td">' . $restoraunt['id_restoraunt'] . '</td>
					<td class="regular_td">' . $restoraunt['name'] . '</td>
					<td class="regular_td">' . $restoraunt['assortment_file'] . '</td>
					<td class="regular_td">' . $restoraunt['style_file'] . '</td>
					<td class="regular_td">' . $restoraunt['category_num'] . '</td>
					<td class="regular_td">' . $restoraunt['products_num'] . '</td>
					<td><input type="checkbox" class="rest_check" id="rc' . $restoraunt['id_restoraunt'] . '"></input></td>
				</tr>';
			}
			$html .= '
		</table>
	</div>
	<div id="choosed_restoraunt">
	</div>';
	
	return $html;
}
?>