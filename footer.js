addEventListener('load', footer_initial);

function footer_initial(e)
{
	var kak_zak = document.getElementById('kak_zak');

	//если мобильная версия сайта, то изменим ссылку на страницу "Как заказать"
	if(document.documentElement.clientWidth > 767){
		var mobb_brand = kak_zak.innerHTML.substr(10, 3);

		if(mobb_brand == 'mcd')
			mobb_brand = 'mcdonalds';
		else if(mobb_brand == 'bur')
			mobb_brand = 'burgerking';
		else if(mobb_brand == 'kfc')
			mobb_brand = 'kfc';

		kak_zak.innerHTML = '<a href="/' + mobb_brand + '/info/kak-zakazat">Как заказать</a>';
	}
}
