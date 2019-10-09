<?php
	//пришел запрос на проверку промокода
	if(isset($_POST['promocode'])){
		$promocode = $_POST['promocode'];

		//переменная которая возвращается в JS и представляет собой новую стоимость доставки
		$delivery_cost = 129;
		//переменная которая возвращается в JS и содержит в себе сообщение о результате проверки промокода
		$promocode_message = '';


		//подкл. к БД чтобы проверить промокод
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//ищем промокод в таблице
		$result = mysqli_query($db, 'SELECT COUNT(*) FROM `promocodes` WHERE `promocode`="' . $promocode . '"');
		$yes_or_no = mysqli_fetch_row($result);
		$yes_or_no = $yes_or_no[0];
		//если промокод найден получим все его значения
		if(!$yes_or_no){
			$promocode_message = "Промокод неверен или истекло время его действия";
		}
		else {
			//получим все значения промокода
			$result = mysqli_query($db, 'SELECT * FROM `promocodes` WHERE `promocode`="' . $promocode . '"');
			$promocode_row = mysqli_fetch_assoc($result);

			//получим новую стоимость доставки с учетом скидки
			$delivery_cost -= $promocode_row['discount'];
			//сообщение
			$promocode_message = "Промокод успешно активирован";
		}

		mysqli_close($db);
		unset($_POST['promocode']);

		echo $promocode_message . 'hilas' . $delivery_cost;

	}


	//пришел запрос записать данные о промокоде в таблицу orders
	if(isset($_POST['promokod'])){
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//запишем в БД
		$query = 'UPDATE `orders` SET id_order=' . $_COOKIE['cart_id'] . ', date_order=\'' . date("Y-m-d") . '\', sum=' . $_POST['products_sum'] . 
			', delivery_sum=' . $_POST['delivery_sum'] . ', promocode_used=\'' . $_POST['promokod'] . '\'' .
			' WHERE id_order=' . $_COOKIE['cart_id'];


		mysqli_query($db, $query);
		mysqli_close($db);

		unset($_POST['promokod']);
		unset($_POST['products_sum']);
		unset($_POST['delivery_sum']);

		echo 'WELL_DONE';
	}
?>
