addEventListener('load', delivery_init);

var sms_verify;
var didnt_sms;

var new_sms_code_button;

var sms_input;
var sms_button;

var dont_call;

var to_pay_buttons;

//Поле вывода предупреждающего сообщения
var err_mes;


function delivery_init()
{
	err_mes = document.getElementById('err_mes');

	//кнопка для повторной генерации смс-кода
	new_sms_code_button = document.getElementById("repeat_sms_code");
	new_sms_code_button.onclick = repeat_sms_code;

	//чекбокс Не звонить
	dont_call = document.getElementsByClassName("round_delivery")[0];
	dont_call.onclick = function dont_call_toggler(e){
		e = e.target;
		if(e.getAttribute("class") == "round_delivery" || e.getAttribute("class") == "round_delivery checked"){
			e.classList.toggle("checked");
			e.firstElementChild.classList.toggle("check");
		}
		else {
			e.parentElement.classList.toggle("checked");
			e.classList.toggle("check");
		}
	}
	dont_call.onmouseover = function dont_call_onmo(e){
		var info = document.getElementById("dont_call_info");
		info.style.display = "block";
	}
	dont_call.onmouseout = function dont_call_onmout(e){
		var info = document.getElementById("dont_call_info");
		info.style.display = "none";
	}

	
	sms_verify = document.getElementById("sms_verify");
	didnt_sms = document.getElementById("didnt_sms");
	
	sms_input = document.getElementById("sms_input");
	sms_button = document.getElementById("sms_button");
	
	sms_button.onclick = sms_button_click;
	
	to_pay_buttons = document.getElementsByClassName("to_pay_button");

	if(to_pay_buttons[1].getAttribute('name')){
		to_pay_buttons[1].style.display = "block";
		to_pay_buttons[1].onclick = to_pay;
		document.getElementsByName("phone")[0].onchange = function(){
			to_pay_buttons[1].style.display = 'none';
			to_pay_buttons[0].style.display = 'block';
		};
	}
	else
		to_pay_buttons[0].style.display = "block";

	to_pay_buttons[0].onclick = to_pay_push;
	to_pay_buttons[1].onclick = to_pay;
	//вызов функции из внешнего файла phone_input.js для обработки ввода телефона
	phone_input_func();
}



//функция делает сообщение об ошибке ввода видимым и выводит ссобщение
function err_mes_func(mes)
{
	err_mes.style.display = "block";
	err_mes.innerHTML = mes;
	err_mes.focus();

	//когда вводятся инпуты скрыть сообщение
	document.getElementsByName('name')[0].oninput = err_mes_hide;
	document.getElementsByName('phone')[0].oninput = err_mes_hide;
	document.getElementsByName('time')[0].oninput = err_mes_hide;
	document.getElementsByName('home')[0].oninput = err_mes_hide;
	document.getElementsByName('apartment')[0].oninput = err_mes_hide;
}

//обработчик ввода полей
function err_mes_hide(e)
{
	err_mes.style.display = "none";
}


//нажатие на кнопку "подтвердить"
function sms_button_click(e)
{
	var sms_code = sms_input.value;
	
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('sms_code', sms_code);
	
	var url = "/" + brand + "/delivery_form_handler";
	var req = new XMLHttpRequest();
	req.open("POST", url, true);
	req.addEventListener('load', sms_code_answer);
	req.send(data);
}

//сервер ответил, что он успешно записал в orders_buffer и можно перенаправлять на страницу payment;
function but_ch(e)
{
	if(e.target.responseText == 'Успешно_записано')
		window.location.href = "/" + brand + "/order/payment";
	else if(e.target.responseText == 'Адрес за пределами город'){
		//Адрес не прошел проверку, выводим сообщение что туда не доставляем
		err_mes_func('Ой! Мы пока не доставляем бургеры по вашему адресу');
		err_mes.focus();
	}
	else if(e.target.responseText == 'Адрес за пределами район'){
		err_mes_func('Ой! Сегодня мы доставляем бургеры только в определенных районах');
		err_mes.focus();
	}
	else if(e.target.responseText == 'Адрес в пределах'){
		//адрес прошел проверку, отправляем на сервер запрос о записи в БД
		//запрос на запись в БД
		//готовим ajax-запрос
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('ph', $ph);
		data.append('name', $name);
		data.append('dont_call', $dont_call);
		data.append('time', $time);
		data.append('street', $street);
		data.append('home', $home);
		data.append('corpus', $corpus);
		data.append('build', $build);
		data.append('entrance', $entrance);
		data.append('floor', $floor);
		data.append('apartment', $apartment);

		//ajax-запрос
		var url = "/" + brand + "/delivery_form_handler";
		var req = new XMLHttpRequest();
		req.open("POST", url, true);
		req.addEventListener('load', but_ch);
		req.send(data);
	}
}


var sms_code_enter_count = 0;
//обрабатывает ответ сервера на ajax-запрос с смс-кодом
function sms_code_answer(e)
{
	//здесь нужно вывести галочку - показывая что смс-код верен, пока просто подсвечиваем поле телефона зеленым
	if(e.target.responseText == 'Верно'){
		document.getElementsByName('phone')[0].style.border = "1px solid green";
		//выводим галочку
		document.getElementById("ok_img").style.display = "inline";
		
		//останавливаем таймер
		clearInterval(timerId);
		//убираем sms_verify
		sms_verify.style.display = "none";

		//меняем кнопки "к оплате"
		to_pay_buttons[0].style.display = "none";
		to_pay_buttons[1].style.display = "block";
		to_pay_buttons[1].focus();
	}
	else {
		sms_code_enter_count++;
		document.getElementById("sms_input").style.border = "1px solid red";
		
		//пользователь 3 раза ввел смс-код неверно
		if(sms_code_enter_count > 2){
			clearInterval(timerId);
			sms_code_enter_count = 0;
			//переключаем поле для ввода смс-кода на информацию о том что не верно введен код
			sms_verify.style.display = "none";
			didnt_sms.style.display = "block";
			document.getElementById("repeat_sms_code").focus();	
		}
	}
}


var $name;
var $ph;
var $dont_call;
var $time;
var $street;
var $home;
var $corpus;
var $build;
var $entrance;
var $floor;
var $apartment;
//обработчик кнопки "к оплате"
function to_pay_push(e)
{
	$name = document.getElementsByName("name")[0].value;
	$phone = document.getElementsByName("phone")[0].value;
	if($phone != ''){
		$phone = $phone.match(/[0-9]/g);
		$phone[0] = 8;
		$phone = $phone.join('');
	}
	$ph = $phone;
	
	//получаем все данные из полей input
	//name, phone, dont_call, time, street, home, corpus, build, entrance, floor, apartment
	//НАДО ПРОВЕРЯТЬ ЧТОБЫ ЗНАЧЕНИЯ БЫЛИ В НУЖНЫХ ФОРМАТАХ И ДИАПАЗОНАХ
	$name = document.getElementsByName("name")[0].value;
	if(dont_call.getAttribute("class") == "round_delivery checked")
		$dont_call = true;
	else
		$dont_call = false;
	
	$time = document.getElementsByName("time")[0].value;
	$street = document.getElementsByName("street")[0].value;
	$home = document.getElementsByName("home")[0].value;
	$corpus = document.getElementsByName("corpus")[0].value;
	$build = document.getElementsByName("build")[0].value;
	$entrance = document.getElementsByName("entrance")[0].value;
	$floor = document.getElementsByName("floor")[0].value;
	$apartment = document.getElementsByName("apartment")[0].value;

	//проверяем что пользователь ввел все необходимые поля
	if($name == ''){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("name")[0].style.border = "1px solid red";
		//document.getElementsByName("name")[0].previousElementSibling.style.color = "red";
		
		/*var name_reg = RegExp("^$");
		if(name_reg.test($name))
			alert("только числа");
		else
			alert("не только числа");
		*/
		return;
	}
	else
		//document.getElementsByName("name")[0].previousElementSibling.style.color = "black";
		document.getElementsByName("name")[0].style.border = "1px solid gray";

	//если заказ в не рабочее время, то вывести сообщение с режимом работы
	if($time == ""){
		var date = new Date();
		var hours = Number(date.getHours());
		var minutes = Number(date.getMinutes());
	}
	else {
		var date = $time.split(':');
		var hours = Number(date[0]);
		var minutes = Number(date[1]);
	}

	//если улица не из выбранных
	/*if(str_input != street_input.value)
		street_input.style.border = "1px solid red";*/ 

	if(hours < 11 || hours > 23){
		err_mes_func('Время работы службы доставки: 11:00 - 23:00 ПН-ВС');
		err_mes.focus();
		return;
	}
	else if(hours == 23 && minutes > 0){
		err_mes_func('Время работы службы доставки: 11:00 - 23:00 ПН-ВС');
		err_mes.focus();
		return;
	}


	if($phone == '' || $phone.length != 11){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("phone")[0].style.border = "1px solid red";
		return;
	}
	else
		document.getElementsByName("phone")[0].style.border = "1px solid gray";

	
	
	if($street == ''){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("street")[0].style.border = "1px solid red";
		return;
	}
	if(!street_input_norm){
		err_mes_func('Необходимо выбрать улицу из выпадающего списка');
		err_mes.focus();
		street_input.style.border = "1px solid red";
		return;
	}
	
	if($home == ''){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("home")[0].style.border = "1px solid red";
		return;
	}
	else
		document.getElementsByName("home")[0].style.border = "1px solid gray";
	
	if($apartment == ''){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("apartment")[0].style.border = "1px solid red";
		return;
	}
	else
		document.getElementsByName("apartment")[0].style.border = "1px solid gray";


	//готовим ajax-запрос
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('street_check', $street);
	
	//ajax-запрос
	var url = "/" + brand + "/delivery_form_handler";
	var req = new XMLHttpRequest();
	req.open("POST", url, false);
	req.addEventListener('load', street_ch_answer);
	req.send(data);
	
	//запустим таймер в 2 мин
	//watches();
	
	//отключаем кнопку "К выбору способа оплаты"
	//to_pay_buttons[0].style.display = "none";
}


var ph_check;
//сервер проверил улицу при нажатии кнопки "К выбору способа оплаты"
function street_ch_answer(e)
{
	if(e.target.responseText == 'Адрес в пределах'){
		//делаем видимым поле для ввода смс-кода
		sms_verify.style.display = "block";
		document.getElementById("sms_input").focus();

		//готовим ajax-запрос
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('phone', $phone);

		//сохраним номер телефона на который отправляли смс
		ph_check = $phone;
		//ajax-запрос
		var url = "/" + brand + "/delivery_form_handler";
		var req = new XMLHttpRequest();
		req.open("POST", url, false);
		req.addEventListener('load', sms_code_answer);
		req.send(data);

		//запустим таймер в 2 мин
		watches();

		//отключаем кнопку "К выбору способа оплаты"
		to_pay_buttons[0].style.display = "none";
	}
	else if(e.target.responseText == 'Адрес за пределами город'){
		//Адрес не прошел проверку, выводим сообщение что туда не доставляем
		err_mes_func('Ой! Мы пока не доставляем бургеры по вашему адресу');
		err_mes.focus();
	}
	else if(e.target.responseText == 'Адрес за пределами район'){
		err_mes_func('Ой! Сегодня мы доставляем бургеры только в определенных районах');
		err_mes.focus();
	}
}

//обработчик кнопки "продолжить"
function to_pay(e)
{
	$name = document.getElementsByName("name")[0].value;
	$phone = document.getElementsByName("phone")[0].value;
	if($phone != ''){
		$phone = $phone.match(/[0-9]/g);
		$phone[0] = 8;
		$phone = $phone.join('');
	} 

	//телефон находящийся в поле ввода телефона во время нажатия кнопки "Продолжить" отличается от телефона на который высылалась смс
		$ph = $phone;

	//получаем все данные из полей input
	//name, phone, dont_call, time, street, home, corpus, build, entrance, floor, apartment
	//НАДО ПРОВЕРЯТЬ ЧТОБЫ ЗНАЧЕНИЯ БЫЛИ В НУЖНЫХ ФОРМАТАХ И ДИАПАЗОНАХ
	$name = document.getElementsByName("name")[0].value;
	if(dont_call.getAttribute("class") == "round_delivery checked")
		$dont_call = true;
	else
		$dont_call = false;
	
	$time = document.getElementsByName("time")[0].value;
	$street = document.getElementsByName("street")[0].value;
	$home = document.getElementsByName("home")[0].value;
	$corpus = document.getElementsByName("corpus")[0].value;
	$build = document.getElementsByName("build")[0].value;
	$entrance = document.getElementsByName("entrance")[0].value;
	$floor = document.getElementsByName("floor")[0].value;
	$apartment = document.getElementsByName("apartment")[0].value;

	//проверяем что пользователь ввел все необходимые поля
	if($name == ''){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("name")[0].style.border = "1px solid red";
		
		/*var name_reg = RegExp("^$");
		if(name_reg.test($name))
			alert("только числа");
		else
			alert("не только числа");
		*/
		return;
	}
	
	if($phone == '' || $phone.length != 11){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("phone")[0].style.border = "1px solid red";
		return;
	}
	//если номер изменился
	if(ph_check != $phone){
		err_mes_func('В поле должен быть указан телефон на который вы получали SMS-код');
		err_mes.focus();
		document.getElementsByName("phone")[0].style.border = "1px solid red";
		return;
	}

	
	if($street == ''){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("street")[0].style.border = "1px solid red";
		return;
	}
	if(!street_input_norm){
		err_mes_func('Необходимо выбрать улицу из выпадающего списка');
		err_mes.focus();
		street_input.style.border = "1px solid red";
		return;
	}
	
	if($home == ''){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("home")[0].style.border = "1px solid red";
		return;
	}
	
	if($apartment == ''){
		err_mes_func('Необходимо заполнить все поля отмеченные *');
		err_mes.focus();
		document.getElementsByName("apartment")[0].style.border = "1px solid red";
		return;
	}
	
	//запрос на запись в БД
	//готовим ajax-запрос
/*	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('ph', $ph);
	data.append('name', $name);
	data.append('dont_call', $dont_call);
	data.append('time', $time);
	data.append('street', $street);
	data.append('home', $home);
	data.append('corpus', $corpus);
	data.append('build', $build);
	data.append('entrance', $entrance);
	data.append('floor', $floor);
	data.append('apartment', $apartment);
*/
	//если заказ в не рабочее время, то вывести сообщение с режимом работы
	if($time == ""){
		var date = new Date();
		var hours = Number(date.getHours());
		var minutes = Number(date.getMinutes());
	}
	else {
		var date = $time.split(':');
		var hours = Number(date[0]);
		var minutes = Number(date[1]);
	}

	//если улица не из выбранных
	/*if(str_input != street_input.value)
		street_input.style.border = "1px solid red";*/ 

	if(hours < 11 || hours > 23){
		err_mes_func('Время работы службы доставки: 11:00 - 23:00 ПН-ВС');
		err_mes.focus();
		return;
	}
	else if(hours == 23 && minutes > 0){
		err_mes_func('Время работы службы доставки: 11:00 - 23:00 ПН-ВС');
		err_mes.focus();
		return;
	}


	//запрос на проверку адреса
	//готовим ajax-запрос
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('street_check', $street);
	
	//ajax-запрос
	var url = "/" + brand + "/delivery_form_handler";
	var req = new XMLHttpRequest();
	req.open("POST", url, true);
	req.addEventListener('load', but_ch);
	req.send(data);
	
	//запустим таймер в 2 мин
	//watches();
	
	//отключаем кнопку "К выбору способа оплаты"
	//to_pay_buttons[0].style.display = "none";
}


//функция повторно запрашивающая у сервера смс-код
function repeat_sms_code(e){
	//делаем видимым поле для ввода смс-кода
	didnt_sms.style.display = "none";
	sms_verify.style.display = "block";
	document.getElementById("sms_input").value = "";
	document.getElementById("sms_input").focus();
	
	//готовим ajax-запрос
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	$phone = document.getElementsByName("phone")[0].value;
	data.append('phone', $phone);
	
	//ajax-запрос
	var url = "/" + brand + "/delivery_form_handler";
	var req = new XMLHttpRequest();
	req.open("POST", url, true);
	//req.addEventListener('load', sms_code_answer);
	req.send(data);
	
	//запустим таймер в 2 мин
	watches();
}

var timerId;
var minutes;
var seconds;
var sec;
//функция таймер
function watches(){
	minutes = document.getElementById("watches_minutes");
	seconds = document.getElementById("watches_seconds");
	sec = 120;
	timerId = setInterval(time_func, 1000);
}

//функция меняющая секунды и минуты
function time_func(){
	sec--;
	var min = Math.trunc(sec / 60);

	if(min >= 0)
			minutes.innerHTML = '0' + min;
	if((sec % 60) == 0)
		min--;
	
	if((sec % 60) < 10)
		seconds.innerHTML = '0' + (sec % 60);
	else
		seconds.innerHTML = sec % 60;
	
	if(sec == 0){
		clearInterval(timerId);
		//переключаем поле для ввода смс-кода на информацию о том что не верно введен код
		sms_verify.style.display = "none";
		didnt_sms.style.display = "block";
	}
}
