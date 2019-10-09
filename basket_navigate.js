addEventListener('load', init_bn);

var bn_array;		//массив из 4 этапов basket_navigate
var stage;			//какая стадия order

function init_bn()
{
	bn_array = document.getElementsByClassName('bn');
	var page = document.getElementsByTagName('title');

	switch(page[0].innerHTML)
	{
		case 'Корзина - MBK-Delivery':
			stage = 0;
			break;
		case 'Адрес доставки - MBK-Delivery':
			stage = 1;
			break;
		case 'Оплата - MBK-Delivery':
			stage = 2;
			break;
		case 'Заказ оформлен - MBK-Delivery':
			stage = 3;
			break;
	}
	
	for(var i = 0; i <= stage; i++){
		bn_array[i].style.background = '#515c6b';
	}
	
	if(document.documentElement.clientWidth < 750){
		bn_array[1].innerHTML = '&#10003; Адрес';
		bn_array[3].innerHTML = '&#10003; Готово';
	}
}