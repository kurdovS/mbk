<?php
//выводим таблицу maintance
function add_maintance()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	$maintance = mysqli_query($db, 'SELECT * FROM `maintance`');
	mysqli_close($db);
	$maintance_num = mysqli_num_rows($maintance);
	$maintance = mysqli_fetch_assoc($maintance);
	
	$html_code = '
	<div id="add_maintance">
		<div class="small_title">Остановить работу службы доставки</div>
		<div id="add_maintance_form">
			<div class="tab_tit">mt_header</div>
			<div class="tab_tit">mt_description</div>
			<div class="tab_tit">deadline</div>
			<div class="tab_input" contenteditable="true"></div>
			<div class="tab_input" contenteditable="true"></div>
			<div class="tab_input" contenteditable="true"></div>
		</div>
		<div id="add_maintance_button">Вставить</div>
	</div>';
			
	return $html_code;
}
?>