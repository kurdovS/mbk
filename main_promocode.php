<?php
function main_promocode()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();

	//получим всю таблицу промокодов promocodes
	$promocodes = mysqli_query($db, 'SELECT * FROM `promocodes`');
	$promocodes_num = mysqli_num_rows($promocodes);

	//закроем БД
	mysqli_close($db);

	$html = '
<div class="small_title">Действующие промокоды</div>
<div id="promocodes_tab">
	<div id="tit_empt"></div>
	<div class="tab_tit">Промокод</div>
	<div class="tab_tit">Тип</div>
	<div class="tab_tit">Количество использований</div>
	<div class="tab_tit">Срок действия</div>
	<div class="tab_tit">Скидка</div>
	<div class="empty"></div>';

	//вводим в таблицу информацию о каждом промокоде
	for($i = 0; $i < $promocodes_num; $i++){
		$promocode = mysqli_fetch_assoc($promocodes);
		if(strtotime($promocode['date']) < strtotime(date('Y-m-d')))
			$tab_class = "tab_row_red";
		else
			$tab_class = "tab_row";
		$html .= '
	<div class="tab_index">' . ($i + 1) . '</div>
	<div class="' . $tab_class . '">' . $promocode['promocode'] . '</div>
	<div class="' . $tab_class . '">' . $promocode['type'] . '</div>
	<div class="' . $tab_class . '">' . $promocode['num'] . '</div>
	<div class="' . $tab_class . '">' . $promocode['date'] . '</div>
	<div class="' . $tab_class . '">' . $promocode['discount'] . '</div>
	<div class="empty"></div>';
	}

	$html .= '
</div>';	//promocodes_tab

	return $html;
}
?>
