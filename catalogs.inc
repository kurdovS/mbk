<?php
function catalogs($catalog_func)
{
	switch($catalog_func)
	{
	case 'main':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/catalogs/catalogs_services/main_catalogs.php');
		$catalog_func = main_catalogs();
		break;
	case 'add':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/catalogs/catalogs_services/add_catalogs.php');
		$catalog_func = add_catalogs();
		break;
	case 'del':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/catalogs/catalogs_services/del_catalogs.php');
		$catalog_func = del_catalogs();
		break;
	case 'edit':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/catalogs/catalogs_services/edit_catalogs.php');
		$catalog_func = edit_catalogs();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/catalogs/catalogs_services/main_user.php');
		$catalog_func = main_user();
		break;
	}
	
	$page = '
<div id="left_menu">
	<a href="/adminpage/catalogs"><div class="left_menu_button">Главная</div></a>
	<a href="/adminpage/catalogs/add"><div class="left_menu_button">Добавить</div></a>
	<a href="/adminpage/catalogs/del"><div class="left_menu_button">Удалить</div></a>
	<a href="/adminpage/catalogs/edit"><div class="left_menu_button">Редактировать</div></a>
</div>
<div id="right_page">' 
	. $catalog_func . '
</div>';

	return $page;
}
?>