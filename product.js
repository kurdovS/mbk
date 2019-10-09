addEventListener('load', class_product_init);

var volumes_in_product;
var volume_buttons;

function class_product_init()
{
	volumes_in_product = document.getElementsByClassName("volume_in_product");
	volume_buttons = document.getElementsByClassName("vol_buttons");
	
	//выключаем ненужные не выбранные объемы
	for(var i = 1; i < volumes_in_product.length; i++){
		volumes_in_product[i].style.display = "none";
	}
	
	//установим обработчик кнопок объема и подсветим правильно кнопки
	for(var i = 0; i < volume_buttons.length; i++){
		volume_buttons[i].onclick = volume_change;
		
		//наведение на кнопку
		volume_buttons[i].onmouseover = function vbomo(e){
			e.target.classList.toggle("over");
		}
		//уведение от кнопки
		volume_buttons[i].onmouseout = function vbout(e){
			e.target.classList.toggle("over");
		}
		
		
		//сделаем на каждой страницы подсвеченной нужную кнопку
		if(i % (volumes_in_product.length + 1) == 0)
			volume_buttons[i].classList.add("on");
	}
}


//обработчик нажатия кнопки
function volume_change(e)
{
	var num_of_button = Number(this.getAttribute("id").substr(11, 1));

	for(var i = 0; i < volumes_in_product.length; i++){
		volumes_in_product[i].style.display = "none";
	}
	volumes_in_product[num_of_button - 1].style.display = "block";
	
}