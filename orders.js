addEventListener('load', orders_init);

var done_buttons;
var del_buttons;
var orders_url;

function orders_init()
{
	orders_url = '/adminpage/orders_form_handler';

	done_buttons = document.getElementsByClassName('done_buttons');
	for(var i = 0; i < done_buttons.length; i++)
		done_buttons[i].onclick = done_button_push;

	del_buttons = document.getElementsByClassName('del_buttons');
	for(var i = 0; i < del_buttons.length; i++)
		del_buttons[i].onclick = del_button_push;

	buffer_buttons = document.getElementsByClassName('buffer_del_buttons');
	for(var i = 0; i < buffer_buttons.length; i++)
		buffer_buttons[i].onclick = buffer_button_push;
}


//нажатие на кнопку "Выполнено" для текущих заказов
function done_button_push(e){
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');

	//передадим серверу № заказа который нужно завершить
	data.append('order_done', e.target.parentElement.parentElement.firstElementChild.innerHTML);
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", orders_url, true);
	request.send(data);
}


//нажатие на кнопку "Удалить" для выполненных заказов
function del_button_push(e){
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');

	//передадим серверу № заказа который нужно удалить
	data.append('order_del', e.target.parentElement.parentElement.firstElementChild.innerHTML);
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", orders_url, true);
	request.send(data);
}


//нажатие на кнопку "Удалить для неоформленных заказов"
function buffer_button_push(e){
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');

	//передадим серверу № заказа который нужно удалить
	data.append('buffer_del', e.target.parentElement.parentElement.firstElementChild.innerHTML);
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", orders_url, true);
	request.send(data);
}


//получение ответов от сервера
function server_answer(e){
	var data = e.target;

	if(data.status == 200){
		if(data.responseText.substr(0, 6) == "Только")
			alert(data.responseText);
		else {
			if(data.responseText.substr(22, 3) == "уда")
				window.location.href = '/adminpage/orders/orders_done';
			else if(data.responseText.substr(36, 3) == "уда")
				window.location.href = '/adminpage/orders/orders_buffer';
			else
				window.location.href = '/adminpage/orders/orders_in_process';
		}
	}
}
