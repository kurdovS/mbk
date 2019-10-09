<?php
	$payment_js = '<script src="/includes/for_order/03_payment/payment.js"></script>';
	
	function stage_payment($brand){
		//ЗАПИШИМ В COOKIE ИЗ КАКОГО РЕСТОРАНА ЗАКАЗ
		setcookie('brand_name', $brand, time() + 259200, '/');
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		$query = 'SELECT * FROM `products_in_cart` WHERE `cart_id`="' . $_COOKIE['cart_id'] . '" AND `brand`="' . $brand . '"';
		$query_result = mysqli_query($db, $query);
		$num_prodInCart = mysqli_num_rows($query_result);
		$count_prodInCart = 0;
		for($i = 0; $i < $num_prodInCart; $i++){
			$prodInCart = mysqli_fetch_assoc($query_result);
			$count_prodInCart += $prodInCart['num'];
		}
		$query = 'SELECT * FROM `orders` WHERE `id_order`="' . $_COOKIE['cart_id'] . '"';
		$query_result = mysqli_query($db, $query);
		$order_row = mysqli_fetch_assoc($query_result);
		
		$stage_payment = '
<div id="order_info_payment">
	<h3 class="payment_h3">Информация о заказе</h3>
	<div id="your_order" class="payment_info_block">
		<span class="pib_span">Ваш заказ</span>
		<hr class="pib_hr" />
		<span class="pib_sp">Количество товаров в корзине: <div class="pib_sp_div">' . $count_prodInCart . '</div></span>
		<span class="pib_sp">Стоимость вашей корзины: <div class="pib_sp_div">' . $order_row['sum'] . ' Р</div></span>
		<span class="pib_sp">Стоимость доставки: <div class="pib_sp_div">' . $order_row['delivery_sum'] . ' Р</div></span>
	</div>
	<div id="total_payment" class="payment_info_block">
		<span class="pib_span">Итого</span>
		<hr class="pib_hr" />
		<span class="pib_sp">С учетом доставки: <div class="pib_sp_d">' . ($order_row['sum'] + $order_row['delivery_sum']) . ' Р</div></span>
	</div>
	<div class="empty"></div>
</div>

<div id="payment_method">
	<h3 class="payment_h3">Выберите способ оплаты</h3>
	
	<div id="with_cash" class="payment_method_block anim_mb">
		<div class="round_payment"><div class="rp_in"></div></div>
		<span class="pmb_span">Оплата наличными курьеру</span>
		<small class="small_pmb_span">В момент получения заказа</small>
		
		<div id="with_cash_bottom">
			<span class="with_cash_bottom_span">С какой суммы подготовить сдачу?</span>
			<input type="text" name="cash_change" id="payment_input">
			<div class="round_payment" id="without_change"><div class="rp_in"></div></div>
			<span class="with_cash_bottom_span">Без сдачи</span>
			<div class="empty"></div>
		</div>
	</div>
	<span class="payment_method_desc">Оплата курьеру наличными средствами во время получения заказа. Пожалуйста укажите с какой суммы приготовить сдачу.</span>
	<div class="empty"></div>
	
	<div id="with_card_online" class="payment_method_block anim_mb">
		<div class="round_payment"><div class="rp_in"></div></div>
		<span class="pmb_span">Оплата банковской картой онлайн</span>
		<small class="small_pmb_span">Вы будете перенаправлены на сервис ROBOKASSA</small>
		
		<div id="with_cash_bottom">
		</div>
	</div>
	<span class="payment_method_desc">Внимание сервис приема платежей может взимать дополнительную комиссию.</span>
	
	<div class="empty"></div>';
	
	
	//работа с ROBOKASSA
	$query = 'SELECT * FROM `orders` WHERE `id_order`="' . $_COOKIE['cart_id'] . '"';
	$query_result = mysqli_query($db, $query);
	mysqli_close($db);
	$order = mysqli_fetch_assoc($query_result);
	
	// регистрационная информация (Идентификатор магазина, пароль #1)
	// registration info (Merchant ID, password #1)
	$mrh_login = "mbk-delivery";
	$mrh_pass1 = "21031990Aakurdov7N";		//Настоящий Пароль #1
	//$mrh_pass1 = "21031990Qq";			//Тестовый Пароль #1
	
	// номер заказа
	// number of order
	//$inv_id = $order['id_order'];
	//$inv_id = '678678';
	$inv_id = '0';
	
	// описание заказа
	// order description
	$inv_desc = "Доставка MBK-Delivery";

	// сумма заказа
	// sum of order
	$out_summ = $order['sum'] + $order['delivery_sum'];
	//$out_summ = '100';

	// предлагаемая валюта платежа
	// default payment e-currency
	//$in_curr = "";

	// язык
	// language
	$culture = "ru";

	// кодировка
	// encoding
	$encoding = "utf-8"; 

	// Адрес электронной почты покупателя
	// E-mail
	$Email = "test@test.ru";
	
	//из какого ресторана
	$Shp_brand = $brand;
	
	//внутренний номер заказа
	$Shp_order = $order['id_order'];

	// Срок действия счёта
	// Expiration Date
	//$ExpirationDate = "2018-07-30T12:00";

	// формирование подписи
	// generate signature
	//$crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");
	$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_brand=$Shp_brand:Shp_order=$Shp_order");
	
	// указывает что это тестовый магазин
	$isTest = 1;
	
	$url = 'https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=' . $mrh_login .
    '&OutSum=' . $out_summ . '&InvoiceID=' . $inv_id . '&Description=' . $inv_desc . 
	'&SignatureValue=' . $crc . '&Shp_brand=' . $Shp_brand . '&Shp_order=' . $Shp_order;
	
	$stage_payment .= '
	<a href="' . $url . '"><div class="to_pay_button">Оформить заказ</div></a>
	<a href="/' . $brand . '/order/final"><div class="to_pay_button">Оформить заказ</div></a>
	<div class="empty"></div>
</div>';

		
		return $stage_payment;
	}
?>
