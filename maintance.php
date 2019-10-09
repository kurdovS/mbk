<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/header/header.inc');
	$head = head('mcdonalds');
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/footer/footer.inc');
	$footer = footer('mcdonalds');

	//получим из БД информацию о причине по которой не работает служба доставки
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	$maintance = mysqli_query($db, 'SELECT * FROM `maintance`');
	$maintance = mysqli_fetch_assoc($maintance);
	mysqli_close($db);
	//количество дней, часов, минут, секунд
	$all_sec = (strtotime($maintance['deadline']) - strtotime(date('Y-m-d H:i:s')));
	$days = (int)($all_sec / 86400);
	$all_sec -= ($days * 86400);
	$hours = (int)($all_sec / 3600);
	$all_sec -= ($hours * 3600);
	$minutes = (int)($all_sec / 60);
	$all_sec -= ($minutes * 60);
	$seconds = $all_sec;
	$time = '';
	if($days > 0)
		$time .= $days . ' дн. ';
	if($hours > 0)
		$time .= $hours . ' ч. ';
	if($minutes > 0)
		$time .= $minutes . ' мин. ';
	if($seconds > 0)
		$time .= $seconds . ' сек. ';
	
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

	<title>Служба доставки скоро запустится</title>
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
	<script src="/classes/maintance/maintance.js"></script>
	<link rel="stylesheet" href="/classes/maintance/maintance.css" />
	<link rel="stylesheet" href="/includes/for_all/footer/footer.css" />
	<script src="/includes/for_all/footer/footer.js"></script>
</head>
<body>' . $head . '
	<div id="wrap">
		<div id="left_rope"></div>
		<div id="right_rope"></div>
		<div id="maintance_table">
			<h1 id="mt_header">' . $maintance['mt_header'] . '</h1>
			<div id="mt_description">' 
				. $maintance['mt_description'] . '
			</div>
			<div id="clock">
				<div id="clock_text">
					До начала работы службы доставки осталось:
				</div>
				<div id="time">' . $time . '</div>
			</div>
		</div>
	</div>' . $footer . '
</body>
</html>';

	echo $html_code;
?>
