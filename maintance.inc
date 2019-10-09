<?php
function maintance($maintance_func)
{
	switch($maintance_func)
	{
	case 'main':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/maintance/maintance_services/main_maintance.php');
		$maintance_func = main_maintance();
		break;
	case 'add':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/maintance/maintance_services/add_maintance.php');
		$maintance_func = add_maintance();
		break;
	case 'del':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/maintance/maintance_services/del_maintance.php');
		$maintance_func = del_maintance();
		break;
	case 'edit':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/maintance/maintance_services/edit_maintance.php');
		$maintance_func = edit_maintance();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/maintance/maintance_services/main_maintance.php');
		$maintance_func = main_maintance();
		break;
	}
	
	$page = '
<div id="left_menu">
	<a href="/adminpage/maintance"><div class="left_menu_button">Главная</div></a>
	<a href="/adminpage/maintance/add"><div class="left_menu_button">Добавить</div></a>
	<a href="/adminpage/maintance/del"><div class="left_menu_button">Удалить</div></a>
	<a href="/adminpage/maintance/edit"><div class="left_menu_button">Редактировать</div></a>
</div>
<div id="right_page">' 
	. $maintance_func . '
</div>';

	return $page;
}
?>