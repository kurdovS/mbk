<?php
	$final_js = '<script src="/includes/for_order/04_final/final.js"></script>';

	//подключаем файл с функцией для отправки viber сообщений
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/viber_message.inc');


	function stage_final($brand){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получаем из БД все о заказе
		$query = 'SELECT * FROM `orders_buffer` WHERE `id_order`="' . $_COOKIE['cart_id'] . '"';
		$result = mysqli_query($db, $query);
		$order = mysqli_fetch_assoc($result);
		
		//текст о состоянии заказа
		$order_result = '';
		
		//проверим оплачен ли уже заказ или нет
		if($order['paid']){	
			$mrh_pass1 = '21031990Aakurdov7N';			//Настоящий Пароль#1
			//$mrh_pass1 = '21031990Qq';				//Тестовый Пароль#1

			//база для расчёта контрольной суммы(SignatureValue): OutSum:InvId:Пароль#1:$Shp_brand
			$sg = strtoupper(md5($_POST['OutSum'] . ':' . $_POST['InvId'] . ':' . $mrh_pass1 . ':Shp_brand=' . $_POST['Shp_brand'] . ':Shp_order=' . $_POST['Shp_order']));
			
			if($sg == strtoupper($_POST['SignatureValue']))
				//01_формируем html-код
				$order_result = "Ваш заказ <b>№" . $_COOKIE['cart_id'] . '</b> оформлен и оплачен.';
			else
				//01_формируем html-код
				$order_result = "Ваш заказ <b>№" . $_COOKIE['cart_id'] . '</b> оформлен.';
		}
		else if($order['cash_or_card']){
			//01_формируем html-код
			$order_result = "Ваш заказ <b>№" . $_COOKIE['cart_id'] . '</b> оформлен.';
		}
		
		
		//ФОРМИРУЕМ КОД СТРАНИЦЫ
		$stage_final = '<div id="congrat">Спасибо!</div>';
		$stage_final .= '<div id="congrat_desc">' . $order_result;
		//if(!$order['dont_call'])
		//	$stage_final .= ' Мы свяжемся с вами в ближайшее время.</div>';
		//else
			$stage_final .= ' Курьер уже приступил к выполнению вашего заказа..</div>';
		$stage_final .= '<a href="/' . $brand . '"><div id="back_to_shop">Вернуться к каталогу</div></a>';
		
		
		//нужно отправить смс клиенту о том что заказ оформлен и курьеру со всей информацией для доставки
		if($order_result != ''){
			//смс для клиента 01
			//ПОКА ОТКЛЮЧИМ СМС ДЛЯ КЛИЕНТА ЧТОБЫ НЕ ТРАТИТЬ ДЕНЬГИ / ПЕРЕД ЗАПУСКОМ ВКЛЮЧИТЬ
			$login = "akav";
			$psw = "21031990Aa";
			$phones = $order['phone_number'];
			$url = 'http://smsc.ru/sys/send.php?login=';
			$mes = 'Ваш заказ №' . $order['id_order'] . ' на сумму ' . ($order['order_sum'] + $order['delivery_sum']) . 'р оформлен. Ожидайте курьера';
			$url .= $login . '&psw=' . $psw . '&phones=' . $phones . '&mes=' . $mes . '&charset=utf-8';
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			$sms_result = curl_exec($ch);
			curl_close($ch);
		
		
			//СМСКИ ДЛЯ КУРЬЕРА
			//смс для курьера с информацией о заказе 02
			$login = "akav";
			$psw = "21031990Aa";
			$phones = '89308741027';
			$url = 'http://smsc.ru/sys/send.php?login=';
		
			$mes = '№' . $order['id_order'] . ' ' . $order['client_name'] . ' ' . $order['phone_number'] . ' ';
			//если установлена галочка "Не звонить"
			if($order['dont_call'])
				$mes .= 'НЗ ';
			$mes .= $order['delivery_time'] . ' ' . $order['order_date'] . ' ';
			//если оплата наличными узнаем нужна ли сдача и с какой суммы
			if($order['cash_or_card']){
				$mes .= 'кэш ';
				//если нужна сдача то укажем с какой суммы
				if($order['with_change'])
					$mes .= $order['change_from'] . 'р ';
				else
					$mes .= 'БC ';
			
			}
			else {	//если оплата картой
				$mes .= 'онлайн ';
				if($order['paid'])
					$mes .= 'оплачен';
				else
					$mes .= 'не оплачен';		
			}
			$mes .= ' | ' . $order['delivery_address'];
		
			$url .= $login . '&psw=' . $psw . '&phones=' . $phones . '&mes=№' . $order['id_order'] . '&charset=utf-8';
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			$sms_result = curl_exec($ch);
			curl_close($ch);
			//отправим сообшение курьеру в viber
			viber_message($mes);

			
			//смс для курьера с информацией о составе заказа 03
			//ПРОВЕРИМ СЛУЧАЙ ЕСЛИ ОПЛАТА ОНЛАЙН И РЕСТОРАН НЕ MCDONALDS
			if($_COOKIE['brand_name'] == 'burgerking' || $_COOKIE['brand_name'] == 'kfc'){
				$brand = $_COOKIE['brand_name'];
			}
			if($brand == 'mcdonalds')
				$br = 'MAC';
			else if($brand == 'burgerking')
				$br = 'BK';
			else if($brand == 'kfc')
				$br = 'kfc';
			$query = 'SELECT * FROM `products_in_cart` WHERE `cart_id`="' . $_COOKIE['cart_id'] . '" AND `brand`="' . $brand . '"';
			$query_result = mysqli_query($db, $query);
			$num_prodInCart = mysqli_num_rows($query_result);
		
			$login = "akav";
			$psw = "21031990Aa";
			$phones = '89206313614';
			$url = 'http://smsc.ru/sys/send.php?login=';
			$mes = '№' . $order['id_order'] . ' = ' . $order['order_sum'] . 'р ' . $order['delivery_sum'] . 'р = ' . ($order['order_sum'] + $order['delivery_sum']) . 'р : ' . $br . ' : ';
			//для каждого продукта
			for($i = 0; $i < $num_prodInCart; $i++){
				$prodInCart = mysqli_fetch_assoc($query_result);
				$res = mysqli_query($db, 'SELECT * FROM ' . $brand . '_items WHERE `id_item`=' . $prodInCart['id_item']);
				$productInfo = mysqli_fetch_assoc($res);
			
				if($brand == 'mcdonalds')
					$mes .= $productInfo['name'] . '=' . $prodInCart['num'] . ' | ';
				else if($brand == 'burgerking')
					$mes .= $productInfo['name'] . ' ' . $productInfo['vol'] . '=' . $prodInCart['num'] . ' | ';
			}
			$url .= $login . '&psw=' . $psw . '&phones=' . $phones . '&mes=' . $mes . '&charset=utf-8';
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
			//$sms_result = curl_exec($ch);
			curl_close($ch);
			//отправим сообщение курьеру в viber
			viber_message($mes);

		
			//03_нужно удалить заказ из таблицы order и удалить все записи из products_in_cart для данного заказа
			mysqli_query($db, 'DELETE FROM `products_in_cart` WHERE `cart_id`=' . $_COOKIE['cart_id']);
			mysqli_query($db, 'DELETE FROM `orders` WHERE `id_order`=' . $_COOKIE['cart_id']);
			//04_удалим промокод или уменьшим количество его применений если он был использован в данном заказе
			$promocode_used = mysqli_query($db, 'SELECT `promocode_used` FROM `orders_buffer` WHERE id_order=' . $_COOKIE['cart_id']);
			$promocode_used = mysqli_fetch_row($promocode_used);
			$promocode_used = $promocode_used[0];
			//проверим есть ли такой промокод в таблице промокодов
			$promocode_exist = mysqli_query($db, 'SELECT COUNT(*) FROM `promocodes` WHERE promocode="' . $promocode_used . '"');
			$promocode_exist = mysqli_fetch_row($promocode_exist);
			$promocode_exist = $promocode_exist[0];
			if($promocode_exist){
				$this_promocode = mysqli_query($db, 'SELECT * FROM `promocodes` WHERE promocode="' . $promocode_used . '"');
				$this_promocode = mysqli_fetch_assoc($this_promocode);
				//если промокод одноразовый, то удаляем его
				if($this_promocode['type'] == 0 || $this_promocode['num'] == 1){
					mysqli_query($db, 'DELETE FROM `promocodes` WHERE `promocode`="' . $promocode_used . '"');
				}
				//если промокод многоразовый, то уменьшим число возможных его применений
				else {
					mysqli_query($db, 'UPDATE `promocodes` SET `num`=' . --$this_promocode['num'] . ' WHERE `promocode`="' . $promocode_used . '"');
				}
			}
			//05_перенесем всю информацию из `orders_buffer` в `orders_in_process`
			$query = 'INSERT INTO `orders_in_process` (id_order, client_name, phone_number, dont_call, ' .
				'delivery_time, delivery_address, cash_or_card, order_sum, delivery_sum, with_change, ' .
				'change_from, order_date, paid, promocode_used) SELECT id_order, client_name, phone_number, ' .
				'dont_call, delivery_time, delivery_address, cash_or_card, order_sum, delivery_sum, ' .
				'with_change, change_from, order_date, paid, promocode_used FROM `orders_buffer` WHERE ' .
				'`id_order`=' . $order['id_order'];
			mysqli_query($db, $query);
			//удалим из `orders_buffer` информацию о заказе
			mysqli_query($db, 'DELETE FROM `orders_buffer` WHERE `id_order` = ' . $order['id_order']);

			setcookie("cart_id", "", time() - 3600, "/");
			setcookie("brand_name", "", time() - 3600, "/");
		
			mysqli_close($db);
		}
		
		return $stage_final;
	}
?>
