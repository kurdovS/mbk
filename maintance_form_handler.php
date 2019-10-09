<?php
	//01_пришел запрос на добавление в таблицу maintance
	if(isset($_POST['add'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//шаблон по заменам для текста
		$replace = array("<", ">");
		$search = array("&lt;", "&gt;");
	
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];
	
		//если пользователь имеет в системе статус "Администратор"
		if($user_status == 'Администратор'){
			$res = mysqli_query($db, 'SELECT * FROM `maintance` WHERE `id_maintance`=1');
			$maintance_num = mysqli_num_rows($res);
		
			//если таблица maintance пуста, то добавим в нее запись
			if($maintance_num == 0){
				mysqli_query($db, 'INSERT INTO `maintance` VALUES("1", "' . addslashes($_POST['mt_header']) . '", "' . addslashes(str_replace($search, $replace, $_POST['mt_description'])) . '", "'
							. addslashes($_POST['deadline']) . '")');
			}
			//если таблица maintance не пуста, то обновим единственную запись
			else {
				mysqli_query($db, 'UPDATE `maintance` SET id_maintance="1", mt_header="' . addslashes($_POST['mt_header']) . '", mt_description="' . 
							addslashes(str_replace($search, $replace, $_POST['mt_description'])) . '", deadline="' . addslashes($_POST['deadline']) . '" WHERE id_maintance="1"');
			}
		
			//сбросим auto_increment
			mysqli_query($db, 'ALTER TABLE `maintance` AUTO_INCREMENT=1');
			
			echo 'Запись была добавлена в таблицу';
		}
		else
			echo 'Только пользователи со статусом Администратор могут останавливать работу службы доставки';
		
		mysqli_close($db);
		unset($_POST['add']);
	}
	
	
	
	//02_пришел запрос на удаление причины не работы службы доставки
	if(isset($_POST['del'])){
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
			//удалим запись
			mysqli_query($db, 'DELETE FROM `maintance` WHERE id_maintance="1"');
			//сбросим auto_increment
			mysqli_query($db, 'ALTER TABLE `maintance` AUTO_INCREMENT=0');
			
			echo 'Запись была удалена. Служба доставки запущена';
		}
		else
			echo 'Только пользователи со статусом Администратор могут включать работу службы доставки';
		
		mysqli_close($db);
		unset($_POST['del']);
	}
	
	
	//03_пришел запрос на изменение причины не работы службы доставки
	if(isset($_POST['edit'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//шаблон по заменам для текста
		$replace = array("<", ">");
		$search = array("&lt;", "&gt;");
	
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];
		
		//если пользователь имеет в системе статус "Администратор"
		if($user_status == 'Администратор'){
			mysqli_query($db, 'UPDATE `maintance` SET id_maintance="1", mt_header="' . addslashes($_POST['mt_header']) . '", mt_description="' . 
							addslashes(str_replace($search, $replace, $_POST['mt_description'])) . '", deadline="' . addslashes($_POST['deadline']) . '" WHERE id_maintance="1"');
			
			//сбросим auto_increment
			mysqli_query($db, 'ALTER TABLE `maintance` AUTO_INCREMENT=1');
			
			echo 'Изменения были сохранены';
		}
		else
			echo 'Только пользователи со статусом Администратор могут изменять работу службы доставки';
		
		mysqli_close($db);
		unset($_POST['edit']);
	}
?>