<?php
function change_images($brand)
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	//запрос к БД 
	$query = 'SELECT * FROM `' . $brand . '_change_images`';
	$result = mysqli_query($db, $query);
	
	$images_num = mysqli_num_rows($result);					//всего изображений
	
	//начало images_window
	$change_images = '<div id="images_window">';
	
	//01_формируем html-код для элементов img
	for($i = 0; $i < $images_num; $i++){
		$change_image_row = mysqli_fetch_assoc($result);
		
		$img_path = $change_image_row['path'];			//путь к изображению
		$class_name = $change_image_row['class_name'];	//имя класса
		
		//формируем html-код
		$change_images .= '
		<img src="/includes/for_mainpage/change_images/' . $img_path . '" class="' . $class_name . '" alt="Акция">';
	}
	
	//начало toggle_circles
	$change_images .= '<div id="toggle_circles">';
	
	//02_формируем html-код для toggle_circles
	for($i = 0; $i < $images_num; $i++){
		$change_images .= '
		<div class="toggle_circles"></div>';
	}
	
	//закрываем элемент toggle_circles
	$change_images .= '</div>';
	
	//добавляем стрелки
	$change_images .= '
	<div id="arrow_left" class="arrows"></div>
	<div id="arrow_right" class="arrows"></div>';
	
	//закрываем элемент images_window
	$change_images .= '</div>
	<div id="after_ci_block"></div>';
	
	//удаляем результат запроса и закрываем соединение с БД
	mysqli_free_result($result);
	mysqli_close($db);

	//заменим jpg/jpeg/png на webp если поддерживается браузером
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/images_to_webp.inc');
	if(isBrowserWebpSupport())
		imagesToWebp($change_images);
	
	return $change_images;
}	

?>
