<?php
$title = '';
function article($brand, $page)
{	
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//получим всю информацию о статье
	$query = 'SELECT * FROM `published_articles` WHERE title_translit="' . $page . '"';
	$res = mysqli_query($db, $query);
	$art = mysqli_fetch_assoc($res);
	mysqli_close($db);
	
	$article = '
<div id="wrap">
	<a href="/' . $brand . '/articles/1"><div id="back_to_articles">&larr; К списку статей</div></a>
	<div id="article_view">
		<div class="article_meta">
			<div class="meta_in_article">
				<img src="/includes/for_articles/articles/icons_article/clock.png" />
				<div class="art_met_txt">' . $art['date_time'] . '</div>
			</div>
			<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,twitter,viber,whatsapp,telegram" data-counter></div>
		</div>
		<h1 id="article_head">' . $art['title'] . '</h2>'
		. $art['text'] . '
		<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,twitter,viber,whatsapp,telegram" data-counter></div>
	</div>
	<a href="/' . $brand . '/articles/1"><div id="back_to_articles">&larr; К списку статей</div></a>
</div>';

	return $article;
}
?>
