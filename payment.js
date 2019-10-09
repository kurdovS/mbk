addEventListener('load', payment_init);

var payment_method_blocks;
var payment_buttons;
var cash_or_online;		//true - наличные | false - картой онлайн
var without_change;

function payment_init()
{
	payment_method_blocks = document.getElementsByClassName("payment_method_block anim_mb");
	
	//делаем блок "наличными" выбранным
	payment_method_blocks[0].classList.add("ch");
	payment_method_blocks[0].children[2].style.display = "block";
	payment_method_blocks[0].firstElementChild.classList.add("checked");
	payment_method_blocks[0].nextElementSibling.style.display = "block";
	payment_method_blocks[0].firstElementChild.firstElementChild.classList.add("check");
	document.getElementById("with_cash_bottom").style.display = "block";
	
	for(var i = 0; i < payment_method_blocks.length; i++){
		payment_method_blocks[i].onclick = payment_checked;
	}
	
	//кнопки "оформить заказ"
	payment_buttons = document.getElementsByClassName("to_pay_button");
	payment_buttons[0].onclick = pay_button_push;
	payment_buttons[1].onclick = pay_button_push;
	payment_buttons[0].style.display = "none";
	payment_buttons[1].style.display = "block";
	cash_or_online = true;
	
	//галочка "Без сдачи"
	without_change = document.getElementById("without_change");
	without_change.onclick = without_change_push;
}


//функция отвечает за выбор того или иного метода оплаты
function payment_checked(e)
{
	e = e.target;
	if(e.getAttribute('class') == 'round_payment' || e.getAttribute('class') == 'pmb_span')
		e = e.parentElement;
	else if(e.getAttribute('class') == 'rp_in')
		e = e.parentElement.parentElement;
	
	if(e.getAttribute('class') == 'payment_method_block anim_mb'){
		//все блоки делаем не выбранными
		for(var i = 0; i < payment_method_blocks.length; i++){
			payment_method_blocks[i].classList.remove("ch");
			payment_method_blocks[i].nextElementSibling.style.display = "none";
			payment_method_blocks[i].firstElementChild.classList.remove("checked");
			payment_method_blocks[i].lastElementChild.style.display = "none";
			payment_method_blocks[i].children[2].style.display = "none";
			payment_method_blocks[i].firstElementChild.firstElementChild.classList.remove("check");
		}
	
		//делаем нажатый блок выбранным
		e.classList.add("ch");
		var txt = e.children[2];
		txt.style.display = "block";
		e.firstElementChild.classList.add("checked");
		e.nextElementSibling.style.display = "block";
		e.firstElementChild.firstElementChild.classList.add("check");
		
		//отображаем скрытое
		if(e.getAttribute("id") == 'with_cash'){
			document.getElementById("with_cash_bottom").style.display = "block";
		}
		
		//изменяем указатель выбранного метода
		cash_or_online = !cash_or_online;
		
		//делаем нужную кнопку "оформить заказ" видимой
		if(!cash_or_online){
			payment_buttons[0].style.display = "block";
			payment_buttons[1].style.display = "none";
		}
		else {
			payment_buttons[0].style.display = "none";
			payment_buttons[1].style.display = "block";
		}
	}
}


//функция отвечает за нажатие кнопки "Оформить заказ"
function pay_button_push(e)
{
	//выбрана оплата наличными
	if(cash_or_online){	
		//сумма с которой подготовить сдачу
		var change_from = document.getElementById("payment_input").value;
		
		//определяем нужна ли сдача или нет
		var with_change = true;		//true - сдача нужна | false - сдача не нужна
		if(without_change.firstElementChild.getAttribute("class") == "rp_in")
			with_change = true;
		else if(without_change.firstElementChild.getAttribute("class") == "rp_in check")
			with_change = false;
		
		//передаем все в php-обработчик payment_form_handler.php
		//Отправляем на сервер id товара  
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('cash', '');
		data.append('change_from', change_from);
		data.append('with_change', with_change);
	
		var url = "/" + brand + "/payment_form_handler";
		var req = new XMLHttpRequest();
		req.open("POST", url, false);			//ПОЧЕМУТО РАБОТАЕТ ДАЖЕ С АСИНХРОННЫМ СОЕДИНЕНИЕМ
		req.addEventListener('load', funcer);
		req.send(data);
	//выбрана оплата картой онлайн
	} else {		
		//передаем все в php-обработчик payment_form_handler.php
		//Отправляем на сервер id товара  
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('card_online', '');
		
		var url = "/" + brand + "/payment_form_handler";
		var req = new XMLHttpRequest();
		req.open("POST", url, false);
		req.addEventListener('load', funcer);
		req.send(data);
	}
}


function funcer(e)
{
	var php = e.target;
	//alert(php.responseText);
}

//функция отвечает за нажатие на галочку "Без сдачи"
function without_change_push(e)
{	
	e = e.target;
	if(e.getAttribute("class") != "rp_in" && e.getAttribute("class") != "rp_in check")
		e = e.firstElementChild;
	//ставим галочку 
	e.classList.toggle("check");
	
	//блокируем инпут для ввода суммы с которой нужна сдача
	var payment_input = document.getElementById("payment_input");
	if(payment_input.hasAttribute('disabled'))
		payment_input.removeAttribute('disabled');
	else 
		payment_input.setAttribute('disabled', 'disabled');
	
}