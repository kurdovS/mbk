<?php
function del_user()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	$current_user_query = 'SELECT * FROM adminpage_system_accounts WHERE name="' 
		. $_SERVER['PHP_AUTH_USER'] . '"';
	$current_user = mysqli_query($db, $current_user_query);
	$current_user = mysqli_fetch_assoc($current_user);
	
	$users_query = 'SELECT * FROM adminpage_system_accounts';
	$users = mysqli_query($db, $users_query);
	$users_num = mysqli_num_rows($users);
	
	$html = '
<div id="current">
	<div class="small_title">Информация о пользователе</div>
	<div class="current_text">Вы вошли в систему как:<div class="ct_value">' . $current_user['name'] . '</div></div>
	<div class="current_text">Статус в системе:<div class="ct_value">' . $current_user['status'] . '</div></div>
	<div class="current_text">Дата регистрации:<div class="ct_value">' . $current_user['date'] . '</div></div>
</div>
<div id="manage">
	<div class="small_title">Удаление учетных записей</div>
	<table class="page_table">
		<tr class="table_title">
			<td class="first_td"></td>
			<td class="td">Имя</td>
			<td class="td">Статус в системе</td>
			<td class="td">Пароль</td>
			<td class="td">Дата регистрации</td>
		</tr>';
			
		//вводим в таблицу информацию о каждом пользователе
		for($i = 0; $i < $users_num; $i++){
			$user = mysqli_fetch_assoc($users);
			$html .= '
		<tr>
			<td class="first_td">' . $user['id_account'] . '</td>
			<td class="td">' . $user['name'] . '</td>
			<td class="td">' . $user['status'] . '</td>
			<td class="td">' . $user['password'] . '</td>
			<td class="td">' . $user['date'] . '</td>
			<td class="del_user_butt">x</td>
		</tr>';
		}
			
	$html .= '
	</table>
</div>';

	return $html;
}
?>