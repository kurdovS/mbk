addEventListener('load', cart_initialization);
addEventListener('click', sc_appear);

var cart_id;
var products_in_cart_wrap;
var brand;
var url;

/////////////////////////////////////////////////////////////////////
////////////////.........ОСНОВНЫЕ ФУНКЦИИ.........../////////////////
/////////////////////////////////////////////////////////////////////

//Функция для первоначальной инициализации корзины
function cart_initialization()
{
	brand = "mcdonalds";
	if(document.location.pathname.charAt(1) == 'b')
		brand = 'burgerking';
	else if(document.location.pathname.charAt(1) == 'k')
		brand = 'kfc';
	url = '/' + brand + '/cart';
	products_in_cart_wrap = document.getElementById("products_in_cart_wrap");

	//01_узнаем id корзины (в куках или просим сервер сгенерировать новое уникальное id)
	//если в куках есть id корзины
	if(cart_id = get_cookie('cart_id')){
	}
	else {
		//В куках нет id корзины, поэтому просим сервер сформировать новое уникальное id
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('generate_new_cart_id', '');
		
		var request = new XMLHttpRequest();
		request.addEventListener('load', generate_new_cart_id);
		request.open("POST", url, false);				//WARNING: используется синхронное соединение!
		request.send(data);
	}
	
	//02_теперь имея id корзины просим сервер инициализировать корзину
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('cart_id', cart_id);
	data.append('brand', brand);
	
	var request = new XMLHttpRequest();
	request.addEventListener('load', receiver);
	request.open("POST", url, true);
	request.send(data);
}


//Функция для добавления товара в корзину
function add_item(e){
	//показываем small_cart
	if(small_cart.getAttribute('class') != "vis")
		if(document.documentElement.clientWidth > 1350)
		cart_button_push();
		
	//Получаем id_item из атрибута name кнопки
	var item_id = e.getAttribute("name");
	//Получаем brand из атрибута id кнопки
	var brand_name = e.getAttribute("id");
	brand_name = brand_name.split('_')[0];
	
	//Отправляем на сервер id товара  
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('add_item', item_id);
	data.append('cart_id', cart_id);
	data.append('brand', brand_name);
	
	var request = new XMLHttpRequest();
	request.addEventListener('load', receiver);
	request.open("POST", url, true);
	request.send(data);
	
	//количество продукта если /order/basket или product_catalog
	if(e.previousElementSibling.getAttribute('class') != 'description_in_product' && e.previousElementSibling.getAttribute('class') != 'volume_buttons'){
		var quantity = Number(e.previousElementSibling.innerHTML) + 1;
		var add_butt = document.getElementsByName(e.getAttribute('name'));
		var innerTo = add_butt[(add_butt.length - 1)].previousElementSibling;
		if(innerTo.getAttribute('class') != 'description_in_product' && innerTo.getAttribute('class') != 'volume_buttons')
			innerTo.innerHTML = quantity;
	}
	
	//ТОЛЬКО ЕСЛИ на странице /order/basket
	if(document.location.pathname == ('/' + brand + '/order/basket')){
		//вычисления для #total_in_basket
		var picm_button_del = document.getElementsByClassName('picm_button_del');
		for(var i = 0; i < picm_button_del.length; i++){
			if(picm_button_del[i].getAttribute('name') == item_id)
				picm_button_del = picm_button_del[i];
		}
		var product_price = picm_button_del.parentElement.previousElementSibling.innerHTML;
		product_price = product_price.match(/[0-9]+/);
		product_price = Number(product_price);
		var price_all = picm_button_del.parentElement.previousElementSibling.previousElementSibling;
		var price_for = price_all.innerHTML.match(/[0-9]+/);
		price_for = Number(price_for);
		price_for += product_price;
		price_all.innerHTML = price_for + ',00 Р';
	
		//изменение #total_in_basket
		var total_of_cart = document.getElementById('total_of_cart');
		total_of_cart = total_of_cart.innerHTML.match(/[0-9]+/);
		total_of_cart = Number(total_of_cart);
		total_of_cart += Number(product_price);
		//вставим новую стоимость в ИТОГО
		var promo_discount = Number(document.getElementById("delivery_discount").innerHTML.match(/[0-9]+/));
		document.getElementById("total_money").innerHTML = (total_of_cart + 129 - promo_discount) + ',00 Р';
		document.getElementsByClassName('total_money_fpd')[0].innerHTML = total_of_cart + ',00 Р';
		//число продуктов в корзине
		var count_items_round = document.getElementById('count_items_round').innerHTML;
		count_items_round = Number(count_items_round);
		document.getElementsByClassName('total_money_fpd')[3].innerHTML = ++count_items_round + ' шт';
	}
}


//Функция для удаления товара из корзины
function del_item(e){
	//Получаем id_item из атрибута name кнопки
	var item_id = e.getAttribute("name");
	//Получаем brand из атрибута id кнопки
	var brand_name = e.getAttribute("id");
	brand_name = brand_name.split('_')[0];
	
	//Отправляем на сервер id товара  
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('del_item', item_id);
	data.append('cart_id', cart_id);
	data.append('brand', brand_name);
	
	
	var request = new XMLHttpRequest();
	request.addEventListener('load', receiver);
	request.open("POST", url, true);
	request.send(data);


	//количество продукта /order/basket или product_catalog
	var quantity = Number(e.nextElementSibling.innerHTML) - 1;
	var add_butt = document.getElementsByName(e.getAttribute('name'));
	//не вставляем количество продукта если на странице продукта
	var innerTo = add_butt[(add_butt.length - 1)].previousElementSibling;
	if(quantity >= 0 && innerTo.getAttribute('class') != 'description_in_product' && innerTo.getAttribute('class') != 'volume_buttons')
		add_butt[(add_butt.length - 1)].previousElementSibling.innerHTML = quantity;
	
	
	//ТОЛЬКО ЕСЛИ на странице /order/basket
	if(document.location.pathname == ('/' + brand + '/order/basket')){
		//вычисления для #total_in_basket
		var picm_button_del = document.getElementsByClassName('picm_button_del');
		for(var i = 0; i < picm_button_del.length; i++){
			if(picm_button_del[i].getAttribute('name') == item_id)
				picm_button_del = picm_button_del[i];
		}
		var product_in_cart = picm_button_del.parentElement.parentElement.parentElement;
		if(quantity <= 0)
			product_in_cart.style.display = "none";
		var product_price = picm_button_del.parentElement.previousElementSibling.innerHTML;
		product_price = product_price.match(/[0-9]+/);
		product_price = Number(product_price);
		var price_all = picm_button_del.parentElement.previousElementSibling.previousElementSibling;
		var price_for = price_all.innerHTML.match(/[0-9]+/);
		price_for = Number(price_for);
		price_for -= product_price;
		price_all.innerHTML = price_for + ',00 Р';
		
		//изменение #total_in_basket
		var total_of_cart = document.getElementById('total_of_cart');
		total_of_cart = total_of_cart.innerHTML.match(/[0-9]+/);
		total_of_cart = Number(total_of_cart);
		total_of_cart -= Number(product_price);
		//вставим новую стоимость в ИТОГО
		var promo_discount = Number(document.getElementById("delivery_discount").innerHTML.match(/[0-9]+/));
		document.getElementById("total_money").innerHTML = (total_of_cart + 129 - promo_discount) + ',00 Р';
		//вставим в "За продукты"
		document.getElementsByClassName('total_money_fpd')[0].innerHTML = total_of_cart + ',00 Р'; 
		//число продуктов в корзине
		var count_items_round = document.getElementById('count_items_round').innerHTML;
		count_items_round = Number(count_items_round);
		document.getElementsByClassName('total_money_fpd')[3].innerHTML = --count_items_round + ' шт';
	}
}


//Функция для отчистки корзины
function cart_clear(e){
	var result = confirm("Вы действительно хотите удалить все товары из корзины?");
	if(result){
		if(document.location.pathname == ('/' + brand)){
			//обнулим все quantity в products_catalog
			products_in_cart_wrap = document.getElementById("products_in_cart_wrap");
			var itemsCartLength = products_in_cart_wrap.children.length;
			for(var i = 0; i < itemsCartLength; i++){
				var id_item = products_in_cart_wrap.children[i].children[2].children[0].getAttribute('name');
				document.getElementById('quantity' + id_item).innerHTML = 0;
			}
		}
		
		//Отправляем на сервер id товара  
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('cart_clear', result);
		data.append('cart_id', cart_id);
		data.append('brand', brand);
		
		var request = new XMLHttpRequest();
		request.addEventListener('load', receiver);
		request.open("POST", url, true);
		request.send(data);	
	}
}


//функция для полного удаления товара из корзины (не по 1 единице) ДЛЯ order/basket
function del_item_from_cart(e){
	//Получаем id_item из атрибута name кнопки
	var item_id = e.getAttribute("name");
	//Получаем brand из атрибута id кнопки
	var brand_name = e.getAttribute("id");
	brand_name = brand_name.split('_')[0];
	
	//Отправляем на сервер id товара  
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('del_item_from_cart', item_id);
	data.append('cart_id', cart_id);
	data.append('brand', brand_name);
	
	
	var request = new XMLHttpRequest();
	request.addEventListener('load', receiver);
	request.open("POST", url, true);
	request.send(data);
}
/////////////////////////////////////////////////////////////////////
////////////////....ПОЛУЧЕНИЕ ОТВЕТОВ ОТ СЕРВЕРА..../////////////////
/////////////////////////////////////////////////////////////////////

//НЕНУЖНАЯ ФУНКЦИЯ ОСТАВИЛ НА ВСЯКИЙ СЛУЧАЙ
//сервер прислал число товаров в корзине
/*function count_items_handler(e){
	var data = e.target;
	if(data.status == 200){
		data = data.responseText;
		var cir = document.getElementById("count_items_round");
		
		if(data > 0){
			cir.style.display = "block";
			if(data > 9)
				cir.style.width = "24px";
			else
				cir.style.width = "16px";
			
			cir.innerHTML = data;
		}
		else
			cir.style.display = "none";
	}
}*/


//сервер прислал общую стоимость корзины
function how_much_is_it(e){
	var data = e.target;
	if(data.status == 200){
		var cart_total = document.getElementById('total_of_cart');
		cart_total.innerHTML = data.responseText;
		document.getElementById('total_mob').innerHTML = data.responseText;
	}
}

//сервер прислал уникальное id корзины
function generate_new_cart_id(e){
	var response = e.target;
	if(response.status == 200){
		cart_id = response.responseText;
	}
}

//Функция для получения ответа от сервера, на запросы
function receiver(e){
	var data = e.target;
	if(data.status == 200){
		str = data.responseText;
		products_in_cart_wrap.innerHTML = str;
	}
	
	sc_childrens = products_in_cart_wrap.children;
	if(sc_childrens[0] != undefined){
		for(var i = 0, child; child = sc_childrens[i]; i++){
			child.onmouseover = replace_description;
			child.onmouseout = replace_buttons;
		}
	}
	
	total_update();
	count_items_round();
}



/////////////////////////////////////////////////////////////////////
////////////////.........СЕРВИСНЫЕ ФУНКЦИИ........../////////////////
/////////////////////////////////////////////////////////////////////

//функция скрывающая small_cart когда нажимаешь в любом месте страницы
function sc_appear(e){
	var cl = e.target.getAttribute("class");
	var id = e.target.getAttribute("id");

	switch(id){
		case "small_cart":
			return;
			break;
		case "cart_button_img":
			return;
			break;
		case "order_button_wrap":
			return;
			break;
		case "order_button":
			return;
			break;
		case "products_in_cart_wrap":
			return;
			break;
		case "clear_button_wrap":
			return;
			break;
	}
	switch(cl){
		case "add_button da_buttons":
			return;
			break;
		case "del_button da_buttons":
			return;
			break;
		case "item_in_cart":
			return;
			break;
		case "item_in_cart_img":
			return;
			break;
		case "item_add_del":
			return;
			break;
		case "ad_buttons_in_cart in_item_add_del":
			return;
			break;
		case "counter_item in_item_add_del":
			return;
			break;
		case "item_in_cart_price":
			return;
			break;	
		case "button_to_cart":
			return;
			break;
	}
	
	if(small_cart.getAttribute("class") == "vis")
		cart_button_push();
}


//функция для смены названия товара на кнопки
function replace_description(e){
	var item_in_cart = e.currentTarget;
	item_in_cart.children[1].style.display = "none";
	item_in_cart.children[2].style.display = "block";
}

//функция для смены кнопок на описание товара 
function replace_buttons(e){
	var item_in_cart = e.currentTarget;
	item_in_cart.children[2].style.display = "none";
	item_in_cart.children[1].style.display = "block";
}


//Функция для получения cookie
function get_cookie(cookie_name){
	var pattern = '(' + cookie_name + '=[0-9]+)';
	var result = document.cookie.match(pattern)
	if(result != null){
		result[0] = unescape(result[0]);
		result[0] = result[0].replace(cookie_name + "=", "");
		return result[0];
	}
	return false;
}

//Функция запрашивающая у сервера информацию об общей стоимости товара
function total_update(){
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('how_much_is_it', '');
	data.append('id', cart_id);
				
	var req = new XMLHttpRequest();
	req.addEventListener('load', how_much_is_it);
	req.open("POST", url, true);				
	req.send(data);
}

//функция обрабатывает значок добавление и убавление товаров в корзине
function count_items_round(){
//НЕНУЖНО ОСТАВЛЕНО НА ВСЯКИЙ СЛУЧАЙ
/*	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('count_items_round', '');
	data.append('id', cart_id);
	data.append('brand', brand);
				
	var req = new XMLHttpRequest();
	req.addEventListener('load', count_items_handler);
	req.open("POST", url, true);				
	req.send(data);*/
	
	var items_in_cart = document.getElementsByClassName("counter_item in_item_add_del");
	var counter = 0;
	for(var i = 0; i < items_in_cart.length; i++)
		counter += Number(items_in_cart[i].innerHTML);
	
	var cir = document.getElementById("count_items_round");
		
	if(counter > 0){
		cir.style.display = "block";
		if(counter > 9)
			cir.style.width = "24px";
		else
			cir.style.width = "16px";
			cir.innerHTML = counter;
			
		
		document.getElementById('product_count_mob').innerHTML = counter;
		document.getElementById('total_mob').style.display = 'block';
		
	}
	else {
		cir.style.display = "none";
		document.getElementById('product_count_mob').innerHTML = '';
		document.getElementById('total_mob').style.display = 'none';
	}
}
