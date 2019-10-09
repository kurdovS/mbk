<?php

function head($brand)
{
	//выберем правильный цвет для соц. кнопок в зависимости от ресторана
	$social_img_pr = "";
	if($brand == 'burgerking')
		$social_img_pr = "_w";

	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
	$db = connectionDB();
	
	
	//запрос к БД 
	$query = 'SELECT `category_rus` FROM `' . $brand . '_items` GROUP BY `category_rus` ORDER BY `id_item`';
	$result = mysqli_query($db, $query);
	
	//запрос к БД 
	$query = 'SELECT `category` FROM `' . $brand . '_items` GROUP BY `category` ORDER BY `id_item`';
	$res = mysqli_query($db, $query);
	
	$categories_num = mysqli_num_rows($result);					//всего категорий товаров
	

	//название ресторана для заголовка страницы
	$rest_name_head = 'Макдональдс';
	if($brand == 'burgerking')
		$rest_name_head = 'Бургер Кинг';
	

	//ПЕРЕКЛЮЧАТЕЛЬ ЛОГО-ЗАГОЛОВКА
	$mbk_delivery = '<b id="mbk_delivery">MBK-Delivery</b>';
	$mbk_description = '<b id="mbk_description">Доставка из McDonalds, Burger King, KFC</b>';
	$header_message = $mbk_delivery . $mbk_description;
	//$header_message = '<b>MBK-Delivery</b> - Доставка из ' . $rest_name_head . ' в Рязани.';
	//$header_message = '<b>ВНИМАНИЕ:</b> Служба доставки временно не работает. Работа службы доставки будет возобновлена <b>07.07.2019</b>.';
	$header_message = '<b>ВНИМАНИЕ:</b> 11 июля 2019 - доставка только по Московскому району, Приокскому, Канищево.';


	//заголовок для инфо-надписи и режима работы
	$time_header = '
	
	<div id="time_header">
		<div id="wrp">
			<div id="timeh_left" class="timeh_in">
				<div id="logo_wrapper"><img src="/mbk_favicons/android-chrome-192x192.png" id="main_logo" alt="MBK-Delivery"></div>
				<div id="left_header_mess">' . $header_message . '</div>
			</div>
			<div id="timeh_right" class="timeh_in">
				<div id="timehr_in">
					<div id="withoutvac">РЕЖИМ РАБОТЫ:</div>
					<div id="elev">11:00 - 23:00</div>
					<div id="withoutvac">БЕЗ ВЫХОДНЫХ</div>	
				</div>
			</div>
		</div>
	</div>';


	//формируем код заголовка
	$head = $time_header . '

	<div id="top_header">
		<div class="width_restrict">
			<div id="mobile_burger">
				<img src="/includes/for_all/header/top_headers_icons/menu_button.png" id="menu_button" alt="Меню">
			</div>
			<ul id="restoraunts">
				<li class="restoraunts_li"><a href="/mcdonalds">
					<img src="/includes/for_all/header/top_headers_icons/mcdonalds.jpg" class="restoraunts_img" alt="McDonalds">
					<span class="restoraunts_span">McDonalds</span>
				</a></li>
				<li class="restoraunts_li"><a href="/burgerking">
					<img src="/includes/for_all/header/top_headers_icons/burgerking.jpg" class="restoraunts_img" alt="Burger King">
					<span class="restoraunts_span">Burger King</span>
				</a></li>
				<li class="restoraunts_li" id="kfc_li">
					<img src="/includes/for_all/header/top_headers_icons/kfc.jpg" class="restoraunts_img" alt="KFC">
					<span class="restoraunts_span">KFC</span>
					<div id="kfc_coming_soon">скоро</div>
				</li>
			</ul>
			<div id="social_icons_head">
				<a href="/' . $brand . '/info/svjazatsja-s-nami" title="Обратная связь">
					<div class="soc_icons" id="contacts_ic">
						<img src="/includes/for_all/header/top_headers_icons/social_icons/contacts' . $social_img_pr . '.png" alt="Обратная связь" class="soc_img" />
					</div>
				</a>
				<a href="/" title="MBK-Delivery Twitter">
					<div class="soc_icons" id="twitter_ic">
						<img src="/includes/for_all/header/top_headers_icons/social_icons/twitter' . $social_img_pr . '.png" alt="MBK-Delivery Twitter" class="soc_img" />
					</div>
				</a>
				<a href="/" title="MBK-Delivery VK">
					<div class="soc_icons" id="vk_ic">
						<img src="/includes/for_all/header/top_headers_icons/social_icons/vk' . $social_img_pr . '.png" alt="MBK-Delivery VK" class="soc_img" />
					</div>
				</a>
				<a href="/" title="MBK-Delivery Facebook">
					<div class="soc_icons" id="fb_ic">
						<img src="/includes/for_all/header/top_headers_icons/social_icons/facebook' . $social_img_pr . '.png" alt="MBK-Delivery Facebook" class="soc_img" />
					</div>
				</a>
				<a href="/" title="MBK-Delivery Instagram">
					<div class="soc_icons" id="instagram_ic">
						<img src="/includes/for_all/header/top_headers_icons/social_icons/instagram' . $social_img_pr . '.png" alt="MBK-Delivery Instagram" class="soc_img" />
					</div>
				</a>
			</div>		
		</div>
	</div>
	
	
	<div id="menu_for_mobile" class="animation out">
		<div id="mobile_head">
			<div id="mh_menu">MBK</div>
			<div id="close_mobile_head"><img src="/includes/for_all/header/top_headers_icons/close_w.png" id="close_img" alt="Закрыть меню"/></div>
		</div>
		<ul id="mobile_menu">';
					
	for($i = 0; $i < $categories_num; $i++){
		$category_rus = mysqli_fetch_row($result);
		$category_rus = mb_strtoupper($category_rus[0]);
		
		$category = mysqli_fetch_row($res);
		$category = $category[0];

		//изменим ссылки на категории если mcdonalds
		if($brand == 'mcdonalds')
			$brand_new = '';
		else
			$brand_new = $brand;

		
		$head .= '
<li class="mobile_menu_li" id="' . $category . '_category" onclick="category_push(this)">
	<a href="/' . $brand_new . '#' . $category . '">';

		if($brand == 'mcdonalds')
			$head .= '<img src="/includes/for_all/header/' . $brand . '_icons/revers_category' . ($i + 1) . '.png" class="category_img" alt="Категория меню"/>'
				. $category_rus;
		else
			$head .= $category_rus;

		$head .= '
	</a>
</li>';
	}

	//запрос к БД еще раз чтобы в bottom_header было то же
	$query = 'SELECT `category_rus` FROM `' . $brand . '_items` GROUP BY `category_rus` ORDER BY `id_item`';
	$result = mysqli_query($db, $query);
	$query = 'SELECT `category` FROM `' . $brand . '_items` GROUP BY `category` ORDER BY `id_item`';
	$res = mysqli_query($db, $query);
	
		$head .= '
				</ul>
				<ul id="info_pages_anchors">
					<li class="anchors_info"><a href="/' . $brand . '/info/kak-sdelat-zakaz">Как заказать</a></li>
					<li class="anchors_info"><a href="/' . $brand . '/info/dostavka-i-oplata">Доставка и оплата</a></li>
					<li class="anchors_info"><a href="/' . $brand . '/info/voprosy-i-otvety">Вопросы и ответы</a></li>
					<li class="anchors_info"><a href="/' . $brand . '/news/1">Новости</a></li>
					<li class="anchors_info"><a href="/' . $brand . '/articles/1">Статьи</a></li>
				</ul>
				<div id="to_phone_in_mm">
					<div id="phone_mm">8-495-407-76-99</div>
				</div>
				<div id="to_copyright">MBK-Delivery</div>
			</div>
			<a href="/' . $brand . '/order/basket"><div id="cart_for_mobile">
				<div id="total_mob"></div>
				<img src="/includes/for_all/header/top_headers_icons/mob_cart_white.png" id="mob_cart" alt="Мобильная корзина"/>
				<div id="product_count_mob"></div>
			</div></a>
	
	<div id="bottom_header">
		<div class="width_restrict">
			<ul id="main_menu">';
				
	for($i = 0; $i < $categories_num; $i++){
		$category_rus = mysqli_fetch_row($result);
		$category_rus = mb_strtoupper($category_rus[0]);
		
		//меняем длинные названия категорий в меню
		if($i == 1 && $category_rus == 'БУРГЕРЫ ИЗ КУРИЦЫ И РЫБЫ')
			$category_rus = 'КУРИЦА И РЫБА';
		else if($i == 0 && $category_rus == 'БУРГЕРЫ ИЗ ГОВЯДИНЫ')
			$category_rus = 'ГОВЯДИНА';
		
		$category = mysqli_fetch_row($res);
		$category = $category[0];
		
		$head .= '
<li class="menu_li"';
		if($i == ($categories_num - 1))
			$head .= ' id="for_bg_rm"';
		$head .= '
	><a href="/' . $brand_new . '#' . $category . '">';

		if($brand == 'mcdonalds')
			$head .= '<img src="/includes/for_all/header/' . $brand . '_icons/category' . ($i + 1) . '.png" class="category_img" alt="Категория"/>' 
			. $category_rus;
		else
			$head .= $category_rus;

		$head .= '
	</a>
</li>';
	
	}
	
	$head .= '
</ul>
<div id="cart_button">
	<div id="total_of_cart"></div>
	<img src="/includes/for_all/header/cart/cart.png" id="cart_button_img" alt="Корзина"/>
	<div id="count_items_round"></div>
	<div id="small_cart">
		<div id="order_button_wrap">
			<a href="/' . $brand . '/order/basket"><div id="order_button">Оформить заказ</div></a>
		</div>
		<div id="products_in_cart_wrap"></div>
		<div id="clear_button_wrap">
			<button id="clear_cart_button" onclick="cart_clear(this)">Очистить корзину</button>
		</div>
	</div>
</div>
</div>
</div>';

	//удаляем результат запроса и закрываем соединение с БД
	mysqli_free_result($result);
	mysqli_close($db);
	
	return $head;
}

?>
