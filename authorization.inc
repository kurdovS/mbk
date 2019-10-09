<?php
	//подкл. к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();

	//если пользователь не авторизован
	if(!isset($_SERVER['PHP_AUTH_USER'])){
		Header("WWW-Authenticate: Basic realm=\"Admin Page\"");
		Header("HTTP/1.0 401 Unauthorized");
		exit();
	}
	//пользователь ввел имя и пароль
	else {
		//проверяем существование пользователя и правильность пароля
		$query = 'SELECT password FROM `adminpage_system_accounts` WHERE name="' . 
				$_SERVER['PHP_AUTH_USER'] . '"';
		$result = mysqli_query($db, $query);
	
		//если в системе нет такого пользователя
		if(mysqli_num_rows($result) == 0){
			Header('WWW-Authenticate: Basic realm="Admin Page"');
			Header("HTTP/1.0 401 Unauthorized");
			exit();
		}
		//если пользователь существует сравним пароли
		else {
			//если пароль не верен
			$pass = mysqli_fetch_row($result);
			if(md5($_SERVER['PHP_AUTH_PW']) != $pass[0]){
				Header('WWW-Authenticate: Basic realm="Admin Page"');
				Header("HTTP/1.0 401 Unauthorized");
				exit();
			}
		}
	}
	
	mysqli_close($db);
	
	//запишем в COOKIE информацию о текущем пользователе на 2 часа
	setcookie("adminpage_user", $_SERVER['PHP_AUTH_USER'], time() + 7200, "/", "mbk-delivery.ru");
	setcookie("adminpage_pass", md5($_SERVER['PHP_AUTH_PW']), time() + 7200, "/", "mbk-delivery.ru");
?>
