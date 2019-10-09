<?php
function del_articles()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//шаблон по заменам для текста для вывода в список статей поле текст вместе с тегами
	$search = array("<", ">");
	$replace = array("&lt", "&gt");
	
	//01_ДЛЯ НЕОПУБЛИКОВАННЫХ СТАТЕЙ
	//получим из БД информацию об неопубликованных статьях
	$query = 'SELECT * FROM `unpublished_articles`';
	$res = mysqli_query($db, $query);
	$unp_num = mysqli_num_rows($res);				//всего статей

	//определим больше ли 10 неопубликованных статей
	if($unp_num > 10){
		$unp_max = 10;
		$unp_pages_num = $unp_num / 10;
	}
	else
		$unp_max = $unp_num;
	
	//01_код для неопубликованных статей
	$html = '
	<div id="unpublished">
		<div class="small_title">Статьи ожидающие публикации</div>
		<table class="articles_table">
			<col width="3%">
			<col width="20%">
			<col width="42%">
			<col width="8%">
			<col width="7%">
			<col width="10%">
			<col width="10%">
		
			<tr class="articles_table_tr">
				<td class="articles_table_title_td"></td>
				<td class="articles_table_title_td">Заголовок статьи</td>
				<td class="articles_table_title_td">Текст статьи</td>
				<td class="articles_table_title_td">Путь к изображению</td>
				<td class="articles_table_title_td">Автор</td>
				<td class="articles_table_title_td">Дата создания</td>
				<td class="articles_table_title_td">Статус</td>
			</tr>';
			
	for($i = 0; $i < $unp_max; $i++){
		$unp_articles = mysqli_fetch_assoc($res);
		$html .= '
			<tr>
				<td class="articles_table_regular_td"><div class="at_div">' . $unp_articles['id_article'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div"><b>' . $unp_articles['title'] . '</b></div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . str_replace($search, $replace, $unp_articles['text']) . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $unp_articles['img'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $unp_articles['author'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $unp_articles['date_time'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $unp_articles['status'] . '</div></td>
				<td class="articles_table_regular_td_l"><div class="del_article">Удалить</div></td>
			</tr>';
	}
	
	$html .= '</table></div>';	//закрываем таблицу и #unpublished
		
	//выведем кнопки навигации по страницам статей
	if($unp_num > 10){
		$html .= '<div id="pages_butt">';
			for($i = 0; $i < $unp_pages_num; $i++)
				$html .= '<div class="unp_pages_butt">' . ($i + 1) . '</div>';
		$html .= '</div>';
	}
	
	
	//02_ДЛЯ ОПУБЛИКОВАННЫХ СТАТЕЙ
	//получим из БД информацию об неопубликованных статьях
	$query = 'SELECT * FROM `published_articles`';
	$res = mysqli_query($db, $query);
	$p_num = mysqli_num_rows($res);				//всего статей

	//определим больше ли 10 неопубликованных статей
	if($p_num > 10){
		$p_max = 10;
		$p_pages_num = $p_num / 10;
	}
	else
		$p_max = $p_num;
	
	//02_код для опубликованных статей
	$html .= '
	<div id="published">
		<div class="small_title">Опубликованные статьи</div>
		<table class="articles_table">
			<col width="3%">
			<col width="20%">
			<col width="42%">
			<col width="8%">
			<col width="7%">
			<col width="10%">
			<col width="10%">
		
			<tr class="articles_table_tr">
				<td class="articles_table_title_td"></td>
				<td class="articles_table_title_td">Заголовок статьи</td>
				<td class="articles_table_title_td">Текст статьи</td>
				<td class="articles_table_title_td">Путь к изображению</td>
				<td class="articles_table_title_td">Автор</td>
				<td class="articles_table_title_td">Дата публикации</td>
				<td class="articles_table_title_td">Статус</td>
			</tr>';
			
	for($i = 0; $i < $p_max; $i++){
		$p_articles = mysqli_fetch_assoc($res);
		$html .= '
			<tr>
				<td class="articles_table_regular_td"><div class="at_div">' . $p_articles['id_article'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div"><b>' . $p_articles['title'] . '</b></div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . str_replace($search, $replace, $p_articles['text']) . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $p_articles['img'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $p_articles['author'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $p_articles['date_time'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $p_articles['status'] . '</div></td>
				<td class="articles_table_regular_td_l"><div class="del_article">Удалить</div></td>
			</tr>';
	}
	
	$html .= '</table></div>';	//закрываем таблицу и #published
		
	//выведем кнопки навигации по страницам статей
	if($p_num > 10){
		$html .= '<div id="pages_butt">';
			for($i = 0; $i < $p_pages_num; $i++)
				$html .= '<div class="p_pages_butt">' . ($i + 1) . '</div>';
		$html .= '</div>';
	}
			
	return $html;
}
?>