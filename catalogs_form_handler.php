<?php
	//01_пришел запрос на формирование кода для главной страницы catalogs
	if(isset($_POST['restoraunts'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//декодируем присланную json строку со значениям какие рестораны выбраны 
		//1 - ресторан выбран	'' - ресторан не выбран 
		//индекс массива == (id_ресторана - 1)
		$checks = json_decode($_POST['r_checks']);
		
		//html-код возвращаемый js-скрипту
		$choosed_rest = '';
		
		//выясним какие кнопки нужны
		$mode = 'main';
		$editable = '';
		switch($_POST['mode']){
			case 'add':
				$mode = '<td class="crt_td_butt"><div class="butt_td" onclick="add_product(this)">Вставить ниже</div></td>';
				break;
			case 'del':
				$mode = '<td class="crt_td_butt"><div class="butt_td_del" onclick="del_product(this)">Удалить</div></td>';
				break;
			case 'edit':
				$mode = '<td class="crt_td_butt"><div class="butt_td_edit" onclick="edit_product(this)">Изменить</div></td>';
				//$editable = ' contenteditable="true"';
				break;
			default:
				$mode = '';
				break;
		}
		
		for($i = 0; $i < $_POST['rest_num']; $i++){
			//если данный ресторан выбран
			if($checks[$i] == 1){
				//возьмем название ресторана и добавим к нему _items чтобы знать какую таблицу с ассортиментом загружать
				$query = 'SELECT name FROM `mbk_restoraunts` WHERE id_restoraunt=' . ($i + 1);
				$res = mysqli_query($db, $query);
				$rest_name = mysqli_fetch_row($res);
				$rest_name = $rest_name[0];
				$rest_name .= '_items';
				$choosed_rest .= '<div class="small_title">' . $rest_name . '</div>
				<table class="choosed_rest_table">';
				
				//получаем информацию из таблицы с ассортиментом
				$query = 'SELECT * FROM ' . $rest_name . ' LIMIT 0, 300';
				$res = mysqli_query($db, $query);
				$products_num = mysqli_num_rows($res);		//число продуктов в таблице
				$column_num = mysqli_num_fields($res);		//число столбцов в таблице
				
				//устанавливаем заголовки полей таблицы
				$choosed_rest .= '<tr class="choosed_rest_tr">
					<td class="f_crt_regular_td"></td>
					<td class="crt_title_td">Название продукта</td>
					<td class="crt_title_td">Категория</td>
					<td class="crt_title_td">Категория по русски</td>
					<td class="crt_title_td">Цена</td>
					<td class="crt_title_td">Путь к изображению</td>
					<td class="crt_title_td">Описание</td>
					<td class="crt_title_td">Ингредиенты</td>
					<td class="crt_title_td">Нутриенты</td>
					<td class="crt_title_td">Объем</td>';
					if($_POST['mode'] == 'add')
						$choosed_rest .= $mode;
				$choosed_rest .= '
				</tr>';
				
				if($_POST['mode'] == 'add'){
					//скрытые поля для добавления продуктов в меню
					$choosed_rest .= '<tr class="hidden_prod_tr_hide">
						<td class="f_crt_regular_td"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
						<td class="hidden_prod_td" contenteditable="true"></td>
					</tr>';
				}
				
				//для каждого продукта
				for($j = 0; $j < $products_num; $j++){
					$product = mysqli_fetch_assoc($res);
					$choosed_rest .= '<tr>
						<td class="f_crt_regular_td">' . $product['id_item'] . '</td>
						<td class="crt_regular_td">' . $product['name'] . '</td>
						<td class="crt_regular_td">' . $product['category'] . '</td>
						<td class="crt_regular_td">' . $product['category_rus'] . '</td>
						<td class="crt_regular_td">' . $product['price'] . '</td>
						<td class="crt_regular_td">' . $product['img'] . '</td>
						<td class="crt_regular_td">' . $product['description'] . '</td>
						<td class="crt_regular_td">' . $product['ingredients'] . '</td>
						<td class="crt_regular_td">' . $product['nutritions'] . '</td>
						<td class="crt_regular_td">' . $product['volume'] . '</td>' .
						$mode . '
					</tr>';
					
					if($_POST['mode'] == 'add' || $_POST['mode'] == 'edit'){
						//скрытые поля для добавления продуктов в меню
						$choosed_rest .= '<tr class="hidden_prod_tr_hide">
							<td class="f_crt_regular_td"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
							<td class="hidden_prod_td" contenteditable="true"></td>
						</tr>';
					}
				}
				$choosed_rest .= '</table';
			}
			
		}
		mysqli_close($db);
		unset($_POST['restoraunts']);
		
		echo $choosed_rest;
	}
	
	
	
	//02_пришел запрос на добавление продукта в меню ресторана
	if(isset($_POST['add_product'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];
		
		//если пользователь имеет в системе статус "Администратор"
		if($user_status == 'Администратор'){
			//переносим все записи идущие после вставляемой вперед на единицу
			$rest_name = $_POST['rest_name'];					//в меню какого ресторана вставляем запись
			$products_num = mysqli_query($db, 'SELECT COUNT(*) FROM `' . $rest_name . '`');
			$products_num = mysqli_fetch_row($products_num);
			$products_num = $products_num[0];					//всего позиций в меню
			$id_before_insert = $_POST['add_product'];			//позиция после которой вставляется новый продукта

			//название продукта транслитом
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');

			//переносим $products_num - $id_before_insert записей вперед на единицу
			for($i = $products_num; $i > $id_before_insert; $i--){
				$current_product = mysqli_query($db, 'SELECT * FROM `' . $rest_name . '` WHERE id_item=' . $i);
				$current_product = mysqli_fetch_assoc($current_product);
				$product_translit = translit_encode(mb_strtolower($current_product['name']));
				//если это самая последняя запись то вставляем ее в таблицу с id + 1
				if($i == $products_num){
					$query = 'INSERT INTO `' . $rest_name . '` VALUES("' . ($i + 1) . '", "' .  addslashes($current_product['name']) . '", "' . addslashes($product_translit) . '", "' .
								addslashes($current_product['category']) . '", "' . addslashes($current_product['category_rus']) . '", "' . addslashes($current_product['price']) . '", "' .
								addslashes($current_product['img']) . '", "' . addslashes($current_product['description']) . '", "' . addslashes($current_product['ingredients']) . '", "' .
								addslashes($current_product['nutritions']) . '", "' . addslashes($current_product['volume']) . '", "' . addslashes($current_product['vol']) . '")';
					mysqli_query($db, $query);
				}
				//если это не последняя запись то переносим позицию из i в i + 1
				else {
					$query = 'UPDATE `' . $rest_name . '` SET id_item="' . ($i + 1) . '", name="' . addslashes($current_product['name']) . '", name_translit="' . addslashes($product_translit) . '", category="' . 
							addslashes($current_product['category']) . '", category_rus="' . addslashes($current_product['category_rus']) . '", price="' . 
							addslashes($current_product['price']) . '", img="' . addslashes($current_product['img']) . '", description="' . addslashes($current_product['description']) . 
							'", ingredients="' . addslashes($current_product['ingredients']) . '", nutritions="' . addslashes($current_product['nutritions']) . '", volume="' .
							addslashes($current_product['volume']) . '", vol="' . addslashes($current_product['vol']) . '" WHERE id_item="' . ($i + 1) . '"';
					$res_query = mysqli_query($db, $query);
				}
			}
			//вставим новую позицию
			$product_translit = translit_encode(mb_strtolower($_POST['add_name']));
			$query = 'UPDATE `' . $rest_name . '` SET id_item="' . ($id_before_insert + 1) . '", name="' . addslashes($_POST['add_name']) . '", name_translit="' . addslashes($product_translit) . '", category="' . 
					addslashes($_POST['add_category']) . '", category_rus="' . addslashes($_POST['add_category_rus']) . '", price="' . addslashes($_POST['add_price']) . '", img="' .
					addslashes($_POST['add_img_path']) . '", description="' . addslashes($_POST['add_description']) . '", ingredients="' . addslashes($_POST['add_ingredients']) . 
					'", nutritions="' . addslashes($_POST['add_nutrients']) . '", volume="' . addslashes($_POST['add_volume']) . '", vol="' . addslashes($_POST['add_vol']) . '" WHERE id_item="' . 
					($id_before_insert + 1) . '"';
			mysqli_query($db, $query);
			
			echo 'Запись была добавлена в позицию ' . ($id_before_insert + 1);
		}
		else
			echo 'Только пользователь со статусом "Администратор" может добавлять позиции в меню';
		
		unset($_POST['add_product']);
		mysqli_close($db);
	}
	
	
	
	//03_пришел запрос на удаление товара из меню ресторана
	if(isset($_POST['del_product'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];
	
		//если пользователь имеет в системе статус "Администратор"
		if($user_status == 'Администратор'){
			$rest_name = $_POST['rest_name'];					//в меню какого ресторана вставляем запись
			//выясним количество записей идущих после удаляемой позиции
			$query = 'SELECT * FROM `' . $rest_name . '` WHERE id_item>' . $_POST['del_product'];
			$after_for_del = mysqli_query($db, $query);
			$after_for_del = mysqli_num_rows($after_for_del);
			$after_for_del += $_POST['del_product'];						//id последней записи
			//удаляем позицию меню
			$query = 'DELETE FROM `' . $rest_name . '` WHERE id_item="' . $_POST['del_product'] . '"';
			$res = mysqli_query($db, $query);
			//уменьшим id всех записей после удаленной на 1
			for($j = ($_POST['del_product'] + 1); $j <= $after_for_del; $j++){
				//для каждой статьи идущей после удаленной уменьшим id на 1
				mysqli_query($db, 'UPDATE `' . $rest_name . '` SET `id_item`="' . ($j - 1) . '" WHERE `id_item`=' . $j);
			}
			//теперь уменьшим на 1 AUTO_INCREMENT
			mysqli_query($db, 'ALTER TABLE `' . $rest_name . '` AUTO_INCREMENT=' . $after_for_del);
			echo 'Позиция была удалена из системы';
		}
		else
			echo 'Только пользователи со статусом "Администратор" могут удалять позиции меню из системы';
		
		mysqli_close($db);
		unset($_POST['del_product']);
	}
	
	
	
	//04_пришел запрос на изменение товара из меню ресторана
	if(isset($_POST['edit_product'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];
		
		//если пользователь имеет в системе статус "Администратор"
		if($user_status == 'Администратор'){
			//имя транслитом
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
			$product_translit = translit_encode(mb_strtolower($_POST['edit_name']));

			$rest_name = $_POST['rest_name'];					//в меню какого ресторана вставляем запись
			$query = 'UPDATE `' . $rest_name . '` SET name="' . addslashes($_POST['edit_name']) . '", name_translit="' . addslashes($product_translit) . '", category="' . 
					addslashes($_POST['edit_category']) . '", category_rus="' . addslashes($_POST['edit_category_rus']) . '", price="' . addslashes($_POST['edit_price']) . '", img="' .
					addslashes($_POST['edit_img_path']) . '", description="' . addslashes($_POST['edit_description']) . '", ingredients="' . addslashes($_POST['edit_ingredients']) . 
					'", nutritions="' . addslashes($_POST['edit_nutrients']) . '", volume="' . addslashes($_POST['edit_volume']) . '", vol="' . addslashes($_POST['edit_vol']) . '" WHERE id_item="' . 
					addslashes($_POST['edit_product']) . '"';
			mysqli_query($db, $query);
			
			echo 'Изменения были сохранены';
		}
		else
			echo 'Только пользователи со статусом "Администратор" могут изменять позиции меню из системы';
		
		mysqli_close($db);
		unset($_POST['edit_product']);
	}
?>
