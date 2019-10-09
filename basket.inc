<?php
	$basket_js = '<script src="/includes/for_order/01_basket/basket.js"></script>';

	function stage_basket($brand){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();


		//формирование запроса и сам запрос
		$query = 'SELECT * FROM `products_in_cart` WHERE cart_id="' . $_COOKIE['cart_id'] . '" AND brand="' . $brand . '"';
		$result = mysqli_query($db, $query);
	

		$products_num = mysqli_num_rows($result);				//число товаров в корзине

		//если в корзине нет товаров
		if($products_num == 0){
			//здесь формируется страница с пустой корзины
			$stage_basket = '<div id="basket_empty">Ваша корзина пуста</div>';
			$stage_basket .= '<div id="basket_empty_img"><img src="/includes/for_order/01_basket/big-basket.png"/></div>';
		}
		else {
			$stage_basket = '<div id="left_for_products">';
			
			$cart_total_money = 0;			//стоимость корзины без доставки
			$all_products_in_cart = 0;		//число продуктов в корзине
			//Получаем иформацию о товарах по их id и сохраняем в массиве items
			for($i = 0; $i < $products_num; $i++){
				//получаем информацию о товарах, находящихся в корзине
				$product_in_cart = mysqli_fetch_assoc($result);		//по каждому товару в корзине
				$all_products_in_cart += $product_in_cart['num'];
				//получаем всю информацию по данному продукту
				$query = 'SELECT * FROM `' . $brand . '_items` WHERE `id_item`="' . $product_in_cart['id_item'] . '"';
				$query_result = mysqli_query($db, $query);
				$current_product = mysqli_fetch_assoc($query_result);
				$cart_total_money += $current_product['price'] * $product_in_cart['num'];
				//продолжаем формировать html-код
				//if(($i + 1) != $products_num)
					$stage_basket .= '<div class="product_in_cart">';
				//else
				//	$stage_basket .= '<div class="product_in_cart" id="pic_mob_jac">';
				$stage_basket .= '
						<button class="del_x" name="' . $current_product['id_item'] . '" id="' . $brand . '_' . $current_product['id_item'] . 'x">x</button>
						<img src="/' . $current_product['img'] . '" class="product_in_cart_img" alt="' . $current_product['name'] . '"/>
						<div class="product_in_cart_name">' . $current_product['name'] . '
							<div class="product_in_cart_desc">' . mb_substr($current_product['description'], 0, 150) . '...</div>
						</div>
						<div class="product_in_cart_meta">
							<div class="picm_price">' . $current_product['price'] * $product_in_cart['num'] . ',00 Р</div>
							<div class="picm_for_one_price">' . $current_product['price'] . ' Р/шт</div>
							<div class="picm_add_del_buttons">
								<button class="picm_button_del" name="' . $current_product['id_item'] . '" id="' . $brand . '_' . $current_product['id_item'] . 'd">-</button>
								<div class="picm_quantity">' . $product_in_cart['num'] . '</div>
								<button class="picm_button_add" name="' . $current_product['id_item'] . '" id="' . $brand . '_' . $current_product['id_item'] . 'a">+</button>
								<div class="empty"></div>
							</div>
						</div>
						<div class="empty"></div>
					</div>';
			}
			
			$stage_basket .= '</div>';		//закрываем left_for_products
			$stage_basket .= '
			
				<div id="total_in_basket">
					<div id="order_total">Итого:<div id="total_money">' . ($cart_total_money + 129) . ',00 Р</div></div>
					<div id="for_products">Стоимость вашей корзины:<div id="products_money" class="total_money_fpd">' . $cart_total_money . ',00 Р</div></div>
					<div id="just_plus">+</div>
					<div id="for_delivery">Стоимость доставки:<div id="delivery_money" class="total_money_fpd">129,00 Р</div></div>
					<div id="just_minus">-</div>
					<div id="for_discount">Скидка:<div id="delivery_discount" class="total_money_fpd"></div></div>
					<div id="count_items_in">Всего товаров:<div class="total_money_fpd">' . $all_products_in_cart . ' шт</div></div>
					<div id="promokod_container">
						<div id="promokod_title">Промокод</div><div id="about_promo">?</div>
						<div class="empty"></div>
						<div id="promo_desc">Используйте если у вас есть промокод на скидку</div>
						<input id="promokod_input"></input>
						<div id="promokod_button">Применить</div>
						<div class="empty"></div>
						<div id="promokod_message"></div>
					</div>
					<div id="to_delivery_button">Оформить заказ</div>
				</div>
			
			<div class="empty"></div>';
		}
		
		//закрываем БД
		mysqli_free_result($result);
		mysqli_close($db);
		
		return $stage_basket;
	}
?>
