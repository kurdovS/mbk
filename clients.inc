<?php
function clients($clients_func)
{
	switch($clients_func)
	{
	case 'main':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/clients/clients_services/main_clients.php');
		$clients_func = main_clients();
		break;
	case 'add':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/clients/clients_services/add_clients.php');
		$clients_func = add_clients();
		break;
	case 'edit':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/clients/clients_services/edit_clients.php');
		$clients_func = edit_clients();
		break;
	case 'del':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/clients/clients_services/del_clients.php');
		$clients_func = del_clients();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/clients/clients_services/main_clients.php');
		$clients_func = main_clients();
		break;
	}

	$page = '
<div id="left_menu">
	<a href="/adminpage/users"><div class="left_menu_button">Главная</div></a>
	<a href="/adminpage/users/add"><div class="left_menu_button">Добавить</div></a>
	<a href="/adminpage/users/del"><div class="left_menu_button">Удалить</div></a>
	<a href="/adminpage/users/edit"><div class="left_menu_button">Редактировать</div></a>
	<a href="https://metrika.yandex.ru/dashboard?id=51798311"><div class="left_menu_button">Анализ</div></a>
</div>
<div id="right_page">'
	. $clients_func . '
</div>';

	return $page;
}
?>
