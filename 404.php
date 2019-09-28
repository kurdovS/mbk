<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/header/header.inc');
	$head = head('mcdonalds');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/footer/footer.inc');
	$footer = footer('mcdonalds');

	$html_code = '<!DOCTYPE html>
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
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/51798311" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

	<title>Страница не найдена</title>
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
	<script src="/includes/for_all/header/header.js"></script>
	<script src="/includes/for_all/header/cart/cart.js"></script>
	<link rel="stylesheet" href="/classes/404/404.css" />
	<link rel="stylesheet" href="/includes/for_all/footer/footer.css" />
	<script src="/includes/for_all/footer/footer.js"></script>
</head>
<body>' . $head . '
	<div id="wrap">
		<h1 class="not_found">Упс! Страница не найдена</h1>
		<h2 class="nf">Вероятно такой бургер еще не придумали :(</h2>
		<div class="ch_r">
			<div class="mcb">
				<div class="bot">
					<img src="/products/mcdonalds/sandwiches/big_mac.jpg" alt="Биг Мак" />
					<a href="/mcdonalds#sandwiches"><button class="resb">Я за Биг Мак!</button></a>
				</div>
			</div>
			<div class="bkb">
				<div class="bot">
					<img src=" /products/burgerking/beef_burgers/Washper.png" alt="Воппер" />
					<a href="/burgerking#beef_burgers"><button class="resb">Я за Воппер!</button></a>
				</div>
			</div>
		</div>
	</div>' . $footer . '
</body>
</html>';

	echo $html_code;
	//header('HTTP/1.0 404 Not Found');
?>
