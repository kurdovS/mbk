<?php

function advantages($brand)
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	
	//запрос к БД 
	$query = 'SELECT * FROM `advantages`';
	$result = mysqli_query($db, $query);
	
	$advant_num = mysqli_num_rows($result);					//всего преимуществ
	$rows_advant_num = (int)($advant_num / 3);				//число строк преимуществ
	$advant_in_row_num = 3;									//число преимуществ в последней строке, может быть < 3
	

	//сделаем правильные названия для ресторанов
	$br_rest = 'McDonald\'s';
	if($brand == 'burgerking')
		$br_rest = 'Burger King';
	else if($brand == 'kfc')
		$br_rest = 'KFC';


	//начинаем заполнять html-код
	$advantages = '<div id="advantages">
		<div id="advant_description">
			<div id="advant_description_h2">
				Доставка из ' . $br_rest . ' в Рязани
			</div>
			<!--noindex-->Хотите отведать бургеров из любимого ресторана быстрого обслуживания, но внешние факторы
			такие как усталость, плохая погода или не желание стоять в очереди мешают вам в этом?
			Теперь это все не проблема! Пусть еда приедет сама!<!--/noindex-->
		</div><!--noindex-->';
		
	
	//цикл по строкам по 3 преимущества
	for($i = 0; $i <= $rows_advant_num; $i++){
		$advantages .= '<div class="one_row">';
		
		//если это последняя строка, то в ней может быть меньше чем 3 преимущества, 
		//поэтому узнаем сколько их
		if($rows_advant_num == $i)
			$advant_in_row_num = $advant_num % 3;
		
		
		//цикл по преимуществам в строке
		for($j = 0; $j < $advant_in_row_num; $j++){
			$advantages .= '<div class="one_cell">';
			
			$advant = mysqli_fetch_assoc($result);		//получаем строку БД для одного преимущества
			
			$img_path = $advant['img_path'];			//путь к иконке для данного преимущества
			$advant_h = $advant['advant_h'];			//заголовок для данного преимущества
			$advant_span = $advant['advant_span'];		//описание для данного преимущества
			
			//html-код для внутренностей одного one_cell
			$advantages .= '<img src="/includes/for_mainpage/advantages/' . $img_path . '" class="advant_icon" alt="' . $advant_h . '"/>';
			$advantages .= '<h3 class="advant_h">' . $advant_h . '</h3>';
			$advantages .= '<span class="advant_span">' . $advant_span . '</span>';
			$advantages .= '<hr class="cell_hr" />';
			$advantages .= '</div>';
		}
		
		//закрываем строку преимуществ
		$advantages .= '<div class="clear_box"></div></div>';
	}
	$advantages .= '<!--/noindex--></div>';	//закрываем advantages
	
	//удаляем результат запроса и закрываем соединение с БД
	mysqli_free_result($result);
	mysqli_close($db);
	
	return $advantages;
}
?>
