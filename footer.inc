<?php

//ОТПРАВКА СООБЩЕНИЯ ЧЕРЕЗ TELEGRAM БОТА
//токен бота
//define('TELEGRAM_TOKEN', '821301116:AAEWf8hrRBU4RzKYIrVIxbu7mqVVDGzjzJY');
//внутренний id
//define('TELEGRAM_CHATID', '653031105');

//функция отправляющая сообщение
/*function message_to_telegram($text)
{
	$ch = curl_init();
	curl_setopt_array(
		$ch,
		array(
			CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/sendMessage',
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_POSTFIELDS => array(
				'chat_id' => TELEGRAM_CHATID,
				'text' => $text,
			),
		)
	);
	curl_exec($ch);
}*/

	
function footer($brand)
{
	//message_to_telegram('Привет');

	$rest_name_foot = 'Макдональдс';
	if($brand == 'burgerking')
		$rest_name_foot = 'Бургер Кинг';

	$footer = '
<div id="footer">
	<div id="footer_wrap">
	
		<div id="left_footer">
			<ul id="info_pages">
				<li class="info" id="kak_zak"><a href="/' . $brand . '/info/kak-sdelat-zakaz">Как заказать</a></li>
				<li class="info"><a href="/' . $brand . '/news/1">Новости</a></li>
				<li class="info"><a href="/' . $brand . '/info/dostavka-i-oplata">Доставка и оплата</a></li>
				<li class="info"><a href="/' . $brand . '/articles/1">Статьи</a></li>
				<li class="info"><a href="/' . $brand . '/info/voprosy-i-otvety">Вопросы и ответы</a></li>
			</ul>
		</div>
		<div id="right_footer">
			<h2 id="phone_number">8-495-407-76-99</h2>
			<span class="text_in_footer">Вы можете оформить заказ на сайте или по телефону.</span>
			<div id="social_icons_footer">
				<a href="/" title="MBK-Delivery Facebook">
					<div class="soc_icon" id="fb_i">
						<img src="/includes/for_all/footer/footer_icons/social_icons/facebook.png" alt="MBK-Delivery Facebook" class="soc_im" />
					</div>
				</a>
				<a href="/" title="MBK-Delivery VK">
					<div class="soc_icon" id="vk_i">
						<img src="/includes/for_all/footer/footer_icons/social_icons/vk.png" alt="MBK-Delivery VK" class="soc_im" />
					</div>
				</a>
				<a href="/" title="MBK-Delivery Instagram">
					<div class="soc_icon" id="instagram_i">
						<img src="/includes/for_all/footer/footer_icons/social_icons/instagram.png" alt="MBK-Delivery Instagram" class="soc_im" />
					</div>
				</a>
				<a href="/" title="MBK-Delivery Twitter">
					<div class="soc_icon" id="twitter_i">
						<img src="/includes/for_all/footer/footer_icons/social_icons/twitter.png" alt="MBK-Delivery Twitter" class="soc_im" />
					</div>
				</a>
				<a href="whatsapp://send?phone=+79206313614" title="Написать в Whatsapp">
					<div class="soc_icon" id="whatsapp_i">
						<img src="/includes/for_all/footer/footer_icons/social_icons/whatsapp.png" alt="MBK-Delivery Whatsapp" class="soc_im" />
					</div>
				</a>
				<a href="tg://resolve?domain=mbkdelivery" title="Написать в Telegram">
					<div class="soc_icon" id="telegram_i">
						<img src="/includes/for_all/footer/footer_icons/social_icons/telegram.png" alt="MBK-Delivery Telegram" class="soc_im" />
					</div>
				</a>
				<a href="viber://chat?number=79206313614" title="Написать в Viber">
					<div class="soc_icon" id="viber_i">
						<img src="/includes/for_all/footer/footer_icons/social_icons/viber.png" alt="MBK-Delivery Viber" class="soc_im" />
					</div>
				</a>
			</div>
		</div>
		
		<div class="empty"></div>
		<hr class="hr_footer" />
		
		<span class="copyright">2019 - MBK-Delivery - доставка из ' . $rest_name_foot . ' в Рязани.</span>
		<div class="empty"></div>
		
	</div>
</div>';

	return $footer;
}
?>
