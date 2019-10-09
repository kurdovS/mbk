<?php
function users($user_func)
{
	switch($user_func)
	{
	case 'main':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/users/users_services/main_user.php');
		$user_func = main_user();
		break;
	case 'add':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/users/users_services/add_user.php');
		$user_func = add_user();
		break;
	case 'del':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/users/users_services/del_user.php');
		$user_func = del_user();
		break;
	case 'edit':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/users/users_services/edit_user.php');
		$user_func = edit_user();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/users/users_services/main_user.php');
		$user_func = main_user();
		break;
	}
	
	$page = '
<div id="left_menu">
	<a href="/adminpage/users"><div class="left_menu_button">Главная</div></a>
	<a href="/adminpage/users/add"><div class="left_menu_button">Добавить</div></a>
	<a href="/adminpage/users/del"><div class="left_menu_button">Удалить</div></a>
	<a href="/adminpage/users/edit"><div class="left_menu_button">Редактировать</div></a>
</div>
<div id="right_page">' 
	. $user_func . '
</div>';

	return $page;
}
?>