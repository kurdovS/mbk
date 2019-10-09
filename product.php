<?php
	function class_product($name_translit, $brand){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//01_подключаем все подключаемые файлы для product
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/header/header.inc');
		$head = head($brand);
		
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/footer/footer.inc');
		$footer = footer($brand);

		$rest_name_prod = 'Макдональдс';
		$rname = 'макдоналдс';
		$burgerking_style = '';
		if($brand == 'burgerking'){
			$burgerking_style = '<link rel="stylesheet" href="/includes/for_all/burgerking_style/burgerking_style.css" />';
			$rest_name_prod = 'Бургер Кинг';
			$rname = 'burger king';
		}

	
		//получаем информацию о продукте
		$query = 'SELECT * FROM `' . $brand . '_items` WHERE `name_translit`="' . $name_translit . '"';
		$query_result = mysqli_query($db, $query);
		$product = mysqli_fetch_assoc($query_result);
		$product_volume = $product['volume'];
		$id_item = $product['id_item'];
	
		//02_начинаем формировать html-код
		$html_code = '
<!DOCTYPE html>
<html lang="ru">
<head>
	<!-- Odnoklassniki like button -->
	<script async>
	!function (d, id, did, st, title, description, image){
		var js = d.createElement("script");
		js.src = "https://connect.ok.ru/connect.js";
		js.onload = js.onreadystatechange = function(){
		if(!this.readyState || this.readyState == "loaded" || this.readyState == "complete"){
			if(!this.executed){
				this.executed = true;
				setTimeout(function(){
					onOkConnectReady()}, 0);
			}
		}}
		d.documentElement.appendChild(js);
	}(document);
	</script>

	<!-- Facebook like button -->
	<script>(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if(d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = \'https://connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v3.2\';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, \'script\', \'facebook-jssdk\'));</script>

	<!-- VK like button -->
	<script type="text/javascript" src="https://vk.com/js/api/openapi.js?160"></script>
	<script type="text/javascript">
		VK.init({apiId: 6815685, onlyWidgets: true});
	</script>

	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-131745647-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag(\'js\', new Date());

  gtag(\'config\', \'UA-131745647-1\');
</script>

	<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(51798311, "init", {
        id:51798311,
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:false
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/51798311" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

	<title>Заказать ' . $product['name'] . ' | Доставка из ' . $rest_name_prod . ' в Рязани | MBK-Delivery</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta charset="utf-8" />

	<meta name="description" content="Заказать ' . $product['name'] . ' из ' . $rest_name_prod . '. Доставка по всей Рязани." />
	<meta name="keywords" content="' . $rest_name_prod . ' доставка, ' . $rest_name_prod . ' доставка рязань, ' . $rest_name_prod . ' доставка на дом, доставка ' . $rest_name_prod . ' на дом рязань, ' . $brand . ' доставка, ' . $brand . ' доставка рязань, ' . $rname . ' доставка, ' . $rname . ' доставка рязань" />

	<link rel="apple-touch-icon" sizes="180x180" href="/mbk_favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/mbk_favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/mbk_favicons/favicon-16x16.png">
	<link rel="icon" type="image/png" sizes="192x192" href="/mbk_favicons/android-chrome-192x192.png">
	<link rel="manifest" href="/mbk_favicons/site.webmanifest">
	<link rel="mask-icon" href="/mbk_favicons/safari-pinned-tab.svg" color="#ff0000">
	<meta name="msapplication-TileColor" content="#d50017">
	<meta name="theme-color" content="#d50017">
	
	<link rel="stylesheet" href="/classes/product/class_product.css" />
	<link rel="stylesheet" href="/includes/for_all/header/header.css" />
	<link rel="stylesheet" href="/includes/for_all/header/cart/cart.css" />
	<link rel="stylesheet" href="/includes/for_all/footer/footer.css" />'
	. $burgerking_style . '
	<script defer src="/includes/for_all/header/header.js"></script>
	<script defer src="/includes/for_all/footer/footer.js"></script>
	<script defer src="/includes/for_all/header/cart/cart.js"></script>
	<script src="/classes/product/product.js"></script>
	<script src="/includes/for_mainpage/products_catalog/products_with_volume.js"></script>
</head>
<body>
' . $head . '
	<div id="wrap">
		<a href="/' . $brand . '" id="to_main_in_product"><div class="back_in_product">&larr; На главную</div></a>
		<div class="empty"></div>';
	
			
		//приведем информацию об ингредиентах к нужному виду
		$ingredients = explode("hilas", $product['ingredients']);
			
		//подготовим всю информацию для таблицы нутриентов
		$nutrients_array = array("кДж", "ккал", "Жиры", "Насыщенные жиры", "Углеводы", "Сахар", "Клетчатка", "Белки", "Соль");
		$nutritions = explode("hilas", $product['nutritions']);
	
	
		//если продукт имеет различный объем
		if($product['volume'] != 0){
			//найдем первый объем в данном продукте
			$query = 'SELECT * FROM `' . $brand . '_items` WHERE `id_item`=' . --$id_item;
			$query_result = mysqli_query($db, $query);
			$product = mysqli_fetch_assoc($query_result);
			
			while($product['volume'] > $product_volume){
				$product_volume = $product['volume'];	
				$query = 'SELECT * FROM `' . $brand . '_items` WHERE `id_item`=' . --$id_item;
				$query_result = mysqli_query($db, $query);
				$product = mysqli_fetch_assoc($query_result);
			}
			
			//в этом месте в $product_volume хранится значение самого первого объема продукта
			//а в $id_item - id этого первого объема
			$id_item++;
			//echo '$product_volume = ' . $product_volume . '<br>$id_item = ' . $id_item;
			
			$volume_array = array('', '', '', '', '');
			for($k = 0; $k < $product_volume; $k++){
				$query = 'SELECT `vol` FROM `' . $brand . '_items` WHERE `id_item`=' . ($id_item + $k);
				$volume_query_res = mysqli_query($db, $query);
				$volume_query_res = mysqli_fetch_row($volume_query_res);
				$volume_array[$k] = $volume_query_res[0];
			}


			//подключим кнопки мне нравится
			//VK
			$html_code .= '<script type = "text/javascript">';
			for($j = 0; $j < $product_volume; $j++)
				$html_code .= 'VK.Widgets.Like("vk_like' . $j . '", {type: "mini"});';
			$html_code .= '</script>';
			//OK
			$html_code .= '<script>
				function onOkConnectReady(){';
			for($j = 0; $j < $product_volume; $j++)
				$html_code .= 'OK.CONNECT.insertShareWidget("ok_shareWidget' . $j . '", "https://mbk-delivery.ru", \'{sz:20,st:"rounded",nt:1}\'); ';
			$html_code .= '}</script>';


			//для каждого объема продукта
			for($j = 0; $j < $product_volume; $j++){
				//получим всю информацию о продукте
				$query = 'SELECT * FROM `' . $brand . '_items` WHERE `id_item`=' . $id_item++;
				$query_result = mysqli_query($db, $query);
				$product = mysqli_fetch_assoc($query_result);
				
				//приведем информацию об ингредиентах к нужному виду
				$ingredients = explode("hilas", $product['ingredients']);
			
				//подготовим всю информацию для таблицы нутриентов
				$nutrients_array = array("кДж", "ккал", "Жиры", "Насыщенные жиры", "Углеводы", "Сахар", "Клетчатка", "Белки", "Соль");
				$nutritions = explode("hilas", $product['nutritions']);
				
				$html_code .= '
				<div id="volume_in_product' . ($j + 1) . '" class="volume_in_product">
					<div class="left_for_img">
						<img src="/' . $product['img'] . '" class="img_in_product" alt="' . $product['name'] . '"/>
					</div>
  
					<div class="meta_in_product">
						<div class="name_in_product">' . $product['name'] . '</div>
						<div class="price_in_product">' . $product['price'] . ' рублей</div>
						<div class="description_in_product">' . $product['description'] . '</div>
						<div class="volume_buttons">';
				
				for($k = 0; $k < $product_volume; $k++)
					$html_code .= '<div class="vol_buttons" id="vol_buttons' . ($k + 1) . '_' . ($j + 1) . '">' . $volume_array[$k] . '</div>';

				$html_code .= '</div>
						<button class="button_to_cart" onclick="add_item(this)" name="' . $product['id_item'] . '" id="' . $brand . '_' . $product['id_item'] . 'a">В корзину</button>
						<div class="like_buttons_block">
							<div class="ok_butt">
								<div id="ok_shareWidget' . $j . '"></div>
							</div>
							<div class="vk_butt">
								<div id="vk_like' . $j . '"></div>
							</div>
						</div>
					</div>
		
					<div class="description_in_product_for_mobile">' . $product['description'] . '</div>
  
					<div class="ingredients_and_nutritions">
						<div class="left_ingredients ingnut">
							<h2>Ингредиенты</h2>
							<div class="left_ingredients_list">';
	
				for($i = 0; $i < count($ingredients); $i++){
					$html_code .= $ingredients[$i] . '<br><br>';
				}
	
				$html_code .= '
							</div>
						</div>
						<div class="right_nutritions ingnut">
							<h2>Пищевая ценность</h2>
							<div class="nutritions">
	
								<table class="nut_table_up">
									<tr>
										<td class="f f_td"></td>
										<td class="s">В порции</td>
										<td class="t">% RI</td>
									</tr>
								</table>
	 
								<table class="nut_table">';
				
				$nutrients_count = 0;
				for($i = 0; $i < count($nutritions); $i++){
					if($i % 2 == 0){
						$html_code .= '<tr><td class = "f">' . $nutrients_array[$nutrients_count++] . '</td>
							<td class="s">' . $nutritions[$i] . '</td>';
					}
					else {
						$html_code .= '<td class="t">' . $nutritions[$i] . '</td></tr>';
					}
				}
				
				$html_code .= '
								</table>
								<div class="ri_text">RI – это средние значения суточных физиологических потребностей взрослого человека в энергии и пищевых веществах.</div>
							</div>
						</div>
						<div class="empty"></div>
					</div>
				</div>';	//volume_in_product
		
				//empty - с clear both - пустышка чтобы растянуть wrap по содержимому
			}
		} 
		else {	
			$html_code .= '	
				<div class="left_for_img">
					<img src="/' . $product['img'] . '" class="img_in_product" alt="' . $product['name'] . '"/>
				</div>
  
				<div class="meta_in_product">
					<div class="name_in_product">' . $product['name'] . '</div>
					<div class="price_in_product">' . $product['price'] . ' рублей</div>
					<div class="description_in_product">' . $product['description'] . '</div>
					<button class="button_to_cart" onclick="add_item(this)" name="' . $product['id_item'] . '" id="' . $brand . '_' . $product['id_item'] . 'a">В корзину</button>
					<div class="like_buttons_block">
						<div class="fb_butt">
							<div class="fb-like" data-href="https://mbk-delivery.ru' . $_SERVER['REQUEST_URI'] . '" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
						</div>
						<div class="ok_butt">
							<div id="ok_shareWidget"></div>
							<script>
							function onOkConnectReady(){
								OK.CONNECT.insertShareWidget("ok_shareWidget", "https://mbk-delivery.ru", \'{sz:20,st:"rounded",nt:1}\');
							}</script>
						</div>
						<div class="vk_butt">
							<div id="vk_like"></div>
							<script type="text/javascript">
								VK.Widgets.Like("vk_like", {type: "mini"});
							</script>
						</div>
					</div>
				</div>
		
				<div class="description_in_product_for_mobile">' . $product['description'] . '</div>
  
				<div class="ingredients_and_nutritions">
					<div class="left_ingredients ingnut">
					<h2>Ингредиенты</h2>
					<div class="left_ingredients_list">';
	
			for($i = 0; $i < count($ingredients); $i++){
				$html_code .= $ingredients[$i] . '<br><br>';
			}
	
			$html_code .= '
					</div>
				</div>
				<div class="right_nutritions ingnut">
					<h2>Пищевая ценность</h2>
					<div class="nutritions">
	
						<table class="nut_table_up">
							<tr>
								<td class="f f_td"></td>
								<td class="s">В порции</td>
								<td class="t">% RI</td>
							</tr>
						</table>
	 
						<table class="nut_table">';
				
			$nutrients_count = 0;
			for($i = 0; $i < count($nutritions); $i++){
				if($i % 2 == 0){
					$html_code .= '<tr><td class = "f">' . $nutrients_array[$nutrients_count++] . '</td>
					<td class="s">' . $nutritions[$i] . '</td>';
				}
				else {
					$html_code .= '<td class="t">' . $nutritions[$i] . '</td></tr>';
				}
			}
				
			$html_code .= '
						</table>
						<div class="ri_text">RI – это средние значения суточных физиологических потребностей взрослого человека в энергии и пищевых веществах.</div>
					</div>
				</div>
				<div class="empty"></div>
			</div>';	//ingredients_and_nutritions
		
		//empty - с clear both - пустышка чтобы растянуть wrap по содержимому
		
		}
		
		//опять общее
		$html_code .= '
	</div>' . $footer . '
</body>
</html>';

		//03_возвращаем код страницы
		return $html_code;
	}
?>
