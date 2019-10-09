addEventListener('load', catalogs_init);

var rest_checks;
var catalogs_url;

var mode;

//для add

function catalogs_init()
{
	//обработчик JS запросов в catalogs
	catalogs_url = '/adminpage/catalogs_form_handler';
	
	rest_checks = document.getElementsByClassName('rest_check');
	for(var i = 0; i < rest_checks.length; i++){
		rest_checks[i].onclick = rest_check_button;
	}
	
	//установим запрашиваемое действие
	if(window.location.pathname.split("/")[3] == 'add')
		mode = 'add';
	else if(window.location.pathname.split("/")[3] == 'del')
		mode = 'del';
	else if(window.location.pathname.split("/")[3] == 'edit')
		mode = 'edit';
	else
		mode = 'main';
}

//запрос к серверу выдать таблицу продуктов в меню
function rest_check_button(e)
{
	//отправляем информацию о нужном ресторане
	var data = new FormData();
	//установим переменную идентифицирующую ajax-запрос с сайта
	data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	data.append('restoraunts', '');
	data.append('mode', mode);
	data.append('rest_num', rest_checks.length);
	//массив неизвестной заранее длины
	var r_checks = [];			//индекс массива == (id_ресторана - 1)
	for(var i = 0; i < rest_checks.length; i++)
		r_checks[i] = rest_checks[i].checked;
	//превратим массив r_checks в json-строку для передачи серверу
	data.append('r_checks', JSON.stringify(r_checks));
	
	var request = new XMLHttpRequest();
	request.addEventListener('load', server_answer);
	request.open("POST", catalogs_url, true);
	request.send(data);
}


//add
//обработчик нажатия кнопок добавить продукт | выводит строку для ввода полей нового продукта
function add_product(e)
{
	//изменим высоту строки для ввода полей добавляемого продукта путем смены класса
	var tr_to_visible = e.parentElement.parentElement.nextElementSibling;
	tr_to_visible.classList.remove('hidden_prod_tr_hide');
	tr_to_visible.classList.add('hidden_prod_tr');
	
	//изменим действие кнопки добавить
	e.onclick = insert_product;
}

//обработчик нажатия кнопок добавить продукт | отправляет запрос серверу о добавлении продукта
function insert_product(e)
{
	//выясним в меню какого ресторана мы вставляем товара
	var rest_name = e.target.parentElement.parentElement.parentElement.parentElement.previousElementSibling.innerHTML;

	//01_узнаем номер товара после которого будем вставлять новый продукт
	var product_id = e.target.parentElement.parentElement.firstElementChild.innerHTML;
	if(product_id == '')
		product_id = '0';

	//02_считываем введенные в поля значения
	var elem = e.target.parentElement.parentElement.nextElementSibling.firstElementChild.nextElementSibling;
	var name = elem.innerHTML;
	elem = elem.nextElementSibling;
	var category = elem.innerHTML;
	elem = elem.nextElementSibling;
	var category_rus = elem.innerHTML;
	elem = elem.nextElementSibling;
	var price = elem.innerHTML;
	elem = elem.nextElementSibling;
	var img_path = elem.innerHTML;
	elem = elem.nextElementSibling;
	var description = elem.innerHTML;
	elem = elem.nextElementSibling;
	var ingredients = elem.innerHTML;
	elem = elem.nextElementSibling;
	var nutrients = elem.innerHTML;
	elem = elem.nextElementSibling;
	var volume = elem.innerHTML;
	elem = elem.nextElementSibling;
	var vol = elem.innerHTML;
	
	//03_формируем и отправляем серверу ajax-запрос
	//если поля Название, категория, категория по русски, цена, путь к изображению и объем не пусты
	if(name != '' && category != '' && category_rus != '' && price != '' && img_path != '' && volume != ''){
		//отправляем информацию об изменяемом пользователе на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		//в меню какого ресторана нужно вставить позицию
		data.append('rest_name', rest_name);
		data.append('add_product', product_id);
		data.append('add_name', name);
		data.append('add_category', category);
		data.append('add_category_rus', category_rus);
		data.append('add_price', price);
		data.append('add_img_path', img_path);
		data.append('add_description', description);
		data.append('add_ingredients', ingredients);
		data.append('add_nutrients', nutrients);
		data.append('add_volume', volume);
		data.append('add_vol', vol);
		
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", catalogs_url, true);
		request.send(data);
	}
	else
		alert('Поля для "Названия", "Категории", "Категории по русски", "Цены", "Пути" и "Объема" не должны быть пустыми');
}


//del
//обработчик нажатия кнопок "Удалить"
function del_product(e)
{
	var result = confirm("Вы действительно хотите удалить эту позицию из меню?");
	if(result){
		//выясним в меню какого ресторана мы вставляем товара
		var rest_name = e.parentElement.parentElement.parentElement.parentElement.previousElementSibling.innerHTML;

		//получим id удаляемой позиции
		var product_to_del = e.parentElement.parentElement.firstElementChild.innerHTML;

		//отправляем информацию об удаляемой позиции
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('rest_name', rest_name);
		data.append('del_product', product_to_del);

		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", catalogs_url, true);
		request.send(data);
	}
}

//edit
//обработчик нажатия кнопок изменить продукт | выводит строку для ввода полей продукта
function edit_product(e)
{
	//изменим высоту строки для ввода полей добавляемого продукта путем смены класса
	var tr_to_visible = e.parentElement.parentElement.nextElementSibling;
	tr_to_visible.classList.remove('hidden_prod_tr_hide');
	tr_to_visible.classList.add('hidden_prod_tr');

	//скопируем старые значения
	var copy_from_tr = tr_to_visible.previousElementSibling;
	var current_from = copy_from_tr.firstElementChild;
	var current_to = tr_to_visible.firstElementChild;
	for(var i = 0; i < 9; i++){
		current_from = current_from.nextElementSibling;
		current_to = current_to.nextElementSibling;
		current_to.innerHTML = current_from.innerHTML;
	}
	
	//изменим действие кнопки добавить
	e.onclick = change_product;
}

//обработчик нажатия изменения продукта
function change_product(e)
{
	//выясним в меню какого ресторана мы вставляем товара
	var rest_name = e.target.parentElement.parentElement.parentElement.parentElement.previousElementSibling.innerHTML;
	
	//01_получаем значения полей
	//id изменяемой позиции
	var product_id = e.target.parentElement.parentElement.firstElementChild.innerHTML;
	//текущее поле
	var current_row = e.target.parentElement.parentElement.nextElementSibling.firstElementChild.nextElementSibling;
	//название продукта
	var name = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//категория продукта
	var category = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//категория по русски продукта
	var category_rus = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//цена продукта
	var price = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//путь к изображению продукта
	var img_path = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//описание продукта
	var description = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//ингредиенты продукта
	var ingredients = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//нутриенты продукта
	var nutrients = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//есть ли объем у продукта
	var volume = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	//объект продукта
	var vol = current_row.innerHTML;
	current_row = current_row.nextElementSibling;
	
	//02_формируем и отправляем серверу ajax-запрос
	//если поля Название, категория, категория по русски, цена, путь к изображению и объем не пусты
	if(name != '' && category != '' && category_rus != '' && price != '' && img_path != '' && volume != ''){
		//отправляем информацию об изменяемом пользователе на сервер
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('rest_name', rest_name);
		data.append('edit_product', product_id);
		data.append('edit_name', name);
		data.append('edit_category', category);
		data.append('edit_category_rus', category_rus);
		data.append('edit_price', price);
		data.append('edit_img_path', img_path);
		data.append('edit_description', description);
		data.append('edit_ingredients', ingredients);
		data.append('edit_nutrients', nutrients);
		data.append('edit_volume', volume);
		data.append('edit_vol', vol);
		
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST", catalogs_url, true);
		request.send(data);
	}
	else
		alert('Поля для "Названия", "Категории", "Категории по русски", "Цены", "Пути" и "Объема" не должны быть пустыми');
}


//обработка информации от сервера
function server_answer(e)
{
	var data = e.target;
	if(data.status == 200){
		//alert(data.responseText);
		var choosed_restoraunt = document.getElementById('choosed_restoraunt');
		choosed_restoraunt.innerHTML = data.responseText;
		
	}
	
}
