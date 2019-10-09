<?php
function articles($articles_func)
{
	switch($articles_func)
	{
	case 'main':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/articles/articles_services/main_articles.php');
		$articles_func = main_articles();
		break;
	case 'add':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/articles/articles_services/add_articles.php');
		$articles_func = add_articles();
		break;
	case 'del':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/articles/articles_services/del_articles.php');
		$articles_func = del_articles();
		break;
	case 'edit':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/articles/articles_services/edit_articles.php');
		$articles_func = edit_articles();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/articles/articles_services/main_articles.php');
		$articles_func = main_articles();
		break;
	}
	
	$page = '
<div id="left_menu">
	<a href="/adminpage/articles"><div class="left_menu_button">Главная</div></a>
	<a href="/adminpage/articles/add"><div class="left_menu_button">Добавить</div></a>
	<a href="/adminpage/articles/del"><div class="left_menu_button">Удалить</div></a>
	<a href="/adminpage/articles/edit"><div class="left_menu_button">Редактировать</div></a>
</div>
<div id="right_page">' 
	. $articles_func . '
</div>';

	return $page;
}
?>