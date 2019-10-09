<?php
function add_news()
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//код
	$html = '
<div id="right_page_inner">
<div class="small_title_add">Зона редактирования новостей</div>
<div id="edit_area">
	<div id="edit_block">
		<div class="small_title_add">Заголовок новости</div>
		<input class="edit_block_input" id="header_new"></input>
		<div class="small_title_add">Текст новости</div>
		<textarea id="edit_text"></textarea>
	</div>
	<div id="buttons_block">
		<div class="small_title_add">Элементы управления</div>
		<div class="bb_buttons" id="butt_save">Сохранить</div>
		<div class="bb_buttons" id="butt_public">Опубликовать</div>
		<div class="bb_buttons" id="butt_download">Загрузить</div>
		<div class="empty"></div>
		<div class="img_text">Перенесите сюда изображение для карточки новости</div>
		<div id="dropbox"></div>
		<div class="img_text">Поместите сюда изображения которые вы хотите использовать в новости</div>
		<div id="imgs_drop"></div>
	</div>
	<div class="empty"></div>
</div>

<div id="viewing_area">
<div class="small_title_add">Предварительное отображение новости</div>
	<div id="new_view">
	</div>
</div>
</div>';
	
	return $html;
}
?>