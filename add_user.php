<?php
function add_user()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	$current_user_query = 'SELECT * FROM adminpage_system_accounts WHERE name="' 
		. $_SERVER['PHP_AUTH_USER'] . '"';
	$current_user = mysqli_query($db, $current_user_query);
	$current_user = mysqli_fetch_assoc($current_user);
	
	$html = '
<div id="current">
	<div class="small_title">Информация о пользователе</div>
	<div class="current_text">Вы вошли в систему как:<div class="ct_value">' . $current_user['name'] . '</div></div>
	<div class="current_text">Статус в системе:<div class="ct_value">' . $current_user['status'] . '</div></div>
	<div class="current_text">Дата регистрации:<div class="ct_value">' . $current_user['date'] . '</div></div>
</div>
<div id="add_user">
	<div class="small_title">Добавление пользователя</div>
	<div id="add_user_form">
		<div class="tab_tit">Имя</div>
		<div class="tab_tit">Статус</div>
		<div class="tab_tit">Пароль</div>
		<input class="tab_input"></input>
		<input class="tab_input"></input>
		<input class="tab_input"></input>
	</div>
	<div id="add_user_button">Добавить</div>
	
</div>';

	return $html;
}
?>