<?php
function add_promocode()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();


	$html = 
'<div class="small_title">Добавление промокода</div>
<div id="add_promocode_form">
	<div class="tab_tit">Промокод</div>
	<div class="tab_tit">Тип</div>
	<div class="tab_tit">Количество использований</div>
	<div class="tab_tit">Срок действия</div>
	<div class="tab_tit">Скидка</div>
	<input class="tab_input"></input>
	<input class="tab_input"></input>
	<input class="tab_input"></input>
	<input class="tab_input"></input>
	<input class="tab_input"></input>
	<div id="add_button">Добавить</div>
</div>';

	return $html;
}
?>
