<?php
function class_info($brand, $page)
{
	//01_подключаем все подключаемые файлы для info
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/header/header.inc');
	$head = head($brand);

	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/footer/footer.inc');
	$footer = footer($brand);

	$rest_name_info = 'Макдональдс';
	$burgerking_style = '';
		if($brand == 'burgerking'){
			$burgerking_style = '<link rel="stylesheet" href="/includes/for_all/burgerking_style/burgerking_style.css" />';
			$rest_name_info = 'Бургер Кинг';
		}

	//для страницы "Доставка и оплата"
	$yandex_map = '';
	if($page == 'dostavka-i-oplata'){
		$yandex_map = '
			<script src="https://api-maps.yandex.ru/2.1.65/?lang=ru_RU" type="text/javascript"></script>
			<script src="/includes/for_info/dostavka_i_oplata.js"></script>';
	}

	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_info/info.inc');
	$css_js = '<link rel="stylesheet" href="/includes/for_info/info.css" />';
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//получим всю информацию о транице
	$query = 'SELECT * FROM `info_pages` WHERE title_translit="' . $page . '"';
	$res = mysqli_query($db, $query);
	$info = mysqli_fetch_assoc($res);
	mysqli_close($db);
	$title = '<title>' . $info['title'] . ' | Доставка из ' . $rest_name_info . ' в Рязани | MBK-Delivery</title>';
	$in_body = info($brand, $page);
	
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

	' . $title . '
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta charset="utf-8" />

	<meta name="description" content="' . $info['title'] . ' | Доставка из ' . $rest_name_info . ' в Рязани" />
	<meta name="keywords" content="' . $rest_name_info . ' доставка, ' . $rest_name_info . ' доставка рязань, ' . $rest_name_info . ' доставка на дом, ' . $info['title'] . '" />

	<link rel="apple-touch-icon" sizes="180x180" href="/mbk_favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/mbk_favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/mbk_favicons/favicon-16x16.png">
	<link rel="icon" type="image/png" sizes="192x192" href="/mbk_favicons/android-chrome-192x192.png">
	<link rel="manifest" href="/mbk_favicons/site.webmanifest">
	<link rel="mask-icon" href="/mbk_favicons/safari-pinned-tab.svg" color="#d50017">
	<meta name="msapplication-TileColor" content="#d50017">
	<meta name="theme-color" content="#d50017">
	
	<link rel="stylesheet" href="/includes/for_all/header/header.css" />
	<link rel="stylesheet" href="/includes/for_all/header/cart/cart.css" />
	<script src="/includes/for_all/header/header.js"></script>
	<script src="/includes/for_all/header/cart/cart.js"></script>
	<script src="/includes/for_all/footer/footer.js"></script>
	<link rel="stylesheet" href="/includes/for_all/footer/footer.css" />' .
	$yandex_map .
	$burgerking_style . 
	$css_js . '
</head>
<body>' . $head 
. $in_body . 
$footer . '</body>
</html>';

	return $html_code;
}
?>
