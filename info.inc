<?php
function info($info_func)
{
	switch($info_func)
	{
	case 'main':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/info/info_services/main_info.php');
		$info_func = main_info();
		break;
	case 'add':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/info/info_services/add_info.php');
		$info_func = add_info();
		break;
	case 'del':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/info/info_services/del_info.php');
		$info_func = del_info();
		break;
	case 'edit':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/info/info_services/edit_info.php');
		$info_func = edit_info();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/info/info_services/main_info.php');
		$info_func = main_info();
		break;
	}
	
	$page = '
<div id="left_menu">
	<a href="/adminpage/info"><div class="left_menu_button">Главная</div></a>
	<a href="/adminpage/info/add"><div class="left_menu_button">Добавить</div></a>
	<a href="/adminpage/info/del"><div class="left_menu_button">Удалить</div></a>
	<a href="/adminpage/info/edit"><div class="left_menu_button">Редактировать</div></a>
</div>
<div id="right_page">' 
	. $info_func . '
</div>';

	return $page;
}
?>