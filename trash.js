addEventListener('load', trash_init);

var red_del_butts;
var clear_red_orders_butt;
var trash_url;

function trash_init()
{
	trash_url = '/adminpage/trash_form_handler';
	
	red_del_butts = document.getElementsByClassName('red_del_butt');
	for(var i = 0; i < red_del_butts.length; i++)
		red_del_butts[i].onclick = red_del_butt_push;
	
	clear_red_orders_butt = document.getElementById('clear_red_orders');
	clear_red_orders_butt.onclick = clear_red_orders_push;
}

//удаление единичного заказа
function red_del_butt_push(e)
{
	var result = confirm('Вы уверены что вы хотите удалить этот заказ?');
	if(result){
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		
		//передадим серверу № заказа который нужно удалить
		data.append('order_del', e.target.parentElement.parentElement.firstElementChild.innerHTML);
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST",trash_url, true);
		request.send(data);
	}
}

//удаление всех красных заказов
function clear_red_orders_push(e){
	var result = confirm('Вы уверены что вы хотите отчистить таблицу от всех устаревших заказов?');
	if(result){
		//определим дату заказа на границы красных и зеленых заказов (последнего красного заказа)
		var tr_elements = document.getElementsByClassName('red_tr');
	
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
	
		//передадим серверу № заказа который нужно удалить
		data.append('orders_clear', tr_elements[tr_elements.length - 1].lastElementChild.previousElementSibling.previousElementSibling.innerHTML);
		var request = new XMLHttpRequest();
		request.addEventListener('load', server_answer);
		request.open("POST",trash_url, true);
		request.send(data);
	}
}

function server_answer(e){
	var data = e.target;

	if(data.status == 200){
		alert(data.responseText);
		window.location.href = '/adminpage/trash';
	}
}