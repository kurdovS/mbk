<?php
	//01_пришел запрос на добавление пользователя
	if(isset($_POST['add'])){
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
			//проверим нету ли в системе уже пользователя с таким именем
			$query = 'SELECT COUNT(*) FROM adminpage_system_accounts WHERE name="' . $_POST['name'] . '"';
			$in_system = mysqli_query($db, $query);
			$in_system = mysqli_fetch_row($in_system);
			$in_system = $in_system[0];
		
			//пользователя в системе еще нет
			if($in_system == 0){
				$query = 'INSERT INTO adminpage_system_accounts(name, password, status, date) 
						VALUES("' . $_POST['name'] . '", "' . md5($_POST['pass']) . '", "' . $_POST['status'] .
						'", "' .  date("d.m.Y") . '")';
				$res = mysqli_query($db, $query);
				echo 'Добавлено';
			}
			//пользователь с таким именем уже есть в системе
			else
				echo 'Пользователь с именем "' . $_POST['name'] . '" уже существует в системе';
		}
		else
			echo 'Вы не можете добавлять пользователей';
		
		mysqli_close($db);
		unset($_POST['add']);
		unset($_POST['name']);
		unset($_POST['status']);
		unset($_POST['date']);
		
	}	
	
	
	//02_пришел запрос на удаление пользователя
	if(isset($_POST['del'])){
		//проверим не себя ли хочет удалить пользователь
		if($_COOKIE['adminpage_user'] == $_POST['name'])
			echo 'Вы не можете удалить из системы самого себя';
		else {
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
				//проверим есть ли в системе такой пользователь	1 - есть 0 - нету
				$query = 'SELECT COUNT(*) FROM `adminpage_system_accounts` WHERE name="' . $_POST['name'] . '"';
				$in_system = mysqli_query($db, $query);
				$in_system = mysqli_fetch_row($in_system);
				$in_system = $in_system[0];		

				//пользователя в системе нет
				if($in_system == 0)
					echo 'Пользователя "' . $_POST['name'] . '" не существует в системе';
				//пользователь в системе есть
				else {
					//выясним id удаляемого пользователя
					$query = 'SELECT * FROM `adminpage_system_accounts` WHERE name="' . $_POST['name'] . '"';
					$id_for_del = mysqli_query($db, $query);
					$id_for_del = mysqli_fetch_assoc($id_for_del);
					$id_for_del = $id_for_del['id_account'];			//id удаляемого пользователя
					//выясним количество записей идущих после удаляемого пользователя
					$query = 'SELECT * FROM `adminpage_system_accounts` WHERE id_account>' . $id_for_del;
					$after_for_del = mysqli_query($db, $query);
					$after_for_del = mysqli_num_rows($after_for_del);
					$after_for_del += $id_for_del;						//id последней записи
					//удаляем пользователя
					$query = 'DELETE FROM `adminpage_system_accounts` WHERE name="' . $_POST['name'] . '"';
					$res = mysqli_query($db, $query);
					//уменьшим id всех записей после удаленной на 1
					for($j = ($id_for_del + 1); $j <= $after_for_del; $j++){
						//для каждой статьи идущей после удаленной уменьшим id на 1
						mysqli_query($db, 'UPDATE `adminpage_system_accounts` SET `id_account`="' . ($j - 1) . '" WHERE `id_account`=' . $j);
					}
					//теперь уменьшим на 1 AUTO_INCREMENT
					mysqli_query($db, 'ALTER TABLE `adminpage_system_accounts` AUTO_INCREMENT=' . $after_for_del);
					echo 'Пользователь ' . $_POST['name'] . ' удален из системы';
				}
			}
			//пользователь не администратор
			else 
				echo 'Вы не можете удалять пользователей из системы';
			
			mysqli_close($db);
		}
		
		unset($_POST['exit']);
		unset($_POST['name']);
	}
	
	
	//03_пришел запрос на изменение информации о пользователе
	if(isset($_POST['edit'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
			
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];
		//получим старое имя изменяемого пользователя
		$query = 'SELECT * FROM `adminpage_system_accounts` WHERE id_account=' . $_POST['edit'];
		$user = mysqli_query($db, $query);
		$user = mysqli_fetch_assoc($user);
		
		//если пользователь администратор или хочет изменить свой аккаунт
		if($user_status == 'Администратор' || $user['name'] == $_COOKIE['adminpage_user']){
			//если никаких изменений нет
			if($user['name'] == $_POST['name'] && $user['status'] == $_POST['status'] && $user['password'] == $_POST['pass']){
				echo 'Вы не ввели никаких изменений';
			}
			else {
				$res = mysqli_query($db, 'UPDATE `adminpage_system_accounts` SET name="' . $_POST['name'] . '", status="' 
						. $_POST['status'] . '", password="' . md5($_POST['pass']) . '" WHERE id_account=' . $_POST['edit']);
				echo 'Вы успешно обновили информацию о пользователе';
			}
		}
		else
			echo 'Чтобы изменять информацию о других пользователях вы должны иметь в системе статус Администратора';
		
		mysqli_close($db);
		unset($_POST['edit']);
		unset($_POST['name']);
		unset($_POST['status']);
		unset($_POST['pass']);
	}
	
?>
