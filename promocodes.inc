<?php
function promocodes($promocode_func)
{
	switch($promocode_func)
	{
	case 'main':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/promocodes/promocodes_services/main_promocode.php');
		$promocode_func = main_promocode();
		break;
	case 'add':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/promocodes/promocodes_services/add_promocode.php');
		$promocode_func = add_promocode();
		break;
	case 'del':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/promocodes/promocodes_services/del_promocode.php');
		$promocode_func = del_promocode();
		break;
	case 'edit':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/promocodes/promocodes_services/edit_promocode.php');
		$promocode_func = edit_promocode();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/promocodes/promocodes_services/main_promocode.php');
		$promocode_func = main_promocode();
		break;
	}
	
	$page = '
<div id="left_menu">
	<a href="/adminpage/promocodes"><div class="left_menu_button">Главная</div></a>
	<a href="/adminpage/promocodes/add"><div class="left_menu_button">Добавить</div></a>
	<a href="/adminpage/promocodes/del"><div class="left_menu_button">Удалить</div></a>
	<a href="/adminpage/promocodes/edit"><div class="left_menu_button">Редактировать</div></a>
</div>
<div id="right_page">' 
	. $promocode_func . '
</div>';

	return $page;
}
?>
