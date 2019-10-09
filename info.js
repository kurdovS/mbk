addEventListener('load', info_init);

var info_url;
var unp_pages_butt;		//кнопки страниц ИНФОРМАЦИОННЫХ СТАТЕЙ

var path_array;

//переменные для add_info
var info_header;		//поле для ввода заголовка статьи
var edit_text;			//поле для ввода текста статьи
var info_view;		//зона отображения статьи
//кнопки для add_info
var release;
var download;

//imgs_drop
var img_tag;
var imgs_drop;
var in_editor;

//для del_info
var del_butt;

//для edit_articles
var edit_butt;

function info_init()
{	
	//обработчик JS запросов в info
	info_url = '/adminpage/info_form_handler';
	
	
	path_array = document.location.pathname.split('/');
	
	//если запрашивается add, del или edit
	if(path_array.length == 4){
		//для add_info
		if(path_array[3] == 'add'){
			//все для imgs_drop
			imgs_drop = document.getElementById('imgs_drop');
			imgs_drop.addEventListener('dragenter', function(e){ 
				e.preventDefault(); 
				imgs_drop.style.background = 'rgba(0, 150, 0, .2)'; 
			});
			imgs_drop.addEventListener('dragover', function(e){ e.preventDefault(); });
			imgs_drop.addEventListener('drop', dropped);
			imgs_drop.addEventListener('dragleave', function(e){ imgs_drop.style.background = 'white'; });
			//настроим imgs_drop как источники изображений для text_edit
			imgs_drop.addEventListener('dragstart', function(e){
				in_editor = '<div class="img_wrap"><img src="' + e.target.getAttribute('src') + '" style="width: 80%; margin: 0 auto;" /><div class="img_span">Подпись к изображению</div></div>';
				imgs_drop.style.background = 'white';
			});
			
			info_header = document.getElementById("header_info");
			edit_text = document.getElementById("edit_text");
			//настроим edit_text как зону приема изображений
			edit_text.addEventListener('dragenter', function(e){ 
				e.preventDefault();
				edit_text.style.background = 'rgba(0, 150, 0, .1)'; 
			});
			edit_text.addEventListener('dragover', function(e){ e.preventDefault(); });
			edit_text.addEventListener('drop', function(e){
				edit_text.value += in_editor;
				edit_text.style.background = 'white';
			});
			edit_text.addEventListener('dragleave', function(e){ edit_text.style.background = 'white'; });
			
			info_view = document.getElementById("info_view");
			//каждую секунду вызываем функцию для переноса текста статьи в область отображения
			var textCopyTimerId = setInterval(text_copy, 1000);
			//получим кнопки ОПУБЛИКОВАТЬ и ЗАГРУЗИТЬ
			release = document.getElementById('butt_public');
			download = document.getElementById('butt_download');
			release.onclick = butt_in_add_push;
			download.onclick = butt_in_add_push;
			
			//если установлено cookie info_to_add, то нужно загрузить нужную инфо-страницу
			if(String(document.cookie).match(/info_to_add/) != null)
				info_load_to_add();
		}
		//для del_info
		else if(path_array[3] == 'del'){
			//устанавливаем обработчик нажатия для кнопок страниц Информационных СТАТЕЙ
			unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
			for(var i = 0; i < unp_pages_butt.length; i++)
				unp_pages_butt[i].onclick = pages_butt_push;
			
			//устанавливаем обработчик нажатия для кнопок "Удалить"
			del_butt = document.getElementsByClassName('del_info');
			for(var i = 0; i < del_butt.length; i++)
				del_butt[i].onclick = del_butt_push;
		}
		//для edit_info
		else if(path_array[3] == 'edit'){
			//устанавливаем обработчик нажатия для кнопок страниц
			unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
			for(var i = 0; i < unp_pages_butt.length; i++)
				unp_pages_butt[i].onclick = pages_butt_push;
			
			//устанавливаем обработчик нажатия для кнопок "Изменить"
			edit_butt = document.getElementsByClassName('edit_info');
			for(var i = 0; i < edit_butt.length; i++)
				edit_butt[i].onclick = edit_butt_push;
		}
	}
	
	//если запрашивается main или же по умолчанию
	if((path_array.length == 4 && path_array[3] == 'main') || path_array.length == 3){
		//устанавливаем обработчик нажатия для кнопок страниц ИНФОРМАЦИОННЫХ СТАТЕЙ
		unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
		for(var i = 0; i < unp_pages_butt.length; i++)
			unp_pages_butt[i].onclick = pages_butt_push;
	}
}


//обработчик нажатия кнопок страниц ИНФОРМАЦИОННЫХ СТАТЕЙ
function pages_butt_push(e)
{
	//отправляем информацию о нужной странице статей на сервер
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('info_page', Number(e.target.innerHTML));
	
	//если запрашивается для страницы del, то укажем об этом серверу
	if(path_array[3] == 'del')
		data.append('for_del', '');
	else if(path_array[3] == 'edit')
		data.append('for_edit', '');
	
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", info_url, true);
	request.send(data);
}


//функция отправляет серверу запрос загрузить статью в add_info
function info_load_to_add()
{
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('load_to_add', '');
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", info_url, true);
	request.send(data);
}


//обработчик перетаскивания изображений
//отправляет изображения на сервер, а тот сохраняет их в файлы
function dropped(e)
{
	e.preventDefault();
	var files = e.dataTransfer.files;
	var reader = new FileReader();
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	
	img_tag = '<img class="drop_imgs" src="/includes/for_info/info_imgs/' + files[0].name + '" />';
	imgs_drop.style.background = 'white';
	
	reader.onload = function readFile(e){
		//обязательно закодируем сырые данные файла в формат base64 перед отправкой
		data.append('img', window.btoa(reader.result));
		data.append('img_name', files[0].name); 
		//отправляем
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", info_url, true);
		request.send(data);
		//alert(window.btoa(reader.result).length);
	}
	//читаем как сырые данные
	reader.readAsBinaryString(files[0]);
}

//функция для add_info обрабатывает нажатие кнопок ОПУБЛИКОВАТЬ и ЗАГРУЗИТЬ
function butt_in_add_push(e)
{
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	//узнаем какая кнопка нажата
	if(e.target.getAttribute('id') == 'butt_public'){
		var res = confirm("Вы хотите опубликовать эту инфо-страницу? ВНИМАНИЕ: ИНФО-СТРАНИЦА СРАЗУ ОКАЖЕТСЯ В ОБЩЕМ ДОСТУПЕ");
		data.append('butt_in_add', 'public');
		if(info_header.value != '' && edit_text.value != ''){
			data.append('info_header', info_header.value);
			data.append('info_text', edit_text.value);
		}
		else {
			alert('Поля "Названия" и "Текста" инфо-страницы не должны быть пустыми');
			return;
		}	
	}
	else {
		var res = confirm("Вы хотите перейти к списку инфо-страниц для редактирования?");
		if(res)
			window.location.href = '/adminpage/info/edit';
	}
	
	//пользователь подтвердил выбранное действие 
	if(res && e.target.getAttribute('id') != 'butt_download'){
		//отправляем на сервер текст и заголовок статьи
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", info_url, true);
		request.send(data);
	}
}


//функция для add_info копирует текст статьи в область отображения
var header_value = '';
var text_value = '';
function text_copy()
{
	if(header_value != info_header.value || text_value != edit_text.value){
		header_value =  '<h1 id="info_head">' + info_header.value + '</h1>';
		text_value = edit_text.value;
		info_view.innerHTML = header_value + text_value;
	}
}


//обработчик нажатия кнопок "Удалить" для del_info
function del_butt_push(e)
{
	//если нажата кнопка у которой в теге прописан обработчик, а не установлен в в этом скрипте
	if(String(e.tagName) == 'undefined')
		e = e.target;
	
	//отправляем на сервер информацию о статье которую нужно удалить
	var result = confirm("Вы действительно хотите удалить эту инфо-страницу?");
	if(result){
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		//главная переменная, также отображает id удаляемой статьи
		data.append('info_del', e.parentElement.parentElement.firstElementChild.firstElementChild.innerHTML);
		
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", info_url, true);
		request.send(data);
	}
}


//обработчик нажатия кнопок "Изменить" для edit_info
function edit_butt_push(e){
	//если нажата кнопка у которой в теге прописан обработчик, а не установлен в этом скрипте
	if(String(e.tagName) == 'undefined')
		e = e.target;
	
	//запишем в cookie id статьи, которую надо загрузить в add_info
	var info_id = e.parentElement.parentElement.firstElementChild.firstElementChild.innerHTML;
		
	document.cookie = 'info_to_add=' + info_id + '; path=/;';
	
	window.location.href = '/adminpage/info/add';
}


//обработка информации от сервера
function server_answer(e)
{
	var info_to;
	var data = e.target;
	
	if(data.status == 200){
		if(data.responseText == 'Инфо-страница была удалена'){
			alert(data.responseText);
			window.location.href = '/adminpage/info/del';
		}
		else if(data.responseText.substr(0, 5) == 'Чтобы'){
			alert(data.responseText);
		}
		else if(data.responseText == 'Инфо-страница была сохранена и доступна публично'){
			alert(data.responseText);
			window.location.href = '/adminpage/info';
		}
		else if(data.responseText.substr(0, 5) == 'hilas'){
			//разобьем строку по разделителю hilas
			var arr = data.responseText.split('hilas');
			//вставляем полученные данные
			info_header.value = arr[1];
			edit_text.innerHTML = arr[2];
		}
		else if(data.responseText == 'imgs_save'){
			imgs_drop.innerHTML += img_tag;
		}
		else {
			info_to = document.getElementById('info_pages');
			info_to.innerHTML = data.responseText;
			//alert(data.responseText);
		}
	}
	
}