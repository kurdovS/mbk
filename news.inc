<?php
function news($news_func)
{
	switch($news_func)
	{
	case 'main':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/news/news_services/main_news.php');
		$news_func = main_news();
		break;
	case 'add':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/news/news_services/add_news.php');
		$news_func = add_news();
		break;
	case 'del':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/news/news_services/del_news.php');
		$news_func = del_news();
		break;
	case 'edit':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/news/news_services/edit_news.php');
		$news_func = edit_news();
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/news/news_services/main_news.php');
		$news_func = main_news();
		break;
	}
	
	$page = '
<div id="left_menu">
	<a href="/adminpage/news"><div class="left_menu_button">Главная</div></a>
	<a href="/adminpage/news/add"><div class="left_menu_button">Добавить</div></a>
	<a href="/adminpage/news/del"><div class="left_menu_button">Удалить</div></a>
	<a href="/adminpage/news/edit"><div class="left_menu_button">Редактировать</div></a>
</div>
<div id="right_page">' 
	. $news_func . '
</div>';

	return $page;
}
?>