<?php
function class_news($brand, $page)
{
	//01_подключаем все подключаемые файлы для news
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/header/header.inc');
	$head = head($brand);
		
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/footer/footer.inc');
	$footer = footer($brand);
	
	$burgerking_style = '';
		if($brand == 'burgerking')
			$burgerking_style = '<link rel="stylesheet" href="/includes/for_all/burgerking_style/burgerking_style.css" />';
	
	//если запрошен список статей, а не конкретная статья
	if(preg_match('/^[0-9]+?/', $page)){
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_news/news/news.inc');
		$news = news($brand, $page);
		$title = '<title>Новости - MBK-Delivery</title>';
		$description = 'Новости о еде - MBK-Delivery - Страница ' . $page;
		$keywords = 'Новости о еде, Макдональдс доставка, Бургер Кинг доставка, KFC доставка, Макдональдс доставка на дом, Бургер Кинг доставка на дом, KFC доставка на дом, mcdonalds доставка, burgerking доставка, kfc доставка';

		$css_js = '<link rel="stylesheet" href="/includes/for_news/news/news.css" />
					<script src="/includes/for_news/news/news.js"></script>';
		$in_body = '<div id="wrap">
<div id="news_wrap">
	<a href="/' . $brand . '"><div id="back_to_main">&larr; На главную</div></a>
	<div id="news_view_switcher">
		<h1>Новости</h1>
		<div id="switcher_view">
			<div class="switcher_img" id="view_icons"><img src="/includes/for_news/news/icons_new/icons.png" /></div>
			<div class="switcher_img" id="view_rows"><img src="/includes/for_news/news/icons_new/rows.png" /></div>
		</div>
	</div>
	<div id="news_area">' . $news . '</div>
</div></div>';

	//кнопки поделиться
		$share_butts = '';
	}
	//если запрошена конкретная новость
	else {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_news/new/new.inc');
		$css_js = '<link rel="stylesheet" href="/includes/for_news/new/new.css" />';
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//кнопки поделиться
		$share_butts = '<script src="https://yastatic.net/share2/share.js" async="async"></script>';

		//получим всю информацию о новости
		$query = 'SELECT * FROM `published_news` WHERE title_translit="' . $page . '"';
		$res = mysqli_query($db, $query);
		$new = mysqli_fetch_assoc($res);
		mysqli_close($db);
		$title = '<title>' . $new['title'] . ' - MBK-Delivery</title>';
		$description = $new['title'] . ' - MBK-Delivery';
		$keywords = 'Новости о еде, Макдональдс доставка, Бургер Кинг доставка, KFC доставка, Макдональдс доставка на дом, Бургер Кинг доставка на дом, KFC доставка на дом, mcdonalds доставка, burgerking доставка, kfc доставка';
		$in_body = newf($brand, $page);
	}
	
	
	//02_начинаем формировать html-код
	$html_code = '
<!DOCTYPE html>
<html lang="ru">
<head>' . $share_butts . '

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
	<meta name="description" content="' . $description . '" />
	<meta name="keywords" content="' . $keywords . '" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta charset="utf-8" />
	
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
