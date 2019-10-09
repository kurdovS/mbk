//в данном файле описана функция которая обрабатывает процесс ввода номера телефона в поле input
function phone_input_func()
{
	var phone_input = document.getElementsByName("phone")[0];
	
	//введем +7 ( в поле если оно в фокусе
	phone_input.onfocus = function phone_focus(e){
		if(e.target.value.length < 5)
			e.target.value = "+7 (";
	}
	
	//уберем +7 ( когда поле не в фокусе и в нем только +7 (
	phone_input.onblur = function phone_blur(e){
		if(e.target.value.length < 5)
			e.target.value = "";
	}
	
	//обработка отпускания клавиш когда инпут телефона в фокусе
	phone_input.onkeyup = function phone_change(e){
		//проверяем что нажата цифра
		var regexpChar = /[0-9]{1}/;
		var lastChar = phone_input.value.substr(phone_input.value.length - 1, 1);
		if(!regexpChar.test(lastChar))
			phone_input.value = phone_input.value.substr(0, phone_input.value.length - 1);
		
		//если нажата не кнопка "стереть"
		if(e.keyCode != 8 && e.keyCode != 229){
			var phone_value = e.target.value;
			
			if(phone_value.length < 5)
				phone_input.value = "+7 (";
			else if(phone_value.length == 7){
				phone_input.value = phone_input.value + ')';
			}
			else if(phone_value.length == 9){
				var str = phone_input.value.substr(0, 8) + ' ' + phone_input.value[8];
				phone_input.value = str;
			}
			else if(phone_value.length == 12 || phone_value.length == 15)
				phone_input.value = phone_input.value + "-";
			else if(phone_value.length > 18)
				phone_input.value = phone_input.value.substr(0, 18);
		}
		else if(e.target.value.length < 5)
			phone_input.value = "+7 (";
	}
}