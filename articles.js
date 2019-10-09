addEventListener('load', articles_init);

var articles_url;
var unp_pages_butt;		//кнопки страниц НЕОПУБЛИКОВАННЫХ СТАТЕЙ
var p_pages_butt;		//кнопки страниц ОПУБЛИКОВАННЫХ СТАТЕЙ

var release_butt;		//кнопки "Опубликовать"
var unrelease_butt;		//кнопки "Снять с публикации"

var path_array;

//переменные для add_articles
var article_header;		//поле для ввода заголовка статьи
var edit_text;			//поле для ввода текста статьи
var article_view;		//зона отображения статьи
//кнопки для add_articles
var save;
var release;
var download;

//для edit_articles
var edit_butt;

//для del_articles
var del_butt;

//dropbox и imgs_drop
var drop;
var img_tag;
var imgs_drop;
var in_editor;

function articles_init()
{
	//обработчик JS запросов в articles
	articles_url = '/adminpage/articles_form_handler';


	path_array = document.location.pathname.split('/');

	//если запрашивается add, del или edit
	if(path_array.length == 4){
		//для add_articles
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
				in_editor = '<div class="img_wrap100" style="float: left; margin-right: 15px;"><img class="mob_img" src="' + e.target.getAttribute('src') + '"/><div class="img_span">Подпись к изображению</div></div>';
				drop.style.background = 'white';
			});
			imgs_drop.addEventListener('dragstart', function(e){
				in_editor = '<div class="img_wrap100" style="float: left; margin-right: 15px;"><img class="mob_img" src="' + e.target.getAttribute('src') + '"/><div class="img_span">Подпись к изображению</div></div>';
				imgs_drop.style.background = 'white';
			});
			
			article_header = document.getElementById("header_article");
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
			
			article_view = document.getElementById("article_view");
			//каждую секунду вызываем функцию для переноса текста статьи в область отображения
			var textCopyTimerId = setInterval(text_copy, 1000);
			//получим кнопки СОХРАНИТЬ, ОПУБЛИКОВАТЬ и ЗАГРУЗИТЬ
			save = document.getElementById('butt_save');
			release = document.getElementById('butt_public');
			download = document.getElementById('butt_download');
			save.onclick = butt_in_add_push;
			release.onclick = butt_in_add_push;
			download.onclick = butt_in_add_push;
			
			//если установлено cookie article_to_add, то нужно загрузить нужную статью
			if(String(document.cookie).match(/article_to_add/) != null)
				article_load_to_add();
		}
		//для del_articles
		else if(path_array[3] == 'del'){
			//устанавливаем обработчик нажатия для кнопок страниц НЕОПУБЛИКОВАННЫХ СТАТЕЙ
			unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
			for(var i = 0; i < unp_pages_butt.length; i++)
				unp_pages_butt[i].onclick = pages_butt_push;
	
			//устанавливаем обработчик нажатия для кнопок страниц ОПУБЛИКОВАННЫХ СТАТЕЙ
			p_pages_butt = document.getElementsByClassName('p_pages_butt');
			for(var i = 0; i < p_pages_butt.length; i++)
				p_pages_butt[i].onclick = pages_butt_push;
			
			//устанавливаем обработчик нажатия для кнопок "Удалить"
			del_butt = document.getElementsByClassName('del_article');
			for(var i = 0; i < del_butt.length; i++)
				del_butt[i].onclick = del_butt_push;
		}
		//для edit_articles
		else if(path_array[3] == 'edit'){
			//устанавливаем обработчик нажатия для кнопок страниц НЕОПУБЛИКОВАННЫХ СТАТЕЙ
			unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
			for(var i = 0; i < unp_pages_butt.length; i++)
				unp_pages_butt[i].onclick = pages_butt_push;
	
			//устанавливаем обработчик нажатия для кнопок страниц ОПУБЛИКОВАННЫХ СТАТЕЙ
			p_pages_butt = document.getElementsByClassName('p_pages_butt');
			for(var i = 0; i < p_pages_butt.length; i++)
				p_pages_butt[i].onclick = pages_butt_push;
			
			//устанавливаем обработчик нажатия для кнопок "Изменить"
			edit_butt = document.getElementsByClassName('edit_article');
			for(var i = 0; i < edit_butt.length; i++)
				edit_butt[i].onclick = edit_butt_push;
		}
	}
	
	//если запрашивается main или же по умолчанию
	if((path_array.length == 4 && path_array[3] == 'main') || path_array.length == 3){
		//устанавливаем обработчик нажатия для кнопок страниц НЕОПУБЛИКОВАННЫХ СТАТЕЙ
		unp_pages_butt = document.getElementsByClassName('unp_pages_butt');
		for(var i = 0; i < unp_pages_butt.length; i++)
			unp_pages_butt[i].onclick = pages_butt_push;
	
		//устанавливаем обработчик нажатия для кнопок страниц ОПУБЛИКОВАННЫХ СТАТЕЙ
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

//функция отправляет серверу запрос загрузить статью в add_articles
function article_load_to_add()
{
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('load_to_add', '');
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", articles_url, true);
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
		img_tag = '<img class="dropbox_img" src="/includes/for_articles/articles_imgs/' + files[0].name + '" />';
		data.append('img_or_imgs', 'img');
		drop.style.background = 'white';
	}
	else {
		img_tag = '<img class="drop_imgs" src="/includes/for_articles/articles_imgs/' + files[0].name + '" />';
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
		request.open("POST", articles_url, true);
		request.send(data);
		//alert(window.btoa(reader.result).length);
	}
	//читаем как сырые данные
	reader.readAsBinaryString(files[0]);
}

//функция для add_articles обрабатывает нажатие кнопок СОХРАНИТЬ, ОПУБЛИКОВАТЬ и ЗАГРУЗИТЬ
function butt_in_add_push(e)
{
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	//узнаем какая кнопка нажата
	if(e.target.getAttribute('id') == 'butt_save'){
		var res = confirm("Вы хотите сохранить эту статью в таблицу не опубликованных статей?");
		data.append('butt_in_add', 'save');
		if(article_header.value != '' && edit_text.value != ''){
			data.append('article_header', article_header.value);
			data.append('article_text', edit_text.value);
			data.append('title_img', drop.firstElementChild.getAttribute('src'));
		}
		else {
			alert('Поля "Заголовка" и "Текста" статьи не должны быть пустыми');
			return;
		}
	}
	else if(e.target.getAttribute('id') == 'butt_public'){
		var res = confirm("Вы хотите опубликовать эту статью? ВНИМАНИЕ: СТАТЬЯ СРАЗУ ОКАЖЕТСЯ В ОБЩЕМ ДОСТУПЕ");
		data.append('butt_in_add', 'public');
		if(article_header.value != '' && edit_text.value != ''){
			data.append('article_header', article_header.value);
			data.append('article_text', edit_text.value);
			data.append('title_img', drop.firstElementChild.getAttribute('src'));
		}
		else {
			alert('Поля "Заголовка" и "Текста" статьи не должны быть пустыми');
			return;
		}	
	}
	else {
		var res = confirm("Вы хотите перейти к списку статей для редактирования?");
		if(res)
			window.location.href = '/adminpage/articles/edit';
	}
	
	//пользователь подтвердил выбранное действие 
	if(res && e.target.getAttribute('id') != 'butt_download'){
		//отправляем на сервер текст и заголовок статьи
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", articles_url, true);
		request.send(data);
	}
}


//функция для add_articles копирует текст статьи в область отображения
var header_value = '';
var text_value = '';
function text_copy()
{
	if(header_value != article_header.value || text_value != edit_text.value){
		header_value =  '<h1 id="article_head">' + article_header.value + '</h1>';
		text_value = edit_text.value;
		article_view.innerHTML = header_value + text_value;
	}
}


//обработчик нажатия кнопок страниц ОПУБЛИКОВАННЫХ и НЕОПУБЛИКОВАННЫХ СТАТЕЙ
function pages_butt_push(e)
{
	//отправляем информацию о нужной странице статей на сервер
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('articles_page', Number(e.target.innerHTML));
	//определим нажата кнопка для ОПУБЛИКОВАННЫХ или для НЕОПУБЛИКОВАННЫХ статей 
	if(e.target.getAttribute('class') == 'unp_pages_butt')
		data.append('type_articles', 'unpublished');
	else if(e.target.getAttribute('class') == 'p_pages_but')
		data.append('type_articles', 'published');
	
	//если запрашивается для страницы del, то укажем об этом серверу
	if(path_array[3] == 'del')
		data.append('for_del', '');
	else if(path_array[3] == 'edit')
		data.append('for_edit', '');
	
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", articles_url, true);
	request.send(data);
}


//обработчик нажатия кнопок "Опубликовать" и "Снять с публикацией"
function release_butt_push(e)
{
	//если нажата кнопка у которой в теге прописан обработчик, а не установлен в в этом скрипте
	if(String(e.tagName) == 'undefined')
		e = e.target;
	//отправляем на сервер информацию о статье которую нужно перенести из одной таблицы в другую
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('article_transfer', e.innerHTML);
	
	//определим нужно перенести из unpublished в published или наоборот
	if(e.innerHTML == "Опубликовать")
		var result = confirm("Вы действительно хотите опубликовать эту статью?");
	else {
		var result = confirm("Вы действительно хотите снять эту статью с публикации?");
		var cause_unpublished = prompt("Введите новый статус для статьи", 'Снята модератором');
		data.append('cause', cause_unpublished);
	}
	
	if(result){
		//определим id нужной статьи
		var article_id = e.parentElement.parentElement.firstElementChild.firstElementChild.innerHTML;
		data.append('article_id', article_id);
	
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", articles_url, true);
		request.send(data);
	}
}


//обработчик нажатия кнопок "Удалить" для del_articles
function del_butt_push(e)
{
	//если нажата кнопка у которой в теге прописан обработчик, а не установлен в в этом скрипте
	if(String(e.tagName) == 'undefined')
		e = e.target;
	
	//отправляем на сервер информацию о статье которую нужно удалить
	var result = confirm("Вы действительно хотите удалить эту статью?");
	if(result){
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		//главная переменная, также отображает id удаляемой статьи
		data.append('article_del', e.parentElement.parentElement.firstElementChild.firstElementChild.innerHTML);
		//определим из какой таблицы удалять статью из published или unpublished
		if(e.parentElement.previousElementSibling.firstElementChild.innerHTML == 'Опубликованно')
			data.append('type', 'published');
		else
			data.append('type', 'unpublished');
		
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", articles_url, true);
		request.send(data);
	}
}


//обработчик нажатия кнопок "Изменить" для edit_articles
function edit_butt_push(e){
	//если нажата кнопка у которой в теге прописан обработчик, а не установлен в этом скрипте
	if(String(e.tagName) == 'undefined')
		e = e.target;
	
	//запишем в cookie id статьи, которую надо загрузить в add_articles
	var article_id = e.parentElement.parentElement.firstElementChild.firstElementChild.innerHTML;
	if(e.parentElement.previousElementSibling.firstElementChild.innerHTML == 'Опубликованно')
		var article_from = 'published';
	else
		var article_from = 'unpublished';
		
	document.cookie = 'article_to_add=' + article_id + '; path=/;';
	document.cookie = 'article_from=' + article_from + '; path=/;';
	
	window.location.href = '/adminpage/articles/add';
}


//обработка информации от сервера
function server_answer(e)
{
	var articles_to;
	var data = e.target;
	
	if(data.status == 200){
		if(data.responseText.substr(29, 6) == 'Статьи'){
			articles_to = document.getElementById('unpublished');
			articles_to.innerHTML = data.responseText;
		}
		else if(data.responseText.substr(0, 2) == 'Вы'){
			alert(data.responseText);
			window.location.href = '/adminpage/articles';
		}
		else if(data.responseText.substr(0, 4) == 'Ваша'){
			alert(data.responseText);
			window.location.href = '/adminpage/articles';
		}
		else if(data.responseText == 'img_save'){
			drop.innerHTML = img_tag;
		}
		else if(data.responseText == 'imgs_save'){
			imgs_drop.innerHTML += img_tag;
		}
		else if(data.responseText == 'Статья была удалена'){
			alert(data.responseText);
			window.location.href = '/adminpage/articles/del';
		}
		else if(data.responseText.substr(0, 5) == 'Чтобы'){
			alert(data.responseText);
		}
		else if(data.responseText.substr(0, 5) == 'hilas'){
			//разобьем строку по разделителю hilas
			var arr = data.responseText.split('hilas');
			//вставляем полученные данные
			article_header.value = arr[2];
			edit_text.innerHTML = arr[3];
			drop.innerHTML = '<img class="dropbox_img" src="' + arr[1] + '" />';
		}
		else {
			articles_to = document.getElementById('published');
			articles_to.innerHTML = data.responseText;
			//alert(data.responseText);
		}
	}
	
}
