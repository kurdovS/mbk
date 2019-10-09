addEventListener('load', users);

var add_button;
var del_buttons;
var edit_buttons;
var users_url;

//начальная инициализация
function users(e)
{
	//обработчик JS запросов в users
	users_url = '/adminpage/users_form_handler';
	
	var path_array = document.location.pathname.split('/');
	
	//если запрашивается add, del или edit
	if(path_array.length == 4){
		if(path_array[3] == 'add'){
			//для add_user
			add_button = document.getElementById('add_user_button');
			add_button.onclick = add_button_push;
		}
		else if(path_array[3] == 'del'){
			//для del_user
			del_buttons = document.getElementsByClassName('del_user_butt');
			for(var i = 0; i < del_buttons.length; i++)
				del_buttons[i].onclick = del_button_push;
		}
		else if(path_array[3] == 'edit'){
			//для edit_user
			edit_buttons = document.getElementsByClassName('edit_user_button');
			for(var i = 0; i < edit_buttons.length; i++)
				edit_buttons[i].onclick = edit_button_push;
		}
	}
}

//нажатие кнопки "Добавить"
function add_button_push(e)
{
	var inputs = document.getElementsByClassName('tab_input');
	
	//проверим что поля не пустые
	for(var i = 0; i < inputs.length; i++){
		if(inputs[i].value == ''){
			inputs[i].style.border = "1px solid red";
			inputs[i].blur = function bl(e){
				e.target.style.border = "1px solid black";
			}
			return;
		}
	}
	
	var result = confirm("Вы действительно хотите добавить этого пользователя в систему?");
	if(result){
		//отправляем информацию о добавляемом пользователе на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('add', '');
		data.append('name', inputs[0].value);
		data.append('status', inputs[1].value);
		data.append('pass', inputs[2].value);
	
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", users_url, true);
		request.send(data);
	}
}

//нажатие кнопки "Удалить"
function del_button_push(e)
{
	var result = confirm("Вы действительно хотите удалить этого пользователя из системы?");
	if(result){
		var name = e.target.parentElement.firstElementChild.nextElementSibling.innerHTML;
	
		//отправляем информацию об удаляемом пользователе на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('del', '');
		data.append('name', name);
	
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", users_url, true);
		request.send(data);
	}
}

//нажатие кнопки "Сохранить"
function edit_button_push(e)
{
	var result = confirm("Вы действительно хотите изменить данные этого пользователя? ВНИМАНИЕ: " + 
	"Если вы измените свои данные вам нужно будет войти заново, используя указанные вами данные");
	if(result){
		//id, имя, статус, пароль
		var user_id = e.target.parentElement.parentElement.firstElementChild.innerHTML;
		var user_name = e.target.parentElement.parentElement.firstElementChild.nextElementSibling.firstElementChild.innerHTML;
		var user_status = e.target.parentElement.parentElement.firstElementChild.nextElementSibling.nextElementSibling.firstElementChild.innerHTML;
		var user_pass = e.target.parentElement.previousElementSibling.previousElementSibling.firstElementChild.innerHTML;
		
		//если поля для имени, статуса и пароля не пусты
		if(user_name != '' && user_status != '' && user_pass != ''){
			//отправляем информацию об изменяемом пользователе на сервер
			var data = new FormData();
			//установим переменную идентифицирующую ajax-запрос с сайта
			data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
			data.append('edit', user_id);
			data.append('name', user_name);
			data.append('status', user_status);
			data.append('pass', user_pass);
	
			var request = new XMLHttpRequest();
			request.addEventListener('load', server_answer);
			request.open("POST", users_url, true);
			request.send(data);
			
		}
		else
			alert('Поля для "Имени", "Статуса" и "Пароля" не должны быть пустыми');
	}
}

//обработка информации от сервера
function server_answer(e)
{
	var data = e.target;
	if(data.status == 200){
		alert(data.responseText);
	}
	
	//обновим страницу если пользователь удален из системы или перенаправим на главную если добавлен
	if(data.responseText.substr(0, 12) == 'Пользователь')
		window.location.href = '/adminpage/users/del';
	else if(data.responseText.substr(0, 9) == 'Добавлено')
		window.location.href = '/adminpage/users';
	else if(data.responseText.substr(0, 19) == 'Вы успешно обновили')
		window.location.href = '/adminpage/users/edit';
}