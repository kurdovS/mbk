addEventListener('load', email_init);

var email_push_button;
var email_input;
var email_block;

function email_init()
{
	email_block = document.getElementById('email_block');
	
	email_push_button = document.getElementById("email_button");
	email_push_button.onclick = email_button_push;
	email_input = document.getElementById("email_input");
	email_input.onfocus = function email_input_focus(e){
		e.target.style.border = "2px solid lightgray";
	}
}

//обработчик кнопки "подписаться на рассылку"
function email_button_push(e){
	var regexp = /^[0-9A-Za-z-]{1,}@[0-9A-Za-z-]{1,}\.[0-9A-Za-z]{2,5}$/;	//email
	var email = email_input.value;
	
	//e-mail введен не верно
	if(!regexp.test(email)){
		email_input.value = "";
		email_input.placeholder = "Введите электронную почту в формате: test@domain.ru";
		email_input.style.border = "2px solid red";
	}
	//e-mail введен верно
	else {
		var data = new FormData();
		//установим переменную идентифицирующую ajax-запрос с сайта
		data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
		data.append('email_to_db', email);
	
		var url = "/" + brand + "/email_include_handler";
		var req = new XMLHttpRequest();
		req.open("POST", url, true);
	
		req.send(data);
		
		setTimeout(function(){
			email_block.classList.remove("email_block_zero");
			email_block.classList.add("email_block_first");
			email_block.innerHTML = '<div id="email_wrap"><h2>Спасибо за подписку!</h2></div>';
		}, 500);
		
		setTimeout(function(){ 
			email_block.classList.remove('email_block_first');
			email_block.classList.add('email_block_second');
		}, 1500);
	}
}
