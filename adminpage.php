<?php
function class_adminpage($brand, $page, $func)
{
	//подключаем файл авторизации
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/authorization/authorization.inc');

	//выбираем нужную страницу
	switch($page)
	{
	case 'users':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/users/users.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/users/users.css"/>
					<script src="/includes/for_adminpage/users/users.js"></script>';
		$page = users($func);
		break;
	case 'catalogs':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/catalogs/catalogs.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/catalogs/catalogs.css"/>
					<script src="/includes/for_adminpage/catalogs/catalogs.js"></script>';
		$page = catalogs($func);
		break;
	case 'articles':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/articles/articles.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/articles/articles.css"/>
					<script src="/includes/for_adminpage/articles/articles.js"></script>';
		$page = articles($func);
		break;
	case 'news':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/news/news.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/news/news.css"/>
					<script src="/includes/for_adminpage/news/news.js"></script>';
		$page = news($func);
		break;
	case 'info':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/info/info.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/info/info.css"/>
					<script src="/includes/for_adminpage/info/info.js"></script>';
		$page = info($func);
		break;
	case 'maintance':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/maintance/maintance.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/maintance/maintance.css"/>
					<script src="/includes/for_adminpage/maintance/maintance.js"></script>';
		$page = maintance($func);
		break;
	case 'orders':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/orders/orders.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/orders/orders.css"/>
					<script src="/includes/for_adminpage/orders/orders.js"></script>';
		$page = orders($func);
		break;
	case 'trash':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/trash/trash.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/trash/trash.css"/>
					<script src="/includes/for_adminpage/trash/trash.js"></script>';
		$page = trash($func);
		break;
	case 'promocodes':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/promocodes/promocodes.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/promocodes/promocodes.css"/>
					<script src="/includes/for_adminpage/promocodes/promocodes.js"></script>';
		$page = promocodes($func);
		break;
	case 'clients':
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/clients/clients.inc');
		$page_css = '<link rel="stylesheet" href="/includes/for_adminpage/clients/clients.css"/>
					<script src="/includes/for_adminpage/clients/clients.js"></script>';
		$page = clients($func);
		break;
	default:
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/users/users.inc');
		$page = users($func);
		break;
	}


	//02_начинаем формировать html-код
	$html_code = '
<!DOCTYPE html>
<html lang="ru">
<head>
	<title>Admin Page - MBK-Delivery</title>
	<link rel="stylesheet" href="/classes/adminpage/adminpage.css"/>
	<script src="/classes/adminpage/adminpage.js"></script>' . 
	$page_css . '
</head>
<body>

	<div id="header">
		<div id="logo">MBK</div>
		<div id="header_swapper">
			<ul id="header_menu">
				<a href="/adminpage/users"><li class="header_menu_li">Пользователи</li></a>
				<a href="/adminpage/catalogs"><li class="header_menu_li">Каталоги</li></a>
				<a href="/adminpage/articles"><li class="header_menu_li">Статьи</li></a>
				<a href="/adminpage/news"><li class="header_menu_li">Новости</li></a>
				<a href="/adminpage/info"><li class="header_menu_li">Инфо-страницы</li></a>
				<a href="/adminpage/maintance"><li class="header_menu_li">Таблички</li></a>
				<a href="/adminpage/orders"><li class="header_menu_li">Заказы</li></a>
				<a href="/adminpage/trash"><li class="header_menu_li">Очистка мусора</li></a>
				<a href="/adminpage/promocodes"><li class="header_menu_li">Промокоды</li></a>
				<a href="/adminpage/clients"><li class="header_menu_li">Клиенты</li></a>
			</ul>
		</div>
		<div id="exit_wrap">
			<div id="exit">EXIT</div>
		</div>
	</div>
	<div class="empty"></div>'
	. $page . '

</body>
</html>';

	//03_возвращаем код страницы
	return $html_code;
}
?>
