<?php
function edit_info()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//шаблон по заменам для текста для вывода в список инфо-страниц поле текст вместе с тегами
	$search = array("<", ">");
	$replace = array("&lt", "&gt");
	

	//получим из БД информацию об неопубликованных инфо-страницах
	$query = 'SELECT * FROM `info_pages`';
	$res = mysqli_query($db, $query);
	$unp_num = mysqli_num_rows($res);				//всего инфо-страниц

	//определим больше ли 10 инфо-страниц
	if($unp_num > 10){
		$unp_max = 10;
		$unp_pages_num = $unp_num / 10;
	}
	else
		$unp_max = $unp_num;
	
	//01_код для инфо-страниц
	$html = '
	<div id="info_pages">
		<div class="small_title">Информационные страницы</div>
		<table class="info_table">
			<col width="3%">
			<col width="17%">
			<col width="30%">
			<col width="50%">
		
			<tr class="info_table_tr">
				<td class="info_table_title_td"></td>
				<td class="info_table_title_td">Название страницы</td>
				<td class="info_table_title_td">Название транслитом</td>
				<td class="info_table_title_td">Содержание страницы</td>
			</tr>';
			
	for($i = 0; $i < $unp_max; $i++){
		$unp_info = mysqli_fetch_assoc($res);
		$html .= '
			<tr>
				<td class="info_table_regular_td"><div class="at_div">' . $unp_info['id_info'] . '</div></td>
				<td class="info_table_regular_td"><div class="at_div"><b>' . $unp_info['title'] . '</b></div></td>
				<td class="info_table_regular_td"><div class="at_div">' . $unp_info['title_translit'] . '</div></td>
				<td class="info_table_regular_td"><div class="at_div">' . str_replace($search, $replace, $unp_info['text']) . '</div></td>
				<td class="info_table_regular_td_l"><div class="edit_info">Изменить</div></td>
			</tr>';
	}
	
	$html .= '</table></div>';	//закрываем таблицу и #info_pages
		
	//выведем кнопки навигации по страницам статей
	if($unp_num > 10){
		$html .= '<div id="pages_butt">';
			for($i = 0; $i < $unp_pages_num; $i++)
				$html .= '<div class="unp_pages_butt">' . ($i + 1) . '</div>';
		$html .= '</div>';
	}
			
	return $html;
}
?>