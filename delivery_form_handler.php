<?php
	//01_сохраняем присланные значения
	if(isset($_POST['name'])){
		$name = $_POST['name'];
		$phone = $_POST['ph'];
		$delivery_time = $_POST['time'];

		
		//если не указано время доставки, то время доставки = сейчас + 1 час
		if($delivery_time == ""){
			$delivery_time = (date("H") + 1) . ':' . date("i");
		}
	
		$is_dont_call = $_POST['dont_call'];
	
	
		//все для адреса
		$street = $_POST['street'];
		$home = $_POST['home'];
		
		//если указан корпус
		if(isset($_POST['corpus']))
			$corpus = $_POST['corpus'];
		
		//если указано строение
		if(isset($_POST['build']))
			$build = $_POST['build'];
		
		//если указан подъезд
		if(isset($_POST['entrance']))
			$entrance = $_POST['entrance'];
		
		//если указан этаж
		if(isset($_POST['floor']))
			$floor = $_POST['floor'];
		
		$apartment = $_POST['apartment'];
	
	
		//соберем весь адрес в одну строку
		$adress = $street . " д." . $home;
	
		if(isset($corpus)){
			$adress .= " к.";
			$adress .= $corpus;
		}
	
		if(isset($build)){
			$adress .= " стр.";
			$adress .= $build;
		}
	
		if(isset($entrance)){
			$adress .= " подъезд.";
			$adress .= $entrance;
		}
	
		if(isset($floor)){
			$adress .= " этаж.";
			$adress .= $floor;
		}
	
		$adress .= " кв.";
		$adress .= $apartment;
		
		unset($_POST['name']);
	
	
	
		//02_непосредственно заносим в БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		//в таблицу клиенты информацию о клиенте
		//$result = mysqli_query($db, 'SELECT COUNT(*) FROM `clients` WHERE `id_order`=' . $_COOKIE['cart_id']);
		$res_phone = mysqli_query($db, 'SELECT COUNT(*) FROM `clients` WHERE `phone`=' . $phone);
		$is_phone_there = mysqli_fetch_row($res_phone);
		$is_phone_there = $is_phone_there[0];
		//$is_there = mysqli_fetch_row($result);
		//$is_there = $is_there[0];
		if($is_phone_there == 0){
			$query = 'INSERT INTO `clients` (`name`, `phone`, `adress`, `id_order`) VALUES 
				("' . $name . '", "' . $phone . '", "' . $adress . '", "' . $_COOKIE['cart_id'] .  '")';
		}
		else {
			$query = 'UPDATE `clients` SET `name`="' . $name . '", `phone`="' . $phone . '", `adress`="' . $adress . '", `id_order`="' . $_COOKIE['cart_id'] . 
				'" WHERE `phone`="' . $phone . '"';
		}
 		mysqli_query($db, $query);
	
		//в таблицу "orders" обновим информацию об id_client
		$query = 'SELECT `id_client` FROM `clients` WHERE `phone`="' . $phone . '"';
		$result = mysqli_query($db, $query);
		$id_client = mysqli_fetch_row($result);
		//обновляем id_client в "orders"
		$query = 'UPDATE `orders` SET `id_user`=' . $id_client[0] . ' WHERE `id_order`=' . $_COOKIE['cart_id'];
		mysqli_query($db, $query);
	
		//в таблицу "orders_in_process"
		$result = mysqli_query($db, 'SELECT * FROM `orders` WHERE `id_order`=' . $_COOKIE['cart_id']);
		$date_order = mysqli_fetch_assoc($result);
		$sum_without_delivery = $date_order['sum'];
		//получим из orders сумму за доставку и использованный промокод
		$delivery_sum = $date_order['delivery_sum'];
		$promocode_used = $date_order['promocode_used'];
		$date_order = $date_order['date_order'];
		$result = mysqli_query($db, 'SELECT COUNT(*) FROM `orders_buffer` WHERE `id_order`=' . $_COOKIE['cart_id']);
		$is_there = mysqli_fetch_row($result);
		$is_there = $is_there[0];
		//если вдруг человек несколько раз заполняет delivery
		if($is_there == 0){
			$query = 'INSERT INTO `orders_buffer` (`id`, `id_order`, `client_name`, `phone_number`, `dont_call`, `delivery_time`, `delivery_address`,
				`cash_or_card`, `order_sum`, `delivery_sum`, `with_change`, `change_from`, `order_date`, `promocode_used`) VALUES 
				(NULL, "' . $_COOKIE['cart_id'] . '", "' . $name . '", "' . $phone . '", ' . $is_dont_call . ', "' . 
				$delivery_time . '", "' . $adress . '", "0", "' . $sum_without_delivery . '", "' . $delivery_sum . '", "0", "", "' . $date_order . '", "' . $promocode_used . '")';
		}
		else {
			$query = 'UPDATE `orders_buffer` SET `client_name`="' . $name . '", `phone_number`="' . $phone . '", `dont_call`=' . 
				$is_dont_call . ', `delivery_time`="' . $delivery_time . '", `delivery_address`="' . $adress . '", `cash_or_card`=0, 
				`order_sum`="' . $sum_without_delivery . '", `delivery_sum`="' . $delivery_sum . 
				'", `with_change`=0, `change_from`="", `order_date`="' . $date_order . '", `promocode_used`="' . $promocode_used . '"  WHERE `id_order`=' . $_COOKIE['cart_id'];
		}
		
		mysqli_query($db, $query);
	
		mysqli_close($db);

		echo 'Успешно_записано';
	}
	
	//03_отправим смс пользователю для подтверждения номера телефона
	//отправка клиенту кода подтверждения посредством smsc.ru
	if(isset($_POST['phone'])){
		//echo 'hi';
		$login = "akav";
		$psw = "21031990Aa";
		$phones = $_POST['phone'];
		$url = 'http://smsc.ru/sys/send.php?login=';
		$rand = rand(1000, 9999);
		$mes = 'Код подтверждения: ' . $rand . ' mbk-delivery.ru';
		$url .= $login . '&psw=' . $psw . '&phones=' . $phones . '&mes=' . $mes . '&charset=utf-8';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		$sms_result = curl_exec($ch);
		curl_close($ch);
		
		//Сохраним в куки код сообщения чтобы на странице verify сравнить его с введенным пользователем
		setcookie("smc", md5($rand), time() + 120);
		
		//отладка
		//$fd = fopen('otlad.txt', 'w');
		//$text = $url . ' ' . $sms_result;
		//fwrite($fd, $text);
		//fclose($fd);
		unset($_POST['phone']);
	}
	
	//пользователь отправил на сервер код подтверждения телефона
	if(isset($_POST['sms_code'])){
		if(md5($_POST['sms_code']) == $_COOKIE['smc']){
			echo 'Верно';
		}
		else {
			echo 'Неверно';
		}
		unset($_POST['sms_code']);
	}


	//04_запрос на проверку улицы
	if(isset($_POST['street_check'])){
		//здесь перекдючается таблица, отвечающая за весь город, или район
		//$del_zone_table = 'all_city_delivery';
		$del_zone_table = 'moscow_district_delivery';

		//подключаемся к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		//проверяем есть ли полученный адрес в таблице доставки
		$query = 'SELECT COUNT(*) FROM `' . $del_zone_table . '` WHERE street="' . $_POST['street_check'] . '"';
		$res = mysqli_query($db, $query);
		$res = mysqli_fetch_row($res);
		$res = $res[0];
		mysqli_close($db);

		//проверим улицу в таблице
		if($res != 0)
			echo 'Адрес в пределах';
		else {
			if($del_zone_table == "all_city_delivery")
				echo 'Адрес за пределами город';
			else
				echo 'Адрес за пределами район';
		}

		unset($_POST['street_check']);
	}



	//ЗАПРОС НА ЗАПИСЬ В ТАБЛИЦУ УЛИЦ
/*	if(isset($_POST['next']))
	{
		//$fd = fopen("all_city_delivery.csv", "a");
		//$text = $_POST['nex'] . "," . $_POST['next'];
		//fwrite($fd, $text);
		//fclose($fd);

		//подключаемся к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();

		$query = 'INSERT INTO `moscow_district_delivery` VALUES("", "' . $_POST['next'] . '")';
		mysqli_query($db, $query);
		mysqli_close($db);
		unset($_POST['next']);
		unset($_POST['nex']);
	}
*/
?>
