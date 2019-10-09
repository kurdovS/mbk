<?php
function edit_news()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//шаблон по заменам для текста для вывода в список новостей поле текст вместе с тегами
	$search = array("<", ">");
	$replace = array("&lt", "&gt");
	
	//01_ДЛЯ НЕОПУБЛИКОВАННЫХ НОВОСТЕЙ
	//получим из БД информацию об неопубликованных новостях
	$query = 'SELECT * FROM `unpublished_news`';
	$res = mysqli_query($db, $query);
	$unp_num = mysqli_num_rows($res);				//всего новостей

	//определим больше ли 10 неопубликованных новостей
	if($unp_num > 10){
		$unp_max = 10;
		$unp_pages_num = $unp_num / 10;
	}
	else
		$unp_max = $unp_num;
	
	//01_код для неопубликованных новостей
	$html = '
	<div id="unpublished">
		<div class="small_title">Новости ожидающие публикации</div>
		<table class="news_table">
			<col width="3%">
			<col width="20%">
			<col width="42%">
			<col width="8%">
			<col width="7%">
			<col width="10%">
			<col width="10%">
		
			<tr class="news_table_tr">
				<td class="news_table_title_td"></td>
				<td class="news_table_title_td">Заголовок новости</td>
				<td class="news_table_title_td">Текст новости</td>
				<td class="news_table_title_td">Путь к изображению</td>
				<td class="news_table_title_td">Автор</td>
				<td class="news_table_title_td">Дата создания</td>
				<td class="news_table_title_td">Статус</td>
			</tr>';
			
	for($i = 0; $i < $unp_max; $i++){
		$unp_news = mysqli_fetch_assoc($res);
		$html .= '
			<tr>
				<td class="news_table_regular_td"><div class="at_div">' . $unp_news['id_new'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div"><b>' . $unp_news['title'] . '</b></div></td>
				<td class="news_table_regular_td"><div class="at_div">' . str_replace($search, $replace, $unp_news['text']) . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $unp_news['img'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $unp_news['author'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $unp_news['date_time'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $unp_news['status'] . '</div></td>
				<td class="news_table_regular_td_l"><div class="edit_new">Изменить</div></td>
			</tr>';
	}
	
	$html .= '</table></div>';	//закрываем таблицу и #unpublished
		
	//выведем кнопки навигации по страницам новостей
	if($unp_num > 10){
		$html .= '<div id="pages_butt">';
			for($i = 0; $i < $unp_pages_num; $i++)
				$html .= '<div class="unp_pages_butt">' . ($i + 1) . '</div>';
		$html .= '</div>';
	}
	
	
	//02_ДЛЯ ОПУБЛИКОВАННЫХ НОВОСТЕЙ
	//получим из БД информацию об неопубликованных новостях
	$query = 'SELECT * FROM `published_news`';
	$res = mysqli_query($db, $query);
	$p_num = mysqli_num_rows($res);				//всего новостей

	//определим больше ли 10 неопубликованных новостей
	if($p_num > 10){
		$p_max = 10;
		$p_pages_num = $p_num / 10;
	}
	else
		$p_max = $p_num;
	
	//02_код для опубликованных новостей
	$html .= '
	<div id="published">
		<div class="small_title">Опубликованные новости</div>
		<table class="news_table">
			<col width="3%">
			<col width="20%">
			<col width="42%">
			<col width="8%">
			<col width="7%">
			<col width="10%">
			<col width="10%">
		
			<tr class="news_table_tr">
				<td class="news_table_title_td"></td>
				<td class="news_table_title_td">Заголовок новости</td>
				<td class="news_table_title_td">Текст новости</td>
				<td class="news_table_title_td">Путь к изображению</td>
				<td class="news_table_title_td">Автор</td>
				<td class="news_table_title_td">Дата публикации</td>
				<td class="news_table_title_td">Статус</td>
			</tr>';
			
	for($i = 0; $i < $p_max; $i++){
		$p_news = mysqli_fetch_assoc($res);
		$html .= '
			<tr>
				<td class="news_table_regular_td"><div class="at_div">' . $p_news['id_new'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div"><b>' . $p_news['title'] . '</b></div></td>
				<td class="news_table_regular_td"><div class="at_div">' . str_replace($search, $replace, $p_news['text']) . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $p_news['img'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $p_news['author'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $p_news['date_time'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $p_news['status'] . '</div></td>
				<td class="news_table_regular_td_l"><div class="edit_new">Изменить</div></td>
			</tr>';
	}
	
	$html .= '</table></div>';	//закрываем таблицу и #published
		
	//выведем кнопки навигации по страницам новостей
	if($p_num > 10){
		$html .= '<div id="pages_butt">';
			for($i = 0; $i < $p_pages_num; $i++)
				$html .= '<div class="p_pages_butt">' . ($i + 1) . '</div>';
		$html .= '</div>';
	}
			
	return $html;
}
?>