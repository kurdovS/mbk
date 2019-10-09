<?php
	//01_пришел запрос на добавление промокода
	if(isset($_POST['add_0'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];

		//если пользователь имеет в системе статус "Администратор"
		if($user_status == 'Администратор'){
			$query = 'INSERT INTO `promocodes` (`promocode`, `type`, `num`, `date`, `discount`) VALUES ("' . 
					$_POST['add_0'] . '", "' . $_POST['add_1'] . '", "' . $_POST['add_2'] . '", "' .
					$_POST['add_3'] . '", "' . $_POST['add_4'] . '")';
			mysqli_query($db, $query);

			echo 'Промокод был успешно добавлен';
		}
		else {
			echo 'Только администратор может добавлять промокоды';
		}

		//закрываем бд
		mysqli_close($db);
		unset($_POST['add_0']);
		unset($_POST['add_1']);
		unset($_POST['add_2']);
		unset($_POST['add_3']);
		unset($_POST['add_4']);
	}




	//02_пришел запрос на удаление промокода
	if(isset($_POST['del'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];

		//если пользователь администратор
		if($user_status == 'Администратор'){
			//удаляем промокод из БД
			mysqli_query($db, 'DELETE FROM `promocodes` WHERE `promocode`=\'' . $_POST['del'] . '\'');
			echo 'Промокод был успешно удален';
		}
		//пользователь не администратор
		else {
			echo 'Только администратор может удалять промокоды';
		}

		mysqli_close($db);
		unset($_POST['del']);
	}


	//03_пришел запрос на изменение промокода
	if(isset($_POST['edit_4'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];

		//если пользователь администратор
		if($user_status == 'Администратор'){
			$is_there_pr = mysqli_query($db, 'SELECT * FROM promocodes WHERE promocode="' . $_POST['edit_4'] . '"');
			$is_there_pr = mysqli_num_rows($is_there_pr);
			if($is_there_pr){
				//запишем промокод в таблицу промокодов
				$query = 'UPDATE `promocodes` SET promocode="' . $_POST['edit_4'] . '", type="' . $_POST['edit_3'] . '", num="' . 
					$_POST['edit_2'] . '", date="' . $_POST['edit_1'] . '", discount="' . $_POST['edit_0'] . '" 
					WHERE `promocode`=\'' . $_POST['edit_4'] . '\'';
				mysqli_query($db, $query);
				echo 'Промокод был успешно изменен';
			}
			else
				//echo $query;
				echo 'Невозможно изменить сам промокод, создайте новый, а данный удалите';
		}
		else
			echo 'Только администратор может изменять промокоды';

		mysqli_close($db);
		unset($_POST['edit_0']);
		unset($_POST['edit_1']);
		unset($_POST['edit_2']);
		unset($_POST['edit_3']);
		unset($_POST['edit_4']);
	}
?>
