addEventListener('load', init_products);


var product_with_volume;
function init_products()
{
		product_with_volume = document.getElementsByClassName('with_volume');
		var num_products_with_volume = product_with_volume.length;
		
		
		//для каждого продукта с различными объемами
		for(var i = 0; i < num_products_with_volume; i++){
			//включить первый элемент
			product_with_volume[i].children[0].style.display = "block";
			//включить кнопку для первого элемента
			product_with_volume[i].lastElementChild.children[0].classList.add('on');
			
			//количество объемов данного продукта
			var num_products_in_block = product_with_volume[i].lastElementChild.children.length;
			
			//задаем функционал для каждой кнопки
			for(var j = 0; j < num_products_in_block; j++){	//массив по кнопкам
				product_with_volume[i].lastElementChild.children[j].onclick = push_button;
				product_with_volume[i].lastElementChild.children[j].onmouseover = over_button;
				product_with_volume[i].lastElementChild.children[j].onmouseout = over_button;
			}
		}
}

//обработчик нажатия на кнопку с объемом
function push_button()
{
	var vol_buttons = this.parentElement;				//элемент vol_buttons
	var vol_buttons_num = vol_buttons.children.length;	//число кнопок
	
	var product_block_with_volume = vol_buttons.parentElement;	//product_block_with_volume
	
	var what_button_was_pushed;
	//цикл по кнопкам
	for(var i = 0; i < vol_buttons_num; i++){
		//выключаем не нужные кнопки
		vol_buttons.children[i].classList.remove('on');
		//выключаем блоки не нужных объемов
		product_block_with_volume.children[i].style.display = "none";
		
		if(vol_buttons.children[i] == this)
			what_button_was_pushed = i;
	}
	
	//включаем нажатую кнопку
	this.classList.add('on');
	//включаем блок с нужным объемом
	product_block_with_volume.children[what_button_was_pushed].style.display = "block";
}

//обработчик наведения на кнопку
function over_button()
{
	this.classList.toggle('over');
}