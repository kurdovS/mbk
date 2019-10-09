<?php
$title = '';
function info($brand, $page)
{	
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//получим всю информацию о статье
	$query = 'SELECT * FROM `info_pages` WHERE title_translit="' . $page . '"';
	$res = mysqli_query($db, $query);
	$info = mysqli_fetch_assoc($res);
	mysqli_close($db);
	
	$info = '
<div id="wrap">
	<a href="/' . $brand . '"><div id="back_to_main">&larr; На главную</div></a>
	<div id="info_view">
		<h1 id="info_head">' . $info['title'] . '</h2>'
		. $info['text'] . '
	</div>';

	//заменим jpg/jpeg/png на webp если поддерживается браузером
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/images_to_webp.inc');
	if(isBrowserWebpSupport())
		imagesToWebp($info);

	$info .= '<a href="/' . $brand . '"><div id="back_to_main">&larr; На главную</div></a></div>';

	return $info;
}
?>
