<?php
	//пришел запрос на удаление единичного заказа
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
			//удалим заказ
			//удалим из orders информацию о заказе и все продукты в данном заказе из products_in_cart
			mysqli_query($db, 'DELETE FROM `orders` WHERE `id_order`=' . $_POST['order_del']);
			mysqli_query($db, 'DELETE FROM `products_in_cart` WHERE `cart_id`=' . $_POST['order_del']);
			echo 'Данный заказ № ' . $_POST['order_del'] . ' не завершался более 3 дней и был удален';
		}
		else
			echo 'Только пользователи со статусом "Администратор" могут очищать БД';
		
		mysqli_close($db);
		unset($_POST['order_del']);
	}
	
	
	//пришел запрос удалить все красные заказы
	if(isset($_POST['orders_clear'])){
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
			//удалим все красные заказы
			$res = mysqli_query($db, 'SELECT * FROM orders WHERE date_order<="' . $_POST['orders_clear'] . '"');
			mysqli_query($db, 'DELETE FROM `orders` WHERE date_order<="' . $_POST['orders_clear'] . '"');
			$orders_num = mysqli_num_rows($res);
			for($i = 0; $i < $orders_num; $i++){
				$order_to_del = mysqli_fetch_assoc($res);
				//удалим все продукты данного заказа из products_in_cart
				mysqli_query($db, 'DELETE FROM `products_in_cart` WHERE `cart_id`=' . $order_to_del['id_order']);
			}
			echo 'Все устаревшие заказы были удалены';
		}
		else
			echo 'Только пользователи со статусом "Администратор" могут очищать БД';
		
		mysqli_close($db);
		unset($_POST['orders_clear']);
	}
?>
