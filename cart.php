<?php
	
	/////////////////////////////////////////////////////////////////////
	////////////////...............ЗАПРОСЫ............../////////////////
	/////////////////////////////////////////////////////////////////////
	
	//01_запрос на генерацию нового id для корзины
	if(isset($_POST['generate_new_cart_id'])){
		mt_srand(time());
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		$res = 1;
		
		while($res){
			$cart_id = mt_rand(100000, 999999);
			$res = mysqli_query($db, 'SELECT COUNT(*) FROM `orders` WHERE id_order="' . $cart_id . '"');
			$res = mysqli_fetch_row($res);
			$res = $res[0];
		}
		mysqli_close($db);
		
		//записываем id корзины в куки
		if(!isset($_COOKIE['cart_id'])){
			setcookie('cart_id', (integer)$cart_id, time() + 259200, '/');
			//проверим нет ли уже корзины с таким id
			//......................
		}
		echo $cart_id;					//Отправляем id для корзины
	}
	
	//02_запрос на инициализацию корзины с указанным id
	if(isset($_POST['cart_id'])){
		if(!isset($_POST['add_item']) && !isset($_POST['del_item']) && !isset($_POST['cart_clear']) && !isset($_POST['how_much_is_it']) && !isset($_POST['del_item_from_cart']))
			echo cart_initialization($_POST['cart_id'], $_POST['brand']);
	}
	
	//03_запрос на добавление товара в корзину
	if(isset($_POST['add_item'])){
		echo add_item($_POST['add_item'], $_POST['cart_id'], $_POST['brand']);
	}
	
	//04_запрос на удаление товара из корзины
	if(isset($_POST['del_item'])){
		echo del_item($_POST['del_item'], $_POST['cart_id'], $_POST['brand']);
	}
	
	//05_запрос на очистку корзины
	if(isset($_POST['cart_clear'])){
		echo clear_cart($_POST['cart_id'], $_POST['brand']);
	}
	
	//06_запрос на общую стоимость корзины
	if(isset($_POST['how_much_is_it'])){
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		
		//Получим стоимость корзины
		$query_result = mysqli_query($db, 'SELECT `sum` FROM `orders` WHERE `id_order`=' . $_POST['id']);
		$query_result = mysqli_fetch_row($query_result);
		$tot = $query_result[0];
		
		mysqli_close($db);
		unset($_POST['how_much_is_it']);
		echo number_format($tot, 2, ',', '') . ' &#8381';
	}
	
	
	//07_запрос на полное удаление товара из корзины (для order/basket)
	if(isset($_POST['del_item_from_cart'])){
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		
		//Удалим товар из БД products_in_order
		mysqli_query($db, 'DELETE FROM `products_in_cart` WHERE cart_id="' . $_POST['cart_id'] . '" AND id_item="' . $_POST['del_item_from_cart'] . '" AND `brand`="' . $_POST['brand'] . '"');
		mysqli_close($db);
		unset($_POST['del_item_from_cart']);
		echo cart_initialization($_POST['cart_id'], $_POST['brand']);
	}
	
	
	//РЕШЕНО ПО ДРУГОМУ ОСТАВИЛ НА ВСЯКИЙ СЛУЧАЙ
	//07_запрос на количество товаров в корзине
	/*if(isset($_POST['count_items_round'])){
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		
		//получим число продуктов в корзине
		$query_result = mysqli_query($db, 'SELECT COUNT(*) FROM `products_in_cart` WHERE `cart_id`=' 
									. $_POST['id'] . ' AND `brand`="' . $_POST['brand'] . '"');
		$query_result = mysqli_fetch_row($query_result);
		$count_items = $query_result[0];
		
		mysqli_close($db);
		unset($_POST['count_items_round']);
		echo $count_items;
	}*/
	
	
	
	/////////////////////////////////////////////////////////////////////
	////////////////...............ФУНКЦИИ............../////////////////
	/////////////////////////////////////////////////////////////////////
	
	//сервисная функция - возвращает содержимое корзины для указанного id корзины
	function cart_initialization($cart_id, $brand){
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		
		//Получим все товары в БД из корзины с данным id
		$query = 'SELECT * FROM `products_in_cart` WHERE cart_id="' . $cart_id . '" AND brand="' . $brand . '"';
		$result = mysqli_query($db, $query);
		
		//Формируем ответ - HTML код с товарами для корзины
		$cart_str_return = '';
		$total = 0;				//Общая сумма(стоимость) всех товаров корзины
		
		//Если данная корзина уже существует
		if($result){
			$products_in_cart_num = mysqli_num_rows($result);
			
			for($i = 0; $i < $products_in_cart_num; $i++){
				//получаем информацию о товарах, находящихся в корзине
				$products_in_cart_row = mysqli_fetch_assoc($result);
				$id_item = $products_in_cart_row['id_item'];
			
				//из таблицы товаров получаем инфу о продукте
				$query_result = mysqli_query($db, 'SELECT * FROM ' . $brand . '_items WHERE id_item=' . $id_item);
				$item = mysqli_fetch_assoc($query_result);
			
				//формируем html-код
				$cart_str_return .= '
				<div class="item_in_cart">
					<img src="/' . $item['img'] . '" class="item_in_cart_img" />
					<span class="item_in_cart_descript">' . mb_substr($item['name'], 0, 14) . '...</span>
					<div class="item_add_del">
						<button class="ad_buttons_in_cart in_item_add_del" onclick="del_item(this)" name="' . $item['id_item'] . '" id="' . $brand . '_' . $item['id_item'] . 'd">-</button>
						<div class="counter_item in_item_add_del">' . $products_in_cart_row['num'] . '</div>
						<button class="ad_buttons_in_cart in_item_add_del" onclick="add_item(this)" name="' . $item['id_item'] . '" id="' . $brand . '_' . $item['id_item'] . 'a">+</button>
					</div>
					<span class="item_in_cart_price">' . ($item['price'] * $products_in_cart_row['num']) . '&#8381</span>
				</div>';
			
				//добавим стоимость продукта * на его количество к стоимости всей корзины
				$total += $item['price'] * $products_in_cart_row['num'];
			}
		}
		
		
		//записываем данные о корзине в БД
		$query_result = mysqli_query($db, 'SELECT COUNT(*) FROM `orders` WHERE `id_order`=' . $cart_id);
		$res = mysqli_fetch_row($query_result);
		
		if($res[0] == 1){
			$ord_res = mysqli_query($db, 'SELECT * FROM `orders` WHERE id_order=' . $cart_id);
			$order_cur = mysqli_fetch_assoc($ord_res);
			if($order_cur['promocode_used'] == undefined)
				$order_cur['promocode_used'] = '';

			mysqli_query($db, 'UPDATE `orders` SET id_order=' . $cart_id . ', date_order=\'' . date("Y-m-d") . '\', sum=' . $total . 
				', delivery_sum=' . $order_cur['delivery_sum'] . ', promocode_used=\'' . $order_cur['promocode_used'] . 
				'\' WHERE id_order=' . $cart_id);

		}
		else
			mysqli_query($db, 'INSERT INTO `orders` VALUES (' . $cart_id . ', 1, \'' . date("Y-m-d") . '\', ' . $total . ', 129, "")');
		
		//закрываем соединение с БД
		unset($query_result);
		unset($result);
		mysqli_close($db);
		
		return $cart_str_return;					//Возвращаем содержимое указанной корзины
	}
	
	
	//сервисная функция, добавляет товар в корзину
	function add_item($item_id, $cart_id, $brand){
		//Работа с БД
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		$query = 'SELECT * FROM `products_in_cart` WHERE cart_id="' . $cart_id . '" AND id_item=' . $item_id . ' AND brand="' . $brand . '"';
		$result = mysqli_query($db, $query);
		$num_rows = mysqli_num_rows($result);
		
		//Если в корзине уже имеется данный товар, то увеличим его количество на 1
		if($num_rows > 0){
			$product = mysqli_fetch_assoc($result);
			$num_in_cart = $product['num'];
			$query_result = mysqli_query($db, 'UPDATE `products_in_cart` SET `num`=' . (++$num_in_cart) . ' WHERE `cart_id`="' . $cart_id . 
												'" AND `id_item`="' . $item_id . '" AND `brand`="' . $brand . '"');
		}
		//Если в корзине данный товар отсутствует, то добавить его 
		else {
			//Добавим товар в БД orders_items
			$query = 'INSERT INTO `products_in_cart` (`cart_id`, `id_item`, `num`, `brand`, `date`) VALUES (' . $cart_id . ', ' . $item_id . ', 1, "' . $brand .  '", "' . date("d.m.Y") . '")';
			mysqli_query($db, $query);
		}
	
		unset($result);
		mysqli_close($db);
		
		return cart_initialization($cart_id, $brand);
	}
	
	
	//сервисная функция, удаляет товар из корзины
	function del_item($item_id, $cart_id, $brand){
		//Работа с БД
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		$query = 'SELECT `num` FROM `products_in_cart` WHERE cart_id="' . $cart_id . '" AND id_item="' . $item_id . '" AND brand="' . $brand . '"';
		$result = mysqli_query($db, $query);
		
		//если в корзине есть данный товар
		$is_item_there = mysqli_fetch_row($result);
			
		//Если в корзине несколько единиц данного товара, то уменьшаем его количество на 1
		if($is_item_there[0] > 1){	
			$query_result = mysqli_query($db, 'UPDATE `products_in_cart` SET `num`=' . (--$is_item_there[0]) . ' WHERE `cart_id`="' . $cart_id . 
											'" AND `id_item`="' . $item_id . '" AND `brand`="' . $brand . '"');
		}
		//Если в корзине товар в одном экземпляре, то удалить его 
		else {
			//Удалим товар из БД products_in_order
			mysqli_query($db, 'DELETE FROM `products_in_cart` WHERE cart_id="' . $cart_id . '" AND id_item="' . $item_id . '" AND `brand`="' . $brand . '"');
		}
		
		unset($result);
		unset($query_result);
		mysqli_close($db);
	
		return cart_initialization($cart_id, $brand);
	}
	
	
	//сервисная функция, очищает корзину
	function clear_cart($cart_id, $brand){
		//Работа с БД
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		$query_result = mysqli_query($db, 'DELETE FROM `products_in_cart` WHERE `cart_id`=' . $cart_id . ' AND `brand`="' . $brand . '"');
		echo $query_result == false;
		unset($query_result);
		mysqli_close($db);
		
		return cart_initialization($cart_id, $brand);
	}
?>
