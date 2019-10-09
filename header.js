var mobile_burger;
var menu_for_mobile;
var krest;
var right_offset;
var wrap;
var is_menu_in;

var cart_button;
var small_cart;
var cart_button_img;

addEventListener('load', header_initial);

function header_initial(){
	//значок "скоро" для kfc
	var kfc_li = document.getElementById("kfc_li");
	var kfc_cs = document.getElementById("kfc_coming_soon");
	kfc_li.onmousemove = function kfc_over(e){
		kfc_cs.style.display = "block";
	}
	kfc_li.onmouseout = function kfc_out(e){
		kfc_cs.style.display = "none";
	}
	
	is_menu_in = false;		//изначально меню закрыто
	
	mobile_burger = document.getElementById("mobile_burger");
	menu_for_mobile = document.getElementById("menu_for_mobile");
	menu_for_mobile.style.position = "fixed";
	menu_for_mobile.addEventListener('transitionend', animation_end);
	krest = document.getElementById("close_mobile_head");
	
	mobile_burger.onclick = my_burger_push;
	krest.onclick = krest_push;
	
	cart_button = document.getElementById("cart_button");
	small_cart = document.getElementById("small_cart");
	cart_button_img = document.getElementById("cart_button_img");
	cart_button_img.onclick = cart_button_push;
	
	
	//для меню в мобильной версии
	wrap = document.getElementById("wrap");
	
	if(document.documentElement.clientWidth < 750){
		//вызовем функцию category_push если на мобильной версии сайта
		var path = String(document.location);
		//выясняем запрашиваемую категорию
		var slash_pos = path.indexOf('#');
		//alert(slash_pos);
		if(slash_pos != -1) {
			var category_str = path.substr((path.indexOf('#') + 1))
			category_str += '_category';
			var mobile_menu_li = document.getElementById(category_str);
			category_push(mobile_menu_li);
		}
		
	}
}


function category_push(e){
	var category = e.getAttribute("id");
	category = category.substr(0, category.indexOf('_category'));
	var category_div = document.getElementById(category + "_div");
	if(category_div == null)
		return;
	//для мобильной версии сайта уберем все категории кроме текущей
	if(document.documentElement.clientWidth < 750){
		var all_category_divs = document.getElementsByClassName("category_div");
		for(var i = 0; i < all_category_divs.length; i++)
			all_category_divs[i].style.display = "none";
	}
	category_div.style.display = "block";
	
	if(is_menu_in)
		krest_push();
}



//функция которая вызывается, когда нажимается кнопка меню
function my_burger_push() {
	right_offset = pageYOffset;
	
	menu_for_mobile.classList.toggle('in');
	menu_for_mobile.classList.toggle('out');	
}

//функция которая вызывается, когда меню закрывается
function krest_push(){
	menu_for_mobile.classList.toggle('in');
	menu_for_mobile.classList.toggle('out');
	
	//до того как закрылось меню зафиксируем положение wrap
	wrap.style.position = "static";
	menu_for_mobile.style.position = "fixed";
	window.scrollTo(0, right_offset);
}

//обработчик конца анимации меню
function animation_end(e){
	if(is_menu_in){
		is_menu_in = false;
	} else {
		//после того как меню уже открыто зафиксируем положение wrap
		wrap.style.position = "fixed";
		menu_for_mobile.style.position = "absolute";
		window.scrollTo(0, 0);
		is_menu_in = true;
	}	
}

function cart_button_push(){
	var className = document.location.pathname.split('/');
	var br = className[1];
	if(br == '')
		br = 'mcdonalds';
	className = className[3];
	if(className != 'payment')
		small_cart.classList.toggle('vis');
	//если планшет, то редирект в корзину
	if(document.documentElement.clientWidth >= 768 && document.documentElement.clientWidth <= 1350)
		window.location.href = '/' + br + '/order/basket';
}