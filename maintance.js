addEventListener('load', maintance);

var mode;

//для add
var add_butt;

//для dell
var del_butt;

//для edit
var edit_butt;

function maintance()
{
	//обработчик JS запросов в users
	maintance_url = '/adminpage/maintance_form_handler';
	
	//установим запрашиваемое действие
	if(window.location.pathname.split("/")[3] == 'add'){
		mode = 'add';
		add_butt = document.getElementById('add_maintance_button');
		add_butt.onclick = add_butt_func;
	}
	else if(window.location.pathname.split("/")[3] == 'del'){
		mode = 'del';
		del_butt = document.getElementById('del_butt_maintance');
		del_butt.onclick = del_butt_func;
	}
	else if(window.location.pathname.split("/")[3] == 'edit'){
		mode = 'edit';
		edit_butt = document.getElementById('edit_butt_save');
		edit_butt.onclick = edit_butt_func;
	}
	else
		mode = 'main';
}


//add
//обработчик нажатия кнопки 
function add_butt_func(e)
{
	var inputs = document.getElementsByClassName('tab_input');
	
	//проверим что поля не пустые
	for(var i = 0; i < inputs.length; i++){
		if(inputs[i].innerHTML == ''){
			inputs[i].style.border = "1px solid red";
			inputs[i].blur = function bl(e){
				e.target.style.border = "1px solid black";
			}
			return;
		}
	}
	
	var result = confirm("Вы действительно хотите остановить работу службы доставки?");
	if(result){
		//отправляем информацию о добавляемом пользователе на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('add', '');
		data.append('mt_header', inputs[0].innerHTML);
		data.append('mt_description', inputs[1].innerHTML);
		data.append('deadline', inputs[2].innerHTML);
	
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", maintance_url, true);
		request.send(data);
	}
}


//del
//обработчик нажатия кнопки 
function del_butt_func(e)
{
	var result = confirm("Вы действительно хотите удалить эту запись? *ВНИМАНИЕ:* удаление записи сделает службу доставки общедоступной");
	if(result){
		//отправляем информацию о добавляемом пользователе на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('del', '');
	
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", maintance_url, true);
		request.send(data);
	}
}


//edit
//обработчик нажатия кнопки 
function edit_butt_func(e)
{
	var inputs = document.getElementsByClassName('td_edit');
	
	//проверим что поля не пустые
	for(var i = 0; i < inputs.length; i++){
		if(inputs[i].innerHTML == ''){
			inputs[i].style.border = "1px solid red";
			inputs[i].blur = function bl(e){
				e.target.style.border = "1px solid black";
			}
			return;
		}
	}

	var result = confirm("Вы действительно хотите сохранить изменения?");
	if(result){
		//отправляем информацию о добавляемом пользователе на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('edit', '');
		data.append('mt_header', inputs[0].innerHTML);
		data.append('mt_description', inputs[1].innerHTML);
		data.append('deadline', inputs[2].innerHTML);
	
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", maintance_url, true);
		request.send(data);
	}
}


//обработка информации от сервера
function server_answer(e)
{
	var data = e.target;
	if(data.status == 200){
		alert(data.responseText);
		window.location.href = '/adminpage/maintance';
	}
}