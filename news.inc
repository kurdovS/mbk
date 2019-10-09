<?php
function news($brand, $page)
{
	$news_num_on_page = 9;		//число новостей на странице
	
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//определим первую и последнюю новость для данной страницы
	$first_new = ($page - 1) * $news_num_on_page;			//первая новость
	$last_new = ($first_new + $news_num_on_page - 1);
	$res = mysqli_query($db, 'SELECT * FROM `published_news` WHERE id_new>' . $first_new . ' ORDER BY `id_new` DESC');
	$max_new = mysqli_num_rows($res);
	if(($first_new + --$max_new) < $last_new)
		$last_new = $max_new + $first_new;
	
	//формируем html-код страницы со списком новостей
	$news = '';
	$current_new;
	for($i = $first_new; $i <= $last_new; $i++){
		$current_new = mysqli_fetch_assoc($res);
		//если запрошены новости в виде списка
		if(!isset($_COOKIE['news_view']) || $_COOKIE['news_view'] != 'icons'){
			//уберем изображения из текста
			//$txt = preg_replace('/<(img)[^>]*\>/', '', $current_new['text']);
			//возьмем текст из первого абзаца
			$st = mb_stripos($current_new['text'], "<p>");
			$en = mb_stripos($current_new['text'], "</p>");
			$ln = $en - $st;
			$txt = mb_substr($current_new['text'], $st, $ln); 
			$news .= '<a href="/' . $brand . '/news/' . $current_new['title_translit'] . '">
			<div class="new_div" name="new_card">
				<div class="new_meta">
					<div class="meta_in_new"><img src="/includes/for_news/news/icons_new/clock.png" /><div class="new_met_txt">' . $current_new['date_time'] . '</div></div>
				</div>
				<div class="new_title" name="new_card_title">' . $current_new['title'] . '</div>
				<div class="new_img_wrap" name="new_card_img"><img src="' . $current_new['img'] . '" /></div>
				<div class="new_present" name="new_present">' . $txt . '</div>
				<div class="empty"></div>
			</div></a>';
			if($i != $last_new)
				$news .= '<hr name="new_card_hr">';
		}
		//новости в виде плиток
		else {
			$news .= '<a href="/' . $brand . '/news/' . $current_new['title_translit'] . '">
			<div class="new_card">
				<div class="new_card_img"><img src="' . $current_new['img'] . '" /></div>
				<div class="new_meta">
					<div class="meta_in_new"><img src="/includes/for_news/news/icons_new/clock.png" /><div class="new_met_txt">' . $current_new['date_time'] . '</div></div>
				</div>
				<div class="new_card_title">' . $current_new['title'] . '</div>
				<div class="empty"></div>
			</div></a>';
			if($i == $last_new)
				$news .= '<div class="empty"></div>';
		}
	}
	
	//ВЫВОДИМ КНОПКИ СТРАНИЦ
	//01_получим общее число страниц по 9 новостей
	$news_num = mysqli_query($db, 'SELECT * FROM `published_news`');
	$news_num = mysqli_num_rows($news_num);			//число новостей
	$pages_num = (integer)($news_num / $news_num_on_page) + 1;			//число страниц
	//кнопки для каких страниц отображать
	$start = 1;
	$finish = 1;	
	
	$news .= '<div id="pages_butt">';
	
	//вывести кнопку влево если запрошена не самая первая страница
	if($page != 1)
		$news .= '<a href="/' . $brand . '/news/' . ($page - 1) . '"><div class="new_butt" title="Назад"><<</div></a>';
	
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
			$news .= '<a href="/' . $brand . '/news/' . $i . '"><div class="new_butt">' . $i . '</div></a>';
		else
			$news .= '<div class="new_butt_ch">' . $i . '</div>';
	}
	
	//вывести кнопку вправо если запрошена не самая последняя страница
	if($page != $pages_num)
		$news .= '<a href="/' . $brand . '/news/' . ($page + 1) . '"><div class="new_butt" title="Вперед">>></div></a>';
	$news .= '</div>';//закрываем pages_buttl;
	
	return $news;
}
?>
