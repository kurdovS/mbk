<?php

function products_catalog($brand)
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	$products_catalog = '<div id="menu">';

	//01_Узнаем количество категорий продуктов
	$query = 'SELECT * FROM `' . $brand . '_items` GROUP BY `category` ORDER BY `id_item`';
	$res = mysqli_query($db, $query);
	$category_num = mysqli_num_rows($res);	//количество категорий продуктов
	$result;
	
	
	
	
	//для каждой категории продуктов
	for($i = 0; $i < $category_num; $i++){
		//получим имя данной категории
		$category_row = mysqli_fetch_assoc($res);
		$category_name = $category_row['category'];
		
		//получим id первого продукта в данной категории
		$id_first_product_in_category = $category_row['id_item'];
		
		
		//получим все продукты в данной категории
		$query = 'SELECT * FROM `' . $brand . '_items` WHERE category="' . $category_name . '" LIMIT 0, 256';
		$result = mysqli_query($db, $query);
		//число продуктов в данной категории
		$products_num = mysqli_num_rows($result);
		
		$products_catalog .= '<div id="' . $category_name . '_div" class="category_div">';
		$products_catalog .= '<div class="category" id="' . $category_name . '">' . $category_row['category_rus'] . '</div>';
		
			
		//для каждого продукта в данной категории
		for($j = 0; $j < $products_num; $j++){
			//получаем запись в таблице для данного продукта
			$product_row = mysqli_fetch_assoc($result);
			
			
			//узнаем имеет ли продукт различный объем
			$is_volume = $product_row['volume'];
			
			//получим имя продукта, ссылку на изображение продукта и цену
			$product_name = $product_row['name'];
			$product_img = $product_row['img'];
			$product_price = $product_row['price'];
			$product_id = $product_row['id_item'];
			//получим транслит имени для ссылки
			$product_translit = $product_row['name_translit'];
			
			$product_in_cart_num = 0;
			//получаем из БД количество данного продукта в корзине
			if(isset($_COOKIE['cart_id'])){
				$pin_num_result = mysqli_query($db, 'SELECT `num` FROM `products_in_cart` WHERE `cart_id`=' . $_COOKIE['cart_id'] . 
													' AND `id_item`=' . $product_id . ' AND `brand`="' . $brand . '"');
		
				if($pin_num_result)
					$product_in_cart_num = mysqli_fetch_row($pin_num_result)[0];
				if($product_in_cart_num == '')
					$product_in_cart_num = 0;
			}
			
			//0B_продукт не имеет различный объем
			if($is_volume == 0){
				$products_catalog .= '<div class="product_block">';
				$products_catalog .= '<a href="/' . $brand . '/product/' . $product_translit . '">
										<img src="/' . $product_img . '" class="product_img" alt="' . $product_name . '"/>
										<div class="product_name">' . $product_name . '</div></a>
	
										<div class="del_add_buttons">
											<button class="del_button da_buttons" onclick="del_item(this)" name="' . $product_id . '" id="' . $brand . '_' . $product_id . 'd">-</button>
											<div class="quantity" id="quantity' . $product_id . '">' . $product_in_cart_num . '</div>
											<button class="add_button da_buttons" onclick="add_item(this)" name="' . $product_id . '" id="' . $brand . '_' . $product_id . 'a">+</button>
										</div>
	
										<div class="product_price">' . $product_price . ' &#8381;</div>
										<div class="clear_div"></div>';
			}
			
			
			
			
			//0А_продукт имеет различный объем
			else {
				$products_catalog .= '<div class="product_block with_volume">';
				$products_catalog .= '<div class="block_for_product_with_volume">
											<a href="/' . $brand . '/product/' . $product_translit . '">
											<img src="/' . $product_img . '" class="product_img" alt="' . $product_name . '"/>
											<div class="product_name">' . $product_name . '</div></a>
	
											<div class="del_add_buttons">
												<button class="del_button da_buttons" onclick="del_item(this)" name="' . $product_id . '" id="' . $brand . '_' . $product_id . 'd">-</button>
												<div class="quantity" id="quantity' . $product_id . '">' . $product_in_cart_num . '</div>
												<button class="add_button da_buttons" onclick="add_item(this)" name="' . $product_id . '" id="' . $brand . '_' . $product_id . 'a">+</button>
											</div>
	
											<div class="product_price">' . $product_price . ' &#8381;</div>
											<div class="clear_div"></div>
										</div>';
				
				//число объемов данного продукта
				$num_of_volume = $is_volume;
				
				//храним в массиве объемы для переключателей объемов
				$product_volume = array('', '', '', '', '');
				$product_volume[0] = (string)$product_row['vol'];
				$s = 1;
				
				//цикл по объемам
				while($num_of_volume-- > 1){
					//берем следующий продукт
					$product_row = mysqli_fetch_assoc($result);
					
					//получим имя продукта, ссылку на изображение продукта и цену, а также объем продукта
					$product_name = $product_row['name'];
					$product_img = $product_row['img'];
					$product_price = $product_row['price'];
					$product_id = $product_row['id_item'];
					$product_volume[$s] = (string)$product_row['vol'];
					
					$product_in_cart_num = 0;
					//получаем из БД количество данного продукта в корзине
					if(isset($_COOKIE['cart_id'])){
						$pin_num_result = mysqli_query($db, 'SELECT `num` FROM `products_in_cart` WHERE `cart_id`=' . $_COOKIE['cart_id'] . 
															' AND `id_item`=' . $product_id . ' AND `brand`="' . $brand . '"');
						if($pin_num_result)
							$product_in_cart_num = mysqli_fetch_row($pin_num_result)[0];
						if($product_in_cart_num == '')
							$product_in_cart_num = 0;
					}
					
					$products_catalog .= '<div class="block_for_product_with_volume">
											<a href="/' . $brand . '/product/' . $product_translit . '">
											<img src="/' . $product_img . '" class="product_img" alt="' . $product_name . '"/>
											<div class="product_name">' . $product_name . '</div></a>
	
											<div class="del_add_buttons">
												<button class="del_button da_buttons" onclick="del_item(this)" name="' . $product_id . '" id="' . $brand . '_' . $product_id . 'd">-</button>
												<div class="quantity" id="quantity' . $product_id . '">' . $product_in_cart_num . '</div>
												<button class="add_button da_buttons" onclick="add_item(this)" name="' . $product_id . '" id="' . $brand . '_' . $product_id . 'a">+</button>
											</div>
	
											<div class="product_price">' . $product_price . ' &#8381;</div>
											<div class="clear_div"></div>
										</div>';
					
					$j++;
					$s++;		//для переключателей объемов
				}
		
				
				//добавляем переключатели
				$products_catalog .= '<div class="volume_buttons">';
				
				for($k = 0; $k < $is_volume; $k++){
					$products_catalog .= '<div class="vol_buttons">' . $product_volume[$k] . '</div>';
				}
	
				$products_catalog .= '</div>';	//volume_buttons
			}
			
			$products_catalog .= '</div>';		//product_block
		}
		$products_catalog .= '</div>';	//category_name_div
	}
	$products_catalog .= '<div class="clear_div"></div></div>';	//menu
	
	mysqli_free_result($res);
	mysqli_free_result($result);
	mysqli_close($db);

	//заменим jpg/jpeg/png на webp если поддерживается браузером
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/images_to_webp.inc');
	if(isBrowserWebpSupport()){
		imagesToWebp($products_catalog);		
	}

	return $products_catalog;
}
?>
