addEventListener('load', initial);

//функция устанавливающая высоту для окна, потому что окно имеет только absolute-потомков
function setHeightForWindow(){
	change_images = document.getElementsByClassName('change_images');
	window_width = change_images[0].clientWidth;
	window_height = change_images[0].clientHeight;
	var pill = window_height / window_width;
	if(pill > 1)
		pill = window_width / window_height;
	var wind = document.getElementById('images_window');
	var windHeight = getComputedStyle(wind);
	windHeight = windHeight.width;
	windHeight = Number(windHeight.replace("px", ""));
	windHeight *= pill;
	wind.style.height = windHeight + 'px';
	//alert(change_images[0].clientHeight);
}

//устанавливаем таймер для автоматического переключения изображений
var delay = 5000;		//задержка в автоматической смене изображений
var id;					//возвращаемое значение функции setInterval

//коллекция индикаторов
var indicators;

//левая и правая кнопки-стрелки
var left_but;
var right_but;

//коллекция изображений и их количество
var change_images;
var num_of_change_images;

//главные переменные скрипта
var inwin;		//изображение которое сейчас в окне
var left;		//изображение слева от окна
var right;		//изображение справа от окна

//переменные для сенсора
var startx;
var window_width;
var window_hight;



//обработчик нажатия на изображения
function touchStart(e){
	clearInterval(id);

	//e.preventDefault();
	var touchobj = e.changedTouches[0];
	startx = touchobj.clientX;

	for(var i = 0; i <= num_of_change_images; i++)
		change_images[i].style.transition = 'left 0s ease';
}

//обработчик перемещения изображений
function touchMove(e){
	//e.preventDefault();
	var touchobj = e.changedTouches[0];
	var dist = parseInt(touchobj.clientX) - startx;
	change_images[inwin].style.left = dist + 'px';
	change_images[left].style.left = (dist - window_width) + 'px';
	change_images[right].style.left = (dist + window_width) + 'px';
}

//обработчик опускания изображений
function touchEnd(e){
	//e.preventDefault();
	if(parseInt(change_images[inwin].style.left) > (window_width / 2))
		to_left();
	else {
		if(startx < (window_width / 2)){
			left--;
			inwin--;
			right--;
		}
		to_right();
	}

	for(var i = 0; i <= num_of_change_images; i++)
		change_images[i].style.transition = 'left .8s ease';
}




function initial(){
	//получаем коллекцию изображений и их количество
	change_images = document.getElementsByClassName('change_images');
	//alert(change_images[0]);
	num_of_change_images = change_images.length - 1;	//Число изображений в слайд-шоу


	//если мобильная версия сайта, то сменим изображения
	if(document.documentElement.clientWidth < 767){
		for(var i = 1; i <= num_of_change_images + 1; i++){
			var innerH = change_images[i - 1].getAttribute('src');
			//для mcdonalds
			innerH = innerH.replace("mc" + i + ".jpg", "mc" + i + "_mob.jpg");
			innerH = innerH.replace("mc" + i + ".webp", "mc" + i + "_mob.webp");
			//для burger king
			change_images[i - 1].setAttribute('src', innerH);
			//alert(innerH);
		}

		change_image = document.getElementsByClassName('change_images');
		//alert(change_image[0].getAttribute('src'));
		//alert(change_image[0].clientHeight);
		setHeightForWindow();
	}


	//ширина и высота изображений
	window_width = change_images[0].clientWidth;
	window_height = change_images[0].clientHeight;

	//получаем коллекцию индикаторов
	indicators = document.getElementsByClassName('toggle_circles');

	//Установим изображения которые слева, в и справа от окна
	left = num_of_change_images;
	inwin = 0;
	right = 1;

	change_images[inwin].style.zIndex = '5';
	change_images[left].style.zIndex = '3';
	change_images[right].style.zIndex = '1';

	//установим обработчики для всех изображений
	for(var i = 0; i <= num_of_change_images; i++){
		change_images[i].addEventListener('touchstart', touchStart);
		change_images[i].addEventListener('touchmove', touchMove);
		change_images[i].addEventListener('touchend', touchEnd);
	}

	//устанавливаем таймер для автоматического переключения изображений
	id = setInterval(to_right, delay);
	//setTimeout(der, 1000);
	change_images[0].addEventListener('load', setHeightForWindow);
	//устанавливаем высоту images_window
	setHeightForWindow();
	addEventListener('resize', setHeightForWindow);

	//включаем слушатель для того чтобы останавливать смену изображений, когда окно не показано
	document.addEventListener('visibilitychange', visChange);

	//получаем кнопки-стрелки
	getArrows();

	//подсветим нужный индикатор
	indicate();

	shift();
}

//функция отслеживает активно окно или нет
function visChange(){
	if(document.visibilityState == "visible"){
		id = setInterval(to_right, delay);
	}
	else{
		clearInterval(id);
	}
}

//получаем кнопки и устанавливаем для них обработчики
function getArrows(){
	left_but = document.getElementById('arrow_left');
	right_but = document.getElementById('arrow_right');
	left_but.onclick = to_left;
	right_but.onclick = to_right;
}

//функция сдвигающая изображения вправо
function to_right(){
	//отключаем кнопки во время анимации
	left_but.onclick = der;
	right_but.onclick = der;

	left++;
	inwin++;
	right++;
	if(left > num_of_change_images)
		left = 0;
	if(inwin > num_of_change_images)
		inwin = 0;
	if(right > num_of_change_images)
		right = 0;

	//подсветим нужный индикатор
	indicate();

	change_images[inwin].style.zIndex = '5';
	change_images[right].style.zIndex = '1';
	change_images[left].style.zIndex = '3';

	shift();

	//делаем невозможным одновременное перемещение из-за нажатия кнопки и из-за таймера
	clearInterval(id);
	id = setInterval(to_right, delay);
}

//сервисная функция используется для отключения кнопок вправо/влево во время внимации
function der(){
	setHeightForWindow();
}

//функция сдвигающая изображения влево
function to_left(){
	//отключаем кнопки во время анимации
	left_but.onclick = der;
	right_but.onclick = der;

	left--;
	inwin--;
	right--;
	if(left < 0)
		left = num_of_change_images;
	if(inwin < 0)
		inwin = num_of_change_images;
	if(right < 0)
		right = num_of_change_images;

	//подсветим нужный индикатор
	indicate();

	change_images[inwin].style.zIndex = '5';
	change_images[right].style.zIndex = '3';
	change_images[left].style.zIndex = '1';

	shift();

	//делаем невозможным одновременное перемещение из-за нажатия кнопки и из-за таймера
	clearInterval(id);
	id = setInterval(to_right, delay);
}

//функция меняющая классы для изображений
function shift(){
	change_images[inwin].style.left = '0px';
	change_images[right].style.left = window_width + 'px';
	change_images[left].style.left = -window_width + 'px';

	//Делаем кнопки вправо/влево снова активными после завершения анимации
	change_images[inwin].addEventListener('transitionend', butActive);
}

//функция делающая кнопки вправо/влево снова активными
function butActive()
{
	left_but.onclick = to_left;
	right_but.onclick = to_right;
}

function indicate(){
	indicators[left].style.background = "black";
	indicators[right].style.background = "black";
	indicators[inwin].style.background = "white";
}
