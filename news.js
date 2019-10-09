addEventListener('load', news_init);

var news_url;
var unp_pages_butt;		//кнопки страниц НЕОПУБЛИКОВАННЫХ НОВОСТЕЙ
var p_pages_butt;		//кнопки страниц ОПУБЛИКОВАННЫХ НОВОСТЕЙ

var release_butt;		//кнопки "Опубликовать"
var unrelease_butt;		//кнопки "Снять с публикации"

var path_array;

//переменные для add_news
var new_header;			//поле для ввода заголовка новости
var edit_text;			//поле для ввода текста новости
var new_view;		//зона отображения новости
//кнопки для add_news
var save;
var release;
var download;

//dropbox и imgs_drop
var drop;
var img_tag;
var imgs_drop;
var in_editor;

//для edit_articles
var edit_butt;

//для del_news
var del_butt;

function news_init()
{
	//обработчик JS запросов в news
	news_url = '/adminpage/news_form_handler';
	
	path_array = document.location.pathname.split('/');
	
	//если запрашивается add, del или edit
	if(path_array.length == 4){
		//для add_news
		if(path_array[3] == 'add'){
			//все для dropbox и imgs_drop
			drop = document.getElementById('dropbox');
			drop.addEventListener('dragenter', function(e){ 
				e.preventDefault();
				drop.style.background = 'rgba(0, 150, 0, .2)'; 
			});
			drop.addEventListener('dragover', function(e){ e.preventDefault(); });
			drop.addEventListener('drop', dropped);
			drop.addEventListener('dragleave', function(e){ drop.style.background = 'white'; });
			//imgs_drop
			imgs_drop = document.getElementById('imgs_drop');
			imgs_drop.addEventListener('dragenter', function(e){ 
				e.preventDefault(); 
				imgs_drop.style.background = 'rgba(0, 150, 0, .2)'; 
			});
			imgs_drop.addEventListener('dragover', function(e){ e.preventDefault(); });
			imgs_drop.addEventListener('drop', dropped);
			imgs_drop.addEventListener('dragleave', function(e){ imgs_drop.style.background = 'white'; });
			//настроим drop и imgs_drop как источники изображений для text_edit
			drop.addEventListener('dragstart', function(e){
				in_editor = '<div class="img_wrap100" style="float: left; margin-right: 15px;"><img class="mob_img" src="' + e.target.getAttribute('src') + '" /><div class="img_span">Подпись к изображению</div></div>';
				drop.style.background = 'white';
			});
			imgs_drop.addEventListener('dragstart', function(e){
				in_editor = '<div class="img_wrap100" style="float: left; margin-right: 15px;"><img class="mob_img" src="' + e.target.getAttribute('src') + '"/><div class="img_span">Подпись к изображению</div></div>';
				imgs_drop.style.background = 'white';
			});
			
			new_header = document.getElementById("header_new");
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
			
			new_view = document.getElementById("new_view");
			//каждую секунду вызываем функцию для переноса текста новости в область отображения
			var textCopyTimerId = setInterval(text_copy, 1000);
			//получим кнопки СОХРАНИТЬ, ОПУБЛИКОВАТЬ и ЗАГРУЗИТЬ
			save = document.getElementById('butt_save');
			release = document.getElementById('butt_public');
			download = document.getElementById('butt_download');
			save.onclick = butt_in_add_push;
			release.onclick = butt_in_add_push;
			download.onclick = butt_in_add_push;
			
			//если установлено cookie new_to_add, то нужно загрузить нужную новость
			if(String(document.cookie).match(/new_to_add/) != null)
				new_load_to_add();
		}
		//для del_news
		else if(path_array[3] == 'del'){
			//устанавливаем обработчик нажатия для кнопок страниц НЕОПУБЛИКОВАННЫХ НОВОСТЕЙ
			unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
			for(var i = 0; i < unp_pages_butt.length; i++)
				unp_pages_butt[i].onclick = pages_butt_push;
	
			//устанавливаем обработчик нажатия для кнопок страниц ОПУБЛИКОВАННЫХ НОВОСТЕЙ
			p_pages_butt = document.getElementsByClassName('p_pages_butt');
			for(var i = 0; i < p_pages_butt.length; i++)
				p_pages_butt[i].onclick = pages_butt_push;
			
			//устанавливаем обработчик нажатия для кнопок "Удалить"
			del_butt = document.getElementsByClassName('del_new');
			for(var i = 0; i < del_butt.length; i++)
				del_butt[i].onclick = del_butt_push;
		}
		//для edit_news
		else if(path_array[3] == 'edit'){
			//устанавливаем обработчик нажатия для кнопок страниц НЕОПУБЛИКОВАННЫХ НОВОСТЕЙ
			unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
			for(var i = 0; i < unp_pages_butt.length; i++)
				unp_pages_butt[i].onclick = pages_butt_push;
	
			//устанавливаем обработчик нажатия для кнопок страниц ОПУБЛИКОВАННЫХ НОВОСТЕЙ
			p_pages_butt = document.getElementsByClassName('p_pages_butt');
			for(var i = 0; i < p_pages_butt.length; i++)
				p_pages_butt[i].onclick = pages_butt_push;
			
			//устанавливаем обработчик нажатия для кнопок "Изменить"
			edit_butt = document.getElementsByClassName('edit_new');
			for(var i = 0; i < edit_butt.length; i++)
				edit_butt[i].onclick = edit_butt_push;
		}
	}
	
	//если запрашивается main или же по умолчанию
	if((path_array.length == 4 && path_array[3] == 'main') || path_array.length == 3){
		//устанавливаем обработчик нажатия для кнопок страниц НЕОПУБЛИКОВАННЫХ НОВОСТЕЙ
		unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
		for(var i = 0; i < unp_pages_butt.length; i++)
			unp_pages_butt[i].onclick = pages_butt_push;
	
		//устанавливаем обработчик нажатия для кнопок страниц ОПУБЛИКОВАННЫХ НОВОСТЕЙ
		p_pages_butt = document.getElementsByClassName('p_pages_butt');
		for(var i = 0; i < p_pages_butt.length; i++)
			p_pages_butt[i].onclick = pages_butt_push;
	
	
		//устанавливаем обработчик нажатия для кнопок "Опубликовать"
		release_butt = document.getElementsByClassName('release');
		for(var i = 0; i < release_butt.length; i++)
			release_butt[i].onclick = release_butt_push;
	
		//устанавливаем обработчик нажатия для кнопок "Снять с публикации"
		unrelease_butt = document.getElementsByClassName('unrelease');
		for(var i = 0; i < unrelease_butt.length; i++)
			unrelease_butt[i].onclick = release_butt_push;
	}
}


//функция отправляет серверу запрос загрузить новость в add_news
function new_load_to_add()
{
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('load_to_add', '');
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", news_url, true);
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
	
	//определим куда было перетащено изображение в dropbox или в drop_imgs
	if(e.target.getAttribute('id') == 'dropbox'){
		//чтобы вставить изображения в окно
		img_tag = '<img class="dropbox_img" src="/includes/for_news/news_imgs/' + files[0].name + '" />';
		data.append('img_or_imgs', 'img');
		drop.style.background = 'white';
	}
	else {
		img_tag = '<img class="drop_imgs" src="/includes/for_news/news_imgs/' + files[0].name + '" />';
		data.append('img_or_imgs', 'imgs');
		imgs_drop.style.background = 'white';
	}
	
	reader.onload = function readFile(e){
		//обязательно закодируем сырые данные файла в формат base64 перед отправкой
		data.append('img', window.btoa(reader.result));
		data.append('img_name', files[0].name); 
		//отправляем
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", news_url, true);
		request.send(data);
		//alert(window.btoa(reader.result).length);
	}
	//читаем как сырые данные
	reader.readAsBinaryString(files[0]);
}

//функция для add_news обрабатывает нажатие кнопок СОХРАНИТЬ, ОПУБЛИКОВАТЬ и ЗАГРУЗИТЬ
function butt_in_add_push(e)
{
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	//узнаем какая кнопка нажата
	if(e.target.getAttribute('id') == 'butt_save'){
		var res = confirm("Вы хотите сохранить эту новость в таблицу не опубликованных новостей?");
		data.append('butt_in_add', 'save');
		if(new_header.value != '' && edit_text.value != ''){
			data.append('new_header', new_header.value);
			data.append('new_text', edit_text.value);
			data.append('title_img', drop.firstElementChild.getAttribute('src'));
		}
		else {
			alert('Поля "Заголовка" и "Текста" новости не должны быть пустыми');
			return;
		}
	}
	else if(e.target.getAttribute('id') == 'butt_public'){
		var res = confirm("Вы хотите опубликовать эту новость? ВНИМАНИЕ: НОВОСТЬ СРАЗУ ОКАЖЕТСЯ В ОБЩЕМ ДОСТУПЕ");
		data.append('butt_in_add', 'public');
		if(new_header.value != '' && edit_text.value != ''){
			data.append('new_header', new_header.value);
			data.append('new_text', edit_text.value);
			data.append('title_img', drop.firstElementChild.getAttribute('src'));
		}
		else {
			alert('Поля "Заголовка" и "Текста" новости не должны быть пустыми');
			return;
		}	
	}
	else {
		var res = confirm("Вы хотите перейти к новостей статей для редактирования?");
		if(res)
			window.location.href = '/adminpage/news/edit';
	}
	
	//пользователь подтвердил выбранное действие 
	if(res && e.target.getAttribute('id') != 'butt_download'){
		//отправляем на сервер текст и заголовок новости
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", news_url, true);
		request.send(data);
	}
}


//функция для add_news копирует текст новости в область отображения
var header_value = '';
var text_value = '';
function text_copy()
{
	if(header_value != new_header.value || text_value != edit_text.value){
		header_value =  '<h1 id="new_head">' + new_header.value + '</h1>';
		text_value = edit_text.value;
		new_view.innerHTML = header_value + text_value;
	}
}


//обработчик нажатия кнопок страниц ОПУБЛИКОВАННЫХ и НЕОПУБЛИКОВАННЫХ НОВОСТЕЙ
function pages_butt_push(e)
{
	//отправляем информацию о нужной странице новостей на сервер
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('news_page', Number(e.target.innerHTML));
	//определим нажата кнопка для ОПУБЛИКОВАННЫХ или для НЕОПУБЛИКОВАННЫХ новостей
	if(e.target.getAttribute('class') == 'unp_pages_butt')
		data.append('type_news', 'unpublished');
	else if(e.target.getAttribute('class') == 'p_pages_but')
		data.append('type_news', 'published');
	
	//если запрашивается для страницы del, то укажем об этом серверу
	if(path_array[3] == 'del')
		data.append('for_del', '');
	else if(path_array[3] == 'edit')
		data.append('for_edit', '');
	
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", news_url, true);
	request.send(data);
}


//обработчик нажатия кнопок "Опубликовать" и "Снять с публикацией"
function release_butt_push(e)
{
	//если нажата кнопка у которой в теге прописан обработчик, а не установлен в этом скрипте
	if(String(e.tagName) == 'undefined')
		e = e.target;
	//отправляем на сервер информацию о новости которую нужно перенести из одной таблицы в другую
	var data = new FormData();
	data.append('new_transfer', e.innerHTML);
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	
	//определим нужно перенести из unpublished в published или наоборот
	if(e.innerHTML == "Опубликовать")
		var result = confirm("Вы действительно хотите опубликовать эту новость?");
	else {
		var result = confirm("Вы действительно хотите снять эту новость с публикации?");
		var cause_unpublished = prompt("Введите новый статус для новости", 'Снята модератором');
		data.append('cause', cause_unpublished);
	}
	
	if(result){
		//определим id нужной новости
		var new_id = e.parentElement.parentElement.firstElementChild.firstElementChild.innerHTML;
		data.append('new_id', new_id);
	
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", news_url, true);
		request.send(data);
	}
}


//обработчик нажатия кнопок "Удалить" для del_news
function del_butt_push(e)
{
	//если нажата кнопка у которой в теге прописан обработчик, а не установлен в этом скрипте
	if(String(e.tagName) == 'undefined')
		e = e.target;
	
	//отправляем на сервер информацию о новости которую нужно удалить
	var result = confirm("Вы действительно хотите удалить эту новость?");
	if(result){
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		//главная переменная, также отображает id удаляемой новости
		data.append('new_del', e.parentElement.parentElement.firstElementChild.firstElementChild.innerHTML);
		//определим из какой таблицы удалять новость из published или unpublished
		if(e.parentElement.previousElementSibling.firstElementChild.innerHTML == 'Опубликованно')
			data.append('type', 'published');
		else
			data.append('type', 'unpublished');
		
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", news_url, true);
		request.send(data);
	}
}


//обработчик нажатия кнопок "Изменить" для edit_news
function edit_butt_push(e){
	//если нажата кнопка у которой в теге прописан обработчик, а не установлен в этом скрипте
	if(String(e.tagName) == 'undefined')
		e = e.target;
	
	//запишем в cookie id новости, которую надо загрузить в add_news
	var new_id = e.parentElement.parentElement.firstElementChild.firstElementChild.innerHTML;
	if(e.parentElement.previousElementSibling.firstElementChild.innerHTML == 'Опубликованно')
		var new_from = 'published';
	else
		var new_from = 'unpublished';
		
	document.cookie = 'new_to_add=' + new_id + '; path=/;';
	document.cookie = 'new_from=' + new_from + '; path=/;';
	
	window.location.href = '/adminpage/news/add';
}


//обработка информации от сервера
function server_answer(e)
{
	var news_to;
	var data = e.target;

	if(data.status == 200){
		if(data.responseText.substr(29, 7) == 'Новости'){
			articles_to = document.getElementById('unpublished');
			articles_to.innerHTML = data.responseText;
		}
		else if(data.responseText.substr(0, 2) == 'Вы'){
			alert(data.responseText);
			window.location.href = '/adminpage/news';
		}
		else if(data.responseText == 'Новость была удалена'){
			alert(data.responseText);
			window.location.href = '/adminpage/news/del';
		}
		else if(data.responseText.substr(0, 5) == 'Чтобы'){
			alert(data.responseText);
		}
		else if(data.responseText.substr(0, 4) == 'Ваша'){
			alert(data.responseText);
			window.location.href = '/adminpage/news';
		}
		else if(data.responseText == 'img_save'){
			drop.innerHTML = img_tag;
		}
		else if(data.responseText == 'imgs_save'){
			imgs_drop.innerHTML += img_tag;
		}
		else if(data.responseText.substr(0, 5) == 'hilas'){
			//разобьем строку по разделителю hilas
			var arr = data.responseText.split('hilas');
			//вставляем полученные данные
			new_header.value = arr[1];
			edit_text.innerHTML = arr[2];
			drop.innerHTML = '<img class="dropbox_img" src="' + arr[3] + '" />';
		}
		else {
			news_to = document.getElementById('published');
			news_to.innerHTML = data.responseText;
			//alert(data.responseText);
		}
	}
}
