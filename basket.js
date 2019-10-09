addEventListener('load', basket_module_init);

var promocode;

function basket_module_init()
{
	//для del_x
	var del_xs = document.getElementsByClassName('del_x');
	//для кнопок -
	var del_buttons = document.getElementsByClassName("picm_button_del");
	//для кнопок +
	var add_buttons = document.getElementsByClassName("picm_button_add");
	for(var i = 0; i < del_buttons.length; i++){
		del_xs[i].onclick = del_x_push;
		del_buttons[i].onclick = del_button_push;
		add_buttons[i].onclick = add_button_push;
	}

	//установим обработчик для нажатия на вопросительный знак при вводе промокода
	document.getElementById("about_promo").onclick = about_promo;
	//установим обработчик кнопки "Применить"
	document.getElementById("promokod_button").onclick = use_promo;
	//установим обработчик нажатия на кнопку "Оформить заказ"
	document.getElementById("to_delivery_button").onclick = to_delivery_push;
}

//обработчик нажатия del_x
function del_x_push(e){
	var product_in_cart = e.target.parentElement;
	var quant = product_in_cart.lastElementChild.previousElementSibling.lastElementChild;
	var del_butt = quant.firstElementChild;
	quant = quant.children[1].innerHTML;
	
	//вызов функции из cart.js 
	del_item_from_cart(del_butt);
	product_in_cart.style.display = "none";
	
	//вычисления для #total_in_basket
	var total_price = document.getElementById('total_of_cart').innerHTML.match(/[0-9]+/);
	total_price = Number(total_price);
	var product_price = product_in_cart.lastElementChild.previousElementSibling.children[1].innerHTML.match(/[0-9]+/);
	product_price = Number(product_price);
	var count_items = document.getElementById('count_items_round').innerHTML.match(/[0-9]+/);
	count_items = Number(count_items);
	
	for(var i = 0; i < quant; i++){
		total_price -= product_price;
		count_items--;
	}
	
	//обновляем информацию в #total_in_basket
	//вставим новую стоимость в ИТОГО
	document.getElementById("total_money").innerHTML = (total_price + 129) + ',00 Р';
	//вставим в "За продукты"
	document.getElementsByClassName('total_money_fpd')[0].innerHTML = total_price + ',00 Р'; 
	//число продуктов в корзине
	document.getElementsByClassName('total_money_fpd')[2].innerHTML = count_items + ' шт';
}


//обработчик нажатия кнопок del
function del_button_push(e){
	//вызов функции из cart.js
	del_item(e.target);
}


//обработчик нажатия кнопок add
function add_button_push(e){
	//вызов функции из cart.js
	add_item(e.target);
}



/*---------------------------Работа с промокодом------------------*/

//обработчик нажатия на вопросительный знак
function about_promo(e){
	document.getElementById("promo_desc").style.display = "block";
	document.getElementById("promokod_container").style.height = "5.5em";
}

//обработчик нажатия кнопки "Применить"
function use_promo(e){
	promocode = document.getElementById("promokod_input").value;
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('promocode', promocode);

	var url = "/" + brand + "/basket_form_handler";
	var req = new XMLHttpRequest();
	req.open("POST", url, true);
	req.addEventListener('load', promocode_answer);
	req.send(data);
}


//обрабатывает ответ сервера на запрос проверить промокод
function promocode_answer(e){
	//получим стоимость и сообщение
	var promo_arr = e.target.responseText.split('hilas');
	var del_cost = Number(promo_arr[1]);
	var del_message = promo_arr[0];


	//получим старую полную стоимость
	var total_price = Number(document.getElementById("total_money").innerHTML.match(/[0-9]+/));
	//получим старую стоимость доставки
	var old_del_cost = Number(document.getElementById("delivery_money").innerHTML.match(/[0-9]+/));
	//получим сумму скидки
	var promo_discount = 129 - del_cost;
	//если скидка есть выведем поле скидки
	if(promo_discount != 0){
		document.getElementById("for_discount").style.display = "block";
		document.getElementById("just_minus").style.display = "block";
		document.getElementById("for_delivery").style.marginBottom = "5px";
	}
	else {
		document.getElementById("for_discount").style.display = "none";
		document.getElementById("just_minus").style.display = "none";
		document.getElementById("for_delivery").style.marginBottom = "30px";
	}


	//вставим новую стоимость в ИТОГО
	document.getElementById("total_money").innerHTML = (total_price - old_del_cost + del_cost) + ',00 Р';
	//вставим новую стоимость доставки
	document.getElementById("delivery_money").innerHTML = del_cost + ',00 Р';
	//вставим скидку
	document.getElementById("delivery_discount").innerHTML = '-' + promo_discount + ',00 Р';
	//выведем сообщение
	document.getElementById("promokod_message").innerHTML = del_message;
	//если скидка успешно применена то окрасим текст в зеленый иначе в красный
	if(del_message == 'Промокод успешно активирован')
		document.getElementById("promokod_message").style.color = "green";
	else
		document.getElementById("promokod_message").style.color = "red";
}


//запрос серверу записать данные о промокоде в БД
function to_delivery_push(e){
	//получим значения переменных которые мы хотим отправить на сервер
	//стоимость корзины
	var products_sum = document.getElementById("products_money").innerHTML.match(/[0-9]+/);
	//стоимость доставки
	var delivery_sum = document.getElementById("delivery_money").innerHTML.match(/[0-9]+/);


	//попросим сервер записать в таблицу orders информацию об использованном промокоде для данного заказа
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('promokod', promocode);
	data.append('products_sum', products_sum);
	data.append('delivery_sum', delivery_sum);

	var url = "/" + brand + "/basket_form_handler";
	var req = new XMLHttpRequest();
	req.open("POST", url, true);
	req.addEventListener('load', to_bd_answer);
	req.send(data);
}

//обработчик ответа от сервера о записи информации о промокоде в БД
function to_bd_answer(e){
	if(e.target.responseText == 'WELL_DONE')
		window.location.href = '/' + brand + '/order/delivery';
}
