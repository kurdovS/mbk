<?php
	function class_order($brand, $stage){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//переменные
		$title;
		$stage_code;
		$css_path;
		$back_to;
		
		//01_подключаем все подключаемые файлы для order
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/header/header.inc');
		$head = head($brand);
		
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/footer/footer.inc');
		$footer = footer($brand);
		
		$burgerking_style = '';
		if($brand == 'burgerking')
			$burgerking_style = '<link rel="stylesheet" href="/includes/for_all/burgerking_style/burgerking_style.css" />';
		
		//подключаем строку состояния оформления заказа
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/basket_navigate/basket_navigate.php');
		$basket_navigate = basket_navigate();
		
		//выбираем страницу какого из шагов оформления заказа хотим вывести
		switch($stage){
			case 'basket':
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/01_basket/basket.inc');
				$stage_code = stage_basket($brand);
				$css_path = $basket_js . '<link rel="stylesheet" href="/includes/for_order/01_basket/basket.css" />';
				$back_to = '<a href="/' . $brand . '">
								<div class="back_in_order">&larr; Вернуться к каталогу</div>
							</a>';
				$title = "Корзина - MBK-Delivery";
				break;
				
			case 'delivery':
				//убедимся что клиент положил хоть что нибудь в корзину
				$res = mysqli_query($db, 'SELECT `sum` FROM `orders` WHERE `id_order`=' . $_COOKIE['cart_id']);
				$res = mysqli_fetch_row($res);
				$res = $res[0];
				//если в корзине клиента пусто, то направим его на страницу basket
				if($res == 0){
					header('Location: /' . $brand . '/order/basket');
					return;
				}
			
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/02_delivery/delivery.inc');
				$stage_code = stage_delivery($brand);
				$css_path = $yandex_map . $delivery_js . '<link rel="stylesheet" href="/includes/for_order/02_delivery/delivery.css" />';
				$back_to = '<a href="/' . $brand . '/order/basket">
								<div class="back_in_order">&larr; Вернуться к корзине</div>
							</a>';
				$title = "Адрес доставки - MBK-Delivery";
				break;
				
			case 'payment':
				//убедимся что клиент ввел адрес доставки
				$res = mysqli_query($db, 'SELECT COUNT(*) FROM `orders_buffer` WHERE `id_order`=' . $_COOKIE['cart_id']);
				$res = mysqli_fetch_row($res);
				$res = $res[0];
				//если клиент не ввел адрес доставки
				if($res == 0){
					header('Location: /' . $brand . '/order/delivery');
					return;
				}
			
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/03_payment/payment.inc');
				$stage_code = stage_payment($brand);
				$css_path = $payment_js . '<link rel="stylesheet" href="/includes/for_order/03_payment/payment.css" />';
				$back_to = '<a href="/' . $brand . '/order/delivery">
								<div class="back_in_order">&larr; Вернуться к вводу адреса доставки</div>
							</a>';
				$title = "Оплата - MBK-Delivery";
				break;
				
			case 'final':
				//убедимся что клиент ввел адрес доставки
				$res = mysqli_query($db, 'SELECT COUNT(*) FROM `orders_buffer` WHERE `id_order`=' . $_COOKIE['cart_id']);
				$res = mysqli_fetch_row($res);
				$res = $res[0];
				//если клиент не ввел адрес доставки
				if($res == 0){
					header('Location: /' . $brand . '/order/delivery');
					return;
				}
			
				require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_order/04_final/final.inc');
				$stage_code = stage_final($brand);
				$css_path = $final_js . '<link rel="stylesheet" href="/includes/for_order/04_final/final.css" />';
				$back_to = '<a href="/' . $brand . '">
								<div class="back_in_order">&larr; Вернуться к каталогу</div>
							</a>';
				$title = "Заказ оформлен - MBK-Delivery";
				break;
		}
		
		mysqli_close($db);
		
		//02_начинаем формировать html-код
		$html_code = '
<!DOCTYPE html>
<html lang="ru">
<head>

	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-131745647-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag(\'js\', new Date());

  gtag(\'config\', \'UA-131745647-1\');
</script>

	<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(51798311, "init", {
        id:51798311,
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:false
   });

</script>
<noscript><div><img src="https://mc.yandex.ru/watch/51798311" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

	<title>' . $title . '</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta charset="utf-8" />
	
	<link rel="apple-touch-icon" sizes="180x180" href="/mbk_favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/mbk_favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/mbk_favicons/favicon-16x16.png">
	<link rel="icon" type="image/png" sizes="192x192" href="/mbk_favicons/android-chrome-192x192.png">
	<link rel="manifest" href="/mbk_favicons/site.webmanifest">
	<link rel="mask-icon" href="/mbk_favicons/safari-pinned-tab.svg" color="#ff0000">
	<meta name="msapplication-TileColor" content="#d50017">
	<meta name="theme-color" content="#d50017">
	
	<link rel="stylesheet" href="/includes/for_all/header/header.css" />
	<link rel="stylesheet" href="/includes/for_all/header/cart/cart.css" />
	<link rel="stylesheet" href="/includes/for_order/basket_navigate/basket_navigate.css" />' .
	$css_path . '
	<link rel="stylesheet" href="/includes/for_all/footer/footer.css" />' .
	$burgerking_style . '
	<script src="/includes/for_all/header/header.js"></script>
	<script src="/includes/for_all/footer/footer.js"></script>
	<script src="/includes/for_all/header/cart/cart.js"></script>
	<script src="/includes/for_order/basket_navigate/basket_navigate.js"></script>
</head>
<body>' . $head . '
	<div id="wrap">' . $basket_navigate . $back_to . $stage_code . '</div>' . $footer . '
</body>
</html>';


		//03_возвращаем код страницы
		return $html_code;
	}
?>
