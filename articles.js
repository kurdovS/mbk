addEventListener('load', articles_init);

//кнопки переключения вида
var view_rows;
var view_icons;

function articles_init()
{
	//задаем обработчик для кнопок переключения вида
	view_rows = document.getElementById('view_rows');
	view_icons = document.getElementById('view_icons');
	view_rows.onclick = view_handler;
	view_icons.onclick = view_handler;
}

//обработчик нажатия кнопок для смены вида
function view_handler(e)
{
	//запишим в куки информацию как мы хотим отображать список статей
	if(e.target.parentElement.getAttribute('id') == 'view_icons')
		document.cookie = "view=icons; path=/";
	else
		document.cookie = "view=rows; path=/";
	window.location.href = document.location.pathname
}