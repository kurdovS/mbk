<?php
	//01_запрос - выбрана оплата наличными
	if(isset($_POST['cash'])){
		//записываем в таблицу "orders_in_process": cash_or_card, with_change, change_from.
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		$query = 'UPDATE `orders_buffer` SET `cash_or_card`=1, `with_change`=' . $_POST['with_change'] . 
		', `change_from`="' . $_POST['change_from'] . '" WHERE `id_order`="' . $_COOKIE['cart_id'] . '"';
		mysqli_query($db, $query);
		mysqli_close($db);
		unset($_POST['cash']);
	}
	
	//02_запрос - выбрана оплата картой онлайн
	if(isset($_POST['card_online'])){
		//записываем в таблицу "orders_in_process": cash_or_card.
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		$query = 'UPDATE `orders_buffer` SET `cash_or_card`=0 WHERE `id_order`="' . $_COOKIE['cart_id'] . '"';
		mysqli_query($db, $query);
		mysqli_close($db);
		echo 'card';
		unset($_POST['card_online']);
	}
	//НУЖНО ПРОВЕРИТЬ ЧТО 01 и 02 ВЗАИМОИСКЛЮЧАЮЩИЕ
	
?>
