<?php
function add_info()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//код
	$html = '
<div id="right_page_inner">
<div class="small_title_add">Зона редактирования инфо-страницы</div>
<div id="edit_area">
	<div id="edit_block">
		<div class="small_title_add">Название инфо-страницы</div>
		<input class="edit_block_input" id="header_info"></input>
		<div class="small_title_add">Текст инфо-страницы</div>
		<textarea id="edit_text"></textarea>
	</div>
	<div id="buttons_block">
		<div class="small_title_add">Элементы управления</div>
		<div class="bb_buttons" id="butt_public">Опубликовать</div>
		<div class="bb_buttons" id="butt_download">Загрузить</div>
		<div class="empty"></div>
		<div class="img_text">Поместите сюда изображения которые вы хотите использовать на инфо-странице</div>
		<div id="imgs_drop"></div>
	</div>
	<div class="empty"></div>
</div>

<div id="viewing_area">
<div class="small_title_add">Предварительное отображение инфо-страницы</div>
	<div id="info_view">
	</div>
</div>
</div>';
	
	return $html;
}
?>