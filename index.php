<?php
	//включим вывод ошибок
	//ini_set('error_reporting', E_ALL);
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);

	$brand = 'mcdonalds';	//какой ресторан
	$module = '';			//модуль - index по умолч.
	$f_param = '';			//первый параметр

	$articles_num_on_page = 9;		//число статей на странице со списком статей
	$news_num_on_page = 9;			//число новостей на странице со списком новостей

	//узнаем работает ли служба доставки
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	$res = mysqli_query($db, 'SELECT COUNT(*) FROM maintance');
	$res = mysqli_fetch_row($res);
	$maintance_num = $res[0];

	//переменная, показывает доступен ли сайт false - сайт доступен | true - сайт не доступен
	$maintance_mode = false;
	if($maintance_num > 0)
		$maintance_mode = true;

	//АНАЛИЗИРУЕМ АДРЕС ЗАПРОСА
	if($_SERVER['REQUEST_URI'] != '/'){
		try {
			//разбиваем строку с адресом на составляющие через слэш и сохраним в массиве $url
			$url = explode('/', $_SERVER['REQUEST_URI']);

			//есть только $brand
			if(count($url) == 2){
				$brand = $url[1];
			}
			//есть $brand и $module
			else if(count($url) == 3){
				$brand = $url[1];
				$module = $url[2];
			}
			//есть $brand, $module и $f_param
			else if(count($url) == 4){
				$brand = $url[1];
				$module = $url[2];
				$f_param = $url[3];
			}
			//если больше, то выбрасываем исключение
			else 
				throw new Exception();
		}
		catch(Exception $exp){
			$module = '404';
		}
	}


	//выясним число продуктов в каталоге для данного ресторана
	if($brand == 'mcdonalds' || $brand == 'burgerking'){			//ДОБАВИТЬ ЗАТЕМ KFC
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		$res = mysqli_query($db, 'SELECT COUNT(*) FROM ' . $brand . '_items LIMIT 0, 300');
		$res = mysqli_fetch_row($res);
		$products_num = $res[0];

		//выясним число страниц со статьями для модуля articles
		if($module == 'articles'){
			$res = mysqli_query($db, 'SELECT * FROM `published_articles`');
			$pages_num = mysqli_num_rows($res);
			$pages_num = $pages_num / $news_num_on_page + 1;			//число страниц со статьями
		}
		else if($module == 'news'){
			$res = mysqli_query($db, 'SELECT * FROM `published_news`');
			$news_pages_num = mysqli_num_rows($res);
			$news_pages_num = $news_pages_num / $news_num_on_page + 1;			//число страниц с новостями
		}

		mysqli_close($db);
	}


	//ОПРЕДЕЛЯЕМ СТРАНИЦУ ДЛЯ ВЫВОДА КЛИЕНТУ
	switch($brand)
	{
	//$brand = 'viber';
//	case 'viber':
//		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/viber.inc');
//		break;
	//$brand = 'adminpage';
	case 'adminpage':
		//switch по $module
		switch($module)
		{
		//$brand = 'adminpage'; $module = '';
		case '':
			require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/adminpage/adminpage.php');
			echo class_adminpage($brand, 'users', 'main');
			break;
		//$brand = 'adminpage'; $module = 'users:info';
		case 'users':
		case 'catalogs':
		case 'articles':
		case 'news':
		case 'info':
		case 'maintance':
		case 'orders':
		case 'trash':
		case 'promocodes':
		case 'clients':
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'adminpage'; $module='users:orders'; $f_param='';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/adminpage/adminpage.php');
				echo class_adminpage($brand, $module, 'main');
				break;
			//$brand = 'adminpage'; $module='users:info'; $f_param='main:edit';
			case 'main':
			case 'add':
			case 'del':
			case 'edit':
				if($module == 'orders' || $module == 'trash'){
					$module = '404';
					break;
				}
				require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/adminpage/adminpage.php');
				echo class_adminpage($brand, $module, $f_param);
				break;
			case 'orders_in_process':
			case 'orders_done':
			case 'orders_buffer':
				if($module != 'orders'){
					$module = '404';
					break;
				}
				require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/adminpage/adminpage.php');
				echo class_adminpage($brand, $module, $f_param);
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'adminpage_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
		
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'adminpage_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/adminpage/adminpage_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'users_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
		
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'users_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/users/users_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'catalogs_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'catalogs_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/catalogs/catalogs_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'articles_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'articles_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/articles/articles_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'news_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'news_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/news/news_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'info_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'info_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/info/info_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'orders_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'info_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/orders/orders_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'trash_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'trash_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/trash/trash_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'maintance_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'trash_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/maintance/maintance_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка JS запросов
		case 'promocodes_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'trash_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_adminpage/promocodes/promocodes_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		default:
			$module = '404';
			break;
		}
		break;
	//$brand = mcdonalds:kfc;
	case 'mcdonalds':
	case 'burgerking':
	//сайт не заблокирован или запрашивается инфо-страница или блог
	if($maintance_mode == false || $module == 'news' || $module == 'info' || $module == 'articles' || $module == 'cart'){
	//case 'kfc':								ВКЛЮЧИТЬ КОГДА ДОБАВИТСЯ KFC

		//отдаем поисковикам зоголовки last modified
		$lastModified_unix = 1294844676; //время последнего изменения страницы
		$lastModified = gmdate("D, d M Y H:i:s \G\M\T", lastModified_unix);
		$ifModifiedSince = false;

		if(isset($_ENV['HTTP_IF_MODIFIED_SINCE']))
			$ifModifiedSince = strtotime(substr($_ENV['HTTP_IF_MODIFIED_SINCE'], 5));
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
			$ifModifiedSince = strtotime(substr($_SERVER['HTTP_IF_MODIFIED_SINCE'], 5));
		if($ifModifiedSince && ($ifModifiedSince >= $lastModified_unix) && !isset($_COOKIE['cart_id'])){
			header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
			exit();
		}
		header('Last-Modified: ' . $lastModified);

		//switch по $module
		switch($module)
		{
		//$brand = mcdonalds:kfc; $module = '';
		case '':
			require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/mainpage/mainpage.php');
			echo class_mainpage($brand);
			break;
		//$brand = mcdonalds:kfc; $module = 'product';
		case 'product':
			//проверяем что такой продукт существует
			$db = connectionDB();
			$query = 'SELECT * FROM ' . $brand . '_items WHERE name_translit="' . $f_param . '"';
			$product_all_rows = mysqli_query($db, $query);
			$is_excite = mysqli_num_rows($product_all_rows);

			//запретим страницы с большим объемом, чем самый маленький
			if($is_excite){
				$product_all_rows = mysqli_fetch_assoc($product_all_rows);
				if($product_all_rows['volume'] != 0){
					//узнаем volume у предыдущего продукта
					$prev_prodAllRows = mysqli_query($db, 'SELECT * FROM ' . $brand . '_items WHERE id_item=' . ($product_all_rows['id_item'] - 1));
					$prev_prodAllRows = mysqli_fetch_assoc($prev_prodAllRows);
					if($prev_prodAllRows['volume'] < $product_all_rows['volume']){
						require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/product/product.php');
						echo class_product($f_param, $brand);
					}
					else
						$module = '404';
				}
				else {
					require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/product/product.php');
					echo class_product($f_param, $brand);
				}
			}
			else
				$module = '404';

			mysqli_close($db);
			break;
		//$brand = mcdonalds:kfc; $module = 'order';
		case 'order':
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'order'; $f_param = 'basket:final';
			case 'basket':
			case 'delivery':
			case 'payment':
			case 'final':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/order/order.php');
				echo class_order($brand, $f_param);
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//$brand = mcdonalds:kfc; $module = 'articles';
		case 'articles':
			if($f_param > 0 && $f_param <= $pages_num){
				require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/articles/articles.php');
				echo class_articles($brand, $f_param);
			}
			//$f_param == название статьи
			else
			{
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
				$article_translit = translit_encode(mb_strtolower($f_param));
				//подключим БД
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
				$db = connectionDB();
				$query = 'SELECT COUNT(*) FROM `published_articles` WHERE `title_translit`="' . $article_translit . '"';
				$is_excite = mysqli_query($db, $query);
				mysqli_close($db);
				$is_excite = mysqli_fetch_row($is_excite);
				$is_excite = $is_excite[0];		//1-страница существует		0-не существует
				if($is_excite == 1){
					require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/articles/articles.php');
					echo class_articles($brand, $f_param);
				}
				else
					$module = '404';
			}
			break;
		//$brand = mcdonalds:kfc; $module = 'news';
		case 'news':
			if($f_param > 0 && $f_param <= $news_pages_num){
				require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/news/news.php');
				echo class_news($brand, $f_param);
			}
			//$f_param == название новости
			else
			{
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
				$new_translit = translit_encode(mb_strtolower($f_param));
				//подключим БД
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
				$db = connectionDB();
				$query = 'SELECT COUNT(*) FROM `published_news` WHERE `title_translit`="' . $new_translit . '"';
				$is_excite = mysqli_query($db, $query);
				mysqli_close($db);
				$is_excite = mysqli_fetch_row($is_excite);
				$is_excite = $is_excite[0];		//1-страница существует		0-не существует
				if($is_excite == 1){
					require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/news/news.php');
					echo class_news($brand, $f_param);
				}
				else
					$module = '404';
			}
			break;
		//$brand = mcdonalds:kfc; $module = 'info';
		case 'info':
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
			$info_translit = translit_encode(mb_strtolower($f_param));
			//подключим БД
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
			$db = connectionDB();
			$query = 'SELECT COUNT(*) FROM `info_pages` WHERE `title_translit`="' . $info_translit . '"';
			$is_excite = mysqli_query($db, $query);
			mysqli_close($db);
			$is_excite = mysqli_fetch_row($is_excite);
			$is_excite = $is_excite[0];		//1-страница существует		0-не существует
			if($is_excite == 1){
				require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/info/info.php');
				echo class_info($brand, $f_param);
			}
			else
				$module = '404';
			break;
		//обработка сервисных запросов из JS
		case 'cart':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'cart'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/header/cart/cart.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка сервисных запросов из JS
		case 'basket_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}

			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'basket_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/01_basket/basket_form_handler/basket_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка сервисных запросов из JS
		case 'delivery_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'delivery_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/02_delivery/delivery_form_handler/delivery_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//обработка сервисных запросов из JS
		case 'payment_form_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
		
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'payment_form_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/03_payment/payment_form_handler/payment_form_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		//РОБОКАССА ПРИСЛАЛА ИНФОРМАЦИЮ О ПЛАТЕЖЕ
		case 'payment_result':
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/03_payment/payment_result/payment_result.php');
			break;
		//обработка сервисных запросов из JS
		case 'email_include_handler':
			//проверка что это ajax запрос
			if(!isset($_POST['ajax_pass']) || $_POST['ajax_pass'] != '688b76242871b8b69ec2175f65eb8c43'){
				$module = '404';
				break;
			}
			
			//switch по $f_param
			switch($f_param)
			{
			//$brand = 'mcdonalds:kfc'; $module = 'email_include_handler'; $f_param = '';
			case '':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_mainpage/email/email_include_handler.php');
				break;
			default:
				$module = '404';
				break;
			}
			break;
		default:
			$module = '404';
			break;
		}
		break;
	//если включен режим технических работ
	} else {
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Location: /maintance');
	}
//	case '404':
//		require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/404/404.php');
//		break;
	//запрошен режим "технические работы"
	case 'maintance':
		//если страница maintance запрошена, но служба доставки работает
		if($maintance_mode == false)
			require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/404/404.php');
		else
			require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/maintance/maintance.php');
		break;
	default:
		$module = '404';
		break;
	}
	
	//запрошена не существующая страница
	if($module == '404'){
		/*$module = '404';
		require_once('$_SERVER['DOCUMENT_ROOT'] . /classes/fourhundredfour.php');
		new fourhundredfour($brand);*/
		header('HTTP/1.x 404 Not Found');
		require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/404/404.php');
		exit();
	//	header('Location: /404');
	}
	
	/*
	//ОПРЕДЕЛЯЕМ СТРАНИЦУ ДЛЯ ВЫВОДА КЛИЕНТУ
	switch($module){
		case 'index':
			require_once('/classes/mainpage/mainpage.php');
			echo class_mainpage($brand);
			break;
		case 'product':
			require_once('/classes/product/product.php');
			echo class_product($id_item, $brand);
			break;
		case 'order':
			require_once('/classes/order/order.php');
			echo class_order($brand, $stage);
			break;
		case 'articles':
			require_once('/classes/articles/articles.php');
			echo class_articles($brand);
			break;
		//в админку
		case 'adminpage':
			require_once('/classes/adminpage/adminpage.php');
			echo class_adminpage($brand, 'users', 'add');
			break;
		//обработка сервисных запросов из JS
		case 'cart':
			require_once('/includes/for_all/header/cart/cart.php');
			break;
		case 'delivery_form_handler':
			require_once('/includes/for_order/02_delivery/delivery_form_handler/delivery_form_handler.php');
			break;
		case 'payment_form_handler':
			require_once('/includes/for_order/03_payment/payment_form_handler/payment_form_handler.php');
			break;
		case 'email_include_handler':
			require_once('/includes/for_mainpage/email/email_include_handler.php');
			break;
		case 'users_form_handler':
			require_once('/includes/for_adminpage/users/users_form_handler.php');
			break;
		//обработка результата от сервиса ROBOKASSA resultURL
		case 'payment_result':
			require_once('/includes/for_order/03_payment/payment_result/payment_result.php');
			break;
		default:
			$module = '404';
			require_once('/classes/fourhundredfour.php');
			new fourhundredfour($brand);
			break;
	}*/
	
?>
