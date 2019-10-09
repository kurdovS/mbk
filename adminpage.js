addEventListener('load', adminpage_init);

var exit_button;
var adminpage_url;

//начальная инициализация
function adminpage_init()
{
	adminpage_url = '/adminpage/adminpage_form_handler';				
	exit_button = document.getElementById("exit");
	exit_button.onclick = exit_button_push;
}

//нажатие кнопки "Выйти"
function exit_button_push(e)
{
	//отправляем информацию
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('exit', '');
	
	var request = new XMLHttpRequest();
	request.open("POST", adminpage_url, true);
	request.send(data);
}

