<?php
	function class_mainpage($brand){
		//01_подключаем все подключаемые файлы для mainpage
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/header/header.inc');
		$head = head($brand);

		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/footer/footer.inc');
		$footer = footer($brand);

		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_mainpage/change_images/change_images.inc');
		$change_images = change_images($brand);

		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_mainpage/advantages/advantages.inc');
		$advantages = advantages($brand);

		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_mainpage/products_catalog/products_catalog.inc');
		$products_catalog = products_catalog($brand);

		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_mainpage/email/email.inc');
		$email = email($brand);


		$rest_name_to_title = 'Макдоналдс';
		$rname = 'макдональдс';
		$burgerking_style = '';
		if($brand == 'burgerking'){
			$burgerking_style = '<link rel="stylesheet" href="/includes/for_all/burgerking_style/burgerking_style.css" />';
			$rest_name_to_title = 'burger king';
			$rname = 'Бургер Кинг';
		}

		//02_начинаем формировать html-код
		$html_code = '<!DOCTYPE html>
<html lang="ru">
<head>
	<title>Доставка из ' . $rest_name_to_title . ' в Рязани | MBK-Delivery</title>
	<meta name="description" content="Доставка любимых блюд из ' . $rest_name_to_title . ' домой или в офис. Быстрая доставка по всему городу от 30 минут! Заказ от 0р. Промокоды и скидки." />
	<meta name="keywords" content="' . $rname . ' доставка, доставка еды Рязань, бургер доставка, ' . $rname . ' меню, доставка еды, еда на дом, ' . $rname . ' доставка рязань, ' . $rname . ' доставка на дом, доставка ' . $rname . ' на дом рязань, ' . $brand . ' доставка, ' . $brand . ' доставка рязань, ' . $rest_name_to_title . ' доставка, ' . $rest_name_to_title . ' доставка рязань" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta charset="utf-8" />
	<link rel="apple-touch-icon" sizes="180x180" href="/mbk_favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/mbk_favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/mbk_favicons/favicon-16x16.png">
	<link rel="icon" type="image/png" sizes="192x192" href="/mbk_favicons/android-chrome-192x192.png">
	<link rel="manifest" href="/mbk_favicons/site.webmanifest">
	<link rel="mask-icon" href="/mbk_favicons/safari-pinned-tab.svg" color="#d50017">
	<meta name="msapplication-TileColor" content="#d50017">
	<meta name="theme-color" content="#ffffff">

	<link rel="stylesheet" href="/includes/for_all/header/header.css" />
	<link rel="stylesheet" href="/includes/for_all/header/cart/cart.css" />
	<script src="/includes/for_all/header/header.js" async></script>
	<script defer src="/includes/for_all/header/cart/cart.js"></script>
	<link rel="stylesheet" href="/includes/for_mainpage/change_images/change_images.css">
	<script defer src="/includes/for_mainpage/change_images/change_images.js"></script>
	<link rel="stylesheet" href="/includes/for_mainpage/advantages/advantages.css">
	<link rel="stylesheet" href="/includes/for_mainpage/products_catalog/product_block.css">
	<script src="/includes/for_mainpage/products_catalog/products_with_volume.js" async></script>
	<link defer rel="stylesheet" href="/includes/for_mainpage/email/email.css">
	<script src="/includes/for_mainpage/email/email.js" async></script>
	<script defer src="/includes/for_all/footer/footer.js"></script>
	<link rel="stylesheet" href="/includes/for_all/footer/footer.css" />

<!-- Global site tag (gtag.js) - Google Analytics -->
<script defer src="https://www.googletagmanager.com/gtag/js?id=UA-131745647-1"></script>
<script defer>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag(\'js\', new Date());

  gtag(\'config\', \'UA-131745647-1\');
</script>' . $burgerking_style . '
</head>
<body>';

		$html_code .=  $head . '<div id="wrap">' . 
		$change_images . $products_catalog . $email . $advantages . '
	</div>' . $footer . '

<!-- Yandex.Metrika counter -->
<script defer type="text/javascript">
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
</body>
</html>';

		//03_возвращаем код страницы
		return $html_code;
	}
?>
