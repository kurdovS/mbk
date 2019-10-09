<?php
$title = '';
function newf($brand, $page)
{	
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//получим всю информацию о статье
	$query = 'SELECT * FROM `published_news` WHERE title_translit="' . $page . '"';
	$res = mysqli_query($db, $query);
	$new = mysqli_fetch_assoc($res);
	mysqli_close($db);
	
	$new_f = '
<div id="wrap">
	<a href="/' . $brand . '/news/1"><div id="back_to_news">&larr; К списку новостей</div></a>
	<div id="new_view">
		<div class="new_meta">
			<div class="meta_in_new">
				<img src="/includes/for_news/news/icons_new/clock.png" />
				<div class="new_met_txt">' . $new['date_time'] . '</div>
			</div>
			<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,twitter,viber,whatsapp,telegram" data-counter></div>
		</div>
		<h1 id="new_head">' . $new['title'] . '</h2>'
		. $new['text'] . '
		<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,twitter,viber,whatsapp,telegram" data-counter></div>
	</div>
	<a href="/' . $brand . '/news/1"><div id="back_to_news">&larr; К списку новостей</div></a>
</div>';

	return $new_f;
}
?>
