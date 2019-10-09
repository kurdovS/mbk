<?php
function articles($brand, $page)
{
	$articles_num_on_page = 9;		//число статей на странице
	
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//определим первую и последнюю статью для данной страницы
	$first_article = ($page - 1) * $articles_num_on_page;			//первая статья
	$last_article = ($first_article + $articles_num_on_page - 1);
	$res = mysqli_query($db, 'SELECT * FROM `published_articles` WHERE id_article>' . $first_article . ' ORDER BY `id_article` DESC');
	$max_article = mysqli_num_rows($res);
	if(($first_article + --$max_article) < $last_article)
		$last_article = $max_article + $first_article;
	
	//формируем html-код страницы со списком статей
	$articles = '';
	$current_article;
	for($i = $first_article; $i <= $last_article; $i++){
		$current_article = mysqli_fetch_assoc($res);
		//если запрошены статьи в виде списка
		if(!isset($_COOKIE['view']) || $_COOKIE['view'] != 'icons'){
			//уберем изображения из текста
			//$txt = preg_replace('/<(img)[^>]*\>/', '', $current_article['text']); 
			//возьмем текст из первого абзаца
			$st = mb_stripos($current_article['text'], "<p>");
			$en = mb_stripos($current_article['text'], "</p>");
			$ln = $en - $st;
			$txt = mb_substr($current_article['text'], $st, $ln);
			$articles .= '<a href="/' . $brand . '/articles/' . $current_article['title_translit'] . '">
			<div class="article_div" name="article_card">
				<div class="article_meta">
					<div class="meta_in_article"><img src="/includes/for_articles/articles/icons_article/clock.png" /><div class="art_met_txt">' . $current_article['date_time'] . '</div></div>
				</div>
				<div class="article_title" name="article_card_title">' . $current_article['title'] . '</div>
				<div class="article_img_wrap" name="article_card_img"><img src="' . $current_article['img'] . '" /></div>
				<div class="article_present" name="article_present">' . $txt . '</div>
				<div class="empty"></div>
			</div></a>';
			if($i != $last_article)
				$articles .= '<hr name="article_card_hr">';
		}
		//статьи в виде плиток
		else {
			$articles .= '<a href="/' . $brand . '/articles/' . $current_article['title_translit'] . '">
			<div class="article_card">
				<div class="article_card_img"><img src="' . $current_article['img'] . '" /></div>
				<div class="article_meta">
					<div class="meta_in_article"><img src="/includes/for_articles/articles/icons_article/clock.png" /><div class="art_met_txt">' . $current_article['date_time'] . '</div></div>
				</div>
				<div class="article_card_title">' . $current_article['title'] . '</div>
				<div class="empty"></div>
			</div></a>';
			if($i == $last_article)
				$articles .= '<div class="empty"></div>';
		}
	}
	
	//ВЫВОДИМ КНОПКИ СТРАНИЦ
	//01_получим общее число страниц по 9 статей
	$articles_num = mysqli_query($db, 'SELECT * FROM `published_articles`');
	$articles_num = mysqli_num_rows($articles_num);			//число статей
	$pages_num = (integer)($articles_num / $articles_num_on_page) + 1;			//число страниц
	//кнопки для каких страниц отображать
	$start = 1;
	$finish = 1;	
	
	$articles .= '<div id="pages_butt">';
	
	//вывести кнопку влево если запрошена не самая первая страница
	if($page != 1)
		$articles .= '<a href="/' . $brand . '/articles/' . ($page - 1) . '"><div class="art_butt" title="Назад"><<</div></a>';
	
	//определим номера кнопок, которые надо вывести
	if($pages_num < 5){
		$start = 1;
		$finish = $pages_num;
	}
	else {
		if(($page + 2) < $pages_num){
			if($page >= 3){ 
				$start = $page - 2;
				$finish = $page + 2;
			}
			else {
				$start = 1;
				$finish = 5;
			}
		}
		else {
			$start = $pages_num - 4;
			$finish = $pages_num;
		}
	}
	
	//выведем кнопки
	for($i = $start; $i <= $finish; $i++){
		if($i != $page)
			$articles .= '<a href="/' . $brand . '/articles/' . $i . '"><div class="art_butt">' . $i . '</div></a>';
		else
			$articles .= '<div class="art_butt_ch">' . $i . '</div>';
	}
	
	//вывести кнопку вправо если запрошена не самая последняя страница
	if($page != $pages_num)
		$articles .= '<a href="/' . $brand . '/articles/' . ($page + 1) . '"><div class="art_butt" title="Вперед">>></div></a>';
	$articles .= '</div>';//закрываем pages_buttl;
	
	return $articles;
}
?>
