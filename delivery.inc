<?php
	$yandex_map = '
<script src="https://api-maps.yandex.ru/2.1.65/?lang=ru_RU" type="text/javascript"></script>
<script src="/includes/for_order/02_delivery/yaMap.js"></script>
<script src="/includes/for_order/02_delivery/provider.js"></script>';


	$delivery_js = '<script src="/includes/for_order/02_delivery/delivery.js"></script>
	<script src = "/includes/for_order/02_delivery/phone_input.js"></script>';


	function stage_delivery($brand){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//если клиент уже вводил данные в delivery вставим их из БД
		$res = mysqli_query($db, 'SELECT * FROM `orders_buffer` WHERE `id_order`=' . $_COOKIE['cart_id']);
		$need_paste = mysqli_num_rows($res);
		//клиент уже вводил данные в delivery
		if($need_paste != 0){
			$order_in_process = mysqli_fetch_assoc($res);
			$name = $order_in_process['client_name'];
			$phone = $order_in_process['phone_number'];
			$time_delivery = $order_in_process['delivery_time'];
			//разбираем адрес
			$address = $order_in_process['delivery_address'];
			$street = mb_substr($address, 0, mb_strpos($address, ' д.'));
			$address = mb_substr($address, (mb_strpos($address, ' д.') + 3));
			$home = mb_substr($address, 0, mb_strpos($address, ' '));
			$address = mb_substr($address, mb_strpos($address, 'к.') + 2);
			$corpus = mb_substr($address, 0, mb_strpos($address, ' '));
			$address = mb_substr($address, mb_strpos($address, 'стр.') + 4);
			$build = mb_substr($address, 0, mb_strpos($address, ' '));
			$address = mb_substr($address, mb_strpos($address, 'подъезд.') + 8);
			$entrance = mb_substr($address, 0, mb_strpos($address, ' '));
			$address = mb_substr($address, mb_strpos($address, 'этаж.') + 5);
			$floor = mb_substr($address, 0, mb_strpos($address, ' '));
			$address = mb_substr($address, mb_strpos($address, 'кв.') + 3);
			$apartment = (int)($address);
			
			//отображать кнопку без подтверждения смс-кода
			$display = 'block';
			
			if($order_in_process['dont_call']){
				$checked = ' checked';
				$check = ' check';
			}
			else {
				$checked = '';
				$check = '';
			}
		}
		else {
			$name = '';
			$phone = '';
			$time_delivery = '';
			$street = '';
			$home = '';
			
			//адрес
			$street = '';
			$home = '';
			$corpus = '';
			$build = '';
			$entrance = '';
			$floor = '';
			$apartment = '';
			
			//отображать кнопку с подтверждением смс-кода
			$display = '';
			
			$checked = '';
			$check = '';
		}
		
		mysqli_close($db);
		
		//формируем форму с контактными данными
		$stage_delivery = '


	<h3 class="delivery_h3">Контактные данные и время доставки</h3>
	<div id="contact_data" class="anim_sf">
		<div class="delivery_input">
			<label for="name">Имя *</label>
			<input type="text" name="name" class="input_in_order" required value="' . $name . '" />
		</div>
					
		<div class="delivery_input">
			<label for="phone">Телефон *</label>
			<input type="tel" name="phone" class="input_in_order" autocomplete="off" required value="' . $phone . '" />
			<img src="/includes/for_order/02_delivery/ok.png" id="ok_img"/>
			
			<div id="sms_verify">
				<label for="sms_code">Отправлен SMS-код для подтверждения.</label>
				<div class="empty"></div>
				<input type="text" name="sms_code" id="sms_input" placeholder="Код" />
				<button id="sms_button">Подтвердить</button>
				<img src="/includes/for_order/02_delivery/clock-small.png" id="clock_small" alt="Часы">
				<div id="watches">
					<div id="watches_minutes" class="in_watches">02</div>
					<div id="delimer" class="in_watches">:</div>
					<div id="watches_seconds" class="in_watches">00</div>
				</div>
			</div>
			
			<div id="didnt_sms">
				<h2>SMS-код больше не работает</h2>
				Истек срок действия пароля или пароль введен несколько раз неправильно.
				<div id="repeat_sms_code" tabindex="-1">Получить новый SMS-код</div>
			</div>
			
		</div>
					
		<div class="delivery_switch">
			<div class="checkbox">
				<div class="round_delivery' . $checked . '"><div class="rd_in' . $check . '"></div></div>
				<label for="dont_call" id="dont_call_label"> Не звонить</label>
			</div>
			<div id="dont_call_info">Выберите этот пункт и мы не будем вам звонить без крайней необходимости</div>
		</div>
		<div class="empty"></div>
		
					
		<div id="deliv_time">
			<label for="time" id="deliv_time_label">Время доставки ЧЧ:ММ [<i>оставьте пустым, если заказ на ближайшее время</i>]</label>
			<input type="time" name="time" id="time_input" placeholder="Как можно скорее" value="' . $time_delivery . '" min="11:00" max="23:59">
		</div>
		<div class="empty"></div>
	</div>
	
	<div id="err_mes">
		Время работы службы доставки: с 11:00 до 23:00
	</div>

	
	<h3 class="delivery_h3">Адрес доставки</h3>
	<div id="order_addr">	
		<div id="left_addr" class="side_addr">	
			<span class="order_addr_desc">Вы можете указать дом на карте</span>
			<div id="map"></div>
		</div>
			
		<div id="right_addr" class="side_addr">
			<span class="order_addr_desc" id="street_span">Улица *</span>
			<input id="suggest_street" type="text" name="street" class="input_in_address" required value="' . $street . '">
							
			<div class="small_delivery_block">
				<label for="home">Дом *</label>
				<input type="text" name="home" class="small_input" required value="' . $home . '">
			</div>
							
			<div class="small_delivery_block">
				<label for="corpus">Корпус</label>
				<input type="text" name="corpus" class="small_input" value="' . $corpus . '">
			</div>
							
			<div class="small_delivery_block">
				<label for="build">Строение</label>
				<input type="text" name="build" class="small_input" value="' . $build . '">
			</div>
							
			<div class="small_delivery_block">
				<label for="entrance">Подъезд</label>
				<input type="text" name="entrance" class="small_input" value="' . $entrance . '">
			</div>
							
			<div class="small_delivery_block">
				<label for="floor">Этаж</label>
				<input type="text" name="floor" class="small_input" value="' . $floor . '">
			</div>
							
			<div class="small_delivery_block">
				<label for="apartment">Кв. / офис *</label>
				<input type="text" name="apartment" class="small_input" required value="' . $apartment . '">
			</div>
			<div class="empty"></div>
			
			<div id="to_payment_button">
				<div class="to_pay_button">К выбору способа оплаты</div>
				<div class="to_pay_button" name="' . $display . '" tabindex="-1">Продолжить</div>
			</div>
		</div>
		
		<div class="empty"></div>
			
	</div>';

		return $stage_delivery;
	
	}
?>
