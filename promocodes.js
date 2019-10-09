addEventListener('load', promocodes);

var add_button;
var del_buttons;
var edit_buttons;
var promocodes_url;

//начальная инициализация
function promocodes(e)
{
	//обработчик JS запросов в users
	promocodes_url = '/adminpage/promocodes_form_handler';

	var path_array = document.location.pathname.split('/');

	//если запрашивается add, del или edit
	if(path_array.length == 4){
		if(path_array[3] == 'add'){
			//для add_promocode
			add_button = document.getElementById('add_button');
			add_button.onclick = add_button_push;
		}
		else if(path_array[3] == 'del'){
			//для del_promocode
			del_buttons = document.getElementsByClassName('promocode_del');
			for(var i = 0; i < del_buttons.length; i++)
				del_buttons[i].onclick = del_button_push;
		}
		else if(path_array[3] == 'edit'){
			//для edit_promocode
			edit_buttons = document.getElementsByClassName('promocode_save');
			for(var i = 0; i < edit_buttons.length; i++)
				edit_buttons[i].onclick = edit_button_push;
		}
	}
}

//нажатие кнопки "Добавить промокод"
function add_button_push(e)
{
	var ok = true;
	//получим всю информацию о вводимом промокоде
	var inputs = document.getElementsByClassName("tab_input");

	//проверим все введенные значения
	if(inputs[0].value == ''){
		inputs[0].style.border = "1px solid red";
		ok = false;
	}

	if(inputs[1].value.search(/[0-1]/)){
		inputs[1].style.border = "1px solid red";
		ok = false;
	}

	if(inputs[2].value.search(/[0-9]+/)){
		inputs[2].style.border = "1px solid red";
		ok = false;
	}

	if(inputs[3].value.search(/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/)){
		inputs[3].style.border = "1px solid red";
		ok = false;
	}

	if(inputs[4].value.search(/[0-9]+/)){
		inputs[4].style.border = "1px solid red";
		ok = false;
	}

	//если все данные в порядке, то просим сервер добавить промокод
	if(ok){
		//отправляем информацию об удаляемом пользователе на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');

		for(var i = 0; i < 5; i++)
			data.append('add_' + i, inputs[i].value);

		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", promocodes_url, true);
		request.send(data);
	}
}

//нажатие кнопки "Удалить"
function del_button_push(e)
{
	var result = confirm("Вы действительно хотите удалить этого пользователя из системы?");
	if(result){
		var promokod = e.target;
		for(var i = 0; i < 5; i++)
			promokod = promokod.previousElementSibling;

		//отправляем информацию об удаляемом промокоде на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('del', promokod.innerHTML);

		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", promocodes_url, true);
		request.send(data);
	}
}

//нажатие кнопки "Сохранить"
function edit_button_push(e)
{
	var result = confirm("Вы действительно хотите изменить данные этого промокода?");
	if(result){
		//promocode, type, num, date, discount
		var promocode_arr = [];
		var promocode = [];
		promocode_arr[0] = e.target.previousElementSibling;
		for(var i = 1; i < 5; i++)
			promocode_arr[i] = promocode_arr[i - 1].previousElementSibling;
		for(var i = 0; i < 5; i++)
			promocode[i] = promocode_arr[i].innerHTML;

		//проверим все введенные значения
		var ok = true;
		if(promocode[4] == ''){
			promocode_arr[4].style.border = "1px solid red";
			ok = false;
		}

		if(promocode[3].search(/[0-1]/)){
			promocode_arr[3].style.border = "1px solid red";
			ok = false;
		}

		if(promocode[2].search(/[0-9]+/)){
			promocode_arr[2].style.border = "1px solid red";
			ok = false;
		}

		if(promocode[1].search(/[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])/)){
			promocode_arr[1].style.border = "1px solid red";
			ok = false;
		}

		if(promocode[0].search(/[0-9]+/)){
			promocode_arr[0].style.border = "1px solid red";
			ok = false;
		}


		//если все ок
		if(ok){
			//отправляем информацию об изменяемом промокоде на сервер
			var data = new FormData();
			//установим переменную идентифицирующую ajax-запрос с сайта
			data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
			for(var i = 4; i >= 0; i--)
				data.append('edit_' + i, promocode[i]);

			var request = new XMLHttpRequest();
			request.addEventListener('load', server_answer);
			request.open("POST", promocodes_url, true);
			request.send(data);
		}
	}
}

//обработка информации от сервера
function server_answer(e)
{
	var data = e.target;
	if(data.status == 200){
		if(data.responseText.substr(0, 6) == "Только" || data.responseText.substr(0, 10) == "Невозможно")
			alert(data.responseText);
		else {
			window.location.href = '/adminpage/promocodes';
			//alert(data.responseText);
		}
	}
}
