<?php
	//пришел запрос на перенос заказа из "текущих заказов" в "выполненные заказы"
	if(isset($_POST['order_done'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];					//статус пользователя в системе
		$user_name = $_COOKIE['adminpage_user'];		//имя пользователя

		//если пользователь Администратор
		if($user_status == 'Администратор'){
			//перенесем данные из orders_in_process в orders_done
			//$order_in_process = mysqli_query($db, 'SELECT * FROM `orders_in_process` WHERE `id_order`=' . $_POST['order_done']);
			$query = 'INSERT INTO `orders_done` (id_order, client_name, phone_number, dont_call, ' . 
				'delivery_time, delivery_address, cash_or_card, order_sum, delivery_sum, with_change, ' . 
				'change_from, order_date, paid, promocode_used) SELECT id_order, client_name, phone_number, ' . 
				'dont_call, delivery_time, delivery_address, cash_or_card, order_sum, delivery_sum, ' . 
				'with_change, change_from, order_date, paid, promocode_used FROM `orders_in_process` WHERE ' . 
				'`id_order`=' . $_POST['order_done'];
			mysqli_query($db, $query);
			//удалим из orders_in_process информацию о заказе
			mysqli_query($db, 'DELETE FROM `orders_in_process` WHERE `id_order`=' . $_POST['order_done']);
			echo 'Данный заказ № ' . $_POST['order_done'] . ' завершен';
			//echo $query;
		}
		else
			echo 'Только пользователь со статусом "Администратор" может завершать заказы';

		mysqli_close($db);
		unset($_POST['order_done']);
	}


	//пришел запрос на удаление заказа из "выполненных заказов"
	if(isset($_POST['order_del'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];					//статус пользователя в системе
		$user_name = $_COOKIE['adminpage_user'];		//имя пользователя

		//если пользователь Администратор
		if($user_status == 'Администратор'){
			//удалим из orders_done информацию о заказе
			mysqli_query($db, 'DELETE FROM `orders_done` WHERE `id_order`=' . $_POST['order_del']);
			echo 'Данный заказ № ' . $_POST['order_del'] . ' удален';
			//echo $query;
		}
		else
			echo 'Только пользователь со статусом "Администратор" может удалять заказы';

		mysqli_close($db);
		unset($_POST['order_del']);
	}



	//пришел запрос на удаление заказа из "неоформленных заказов"
	if(isset($_POST['buffer_del'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];					//статус пользователя в системе
		$user_name = $_COOKIE['adminpage_user'];		//имя пользователя

		//если пользователь Администратор
		if($user_status == 'Администратор'){
			//удалим из orders_done информацию о заказе
			mysqli_query($db, 'DELETE FROM `orders_buffer` WHERE `id_order`=' . $_POST['buffer_del']);
			echo 'Данный неоформленный заказ № ' . $_POST['buffer_del'] . ' удален';
			//echo $query;
		}
		else
			echo 'Только пользователь со статусом "Администратор" может удалять заказы';

		mysqli_close($db);
		unset($_POST['order_del']);
	}
?>
