ymaps.ready(init_ymap);
    var myMap, myPlacemark;
	var str_input = "";
	var street_input;
	var street_input_norm = false;;

//var obj;
//var myObj;
//полигон города
var myPolygon;
//полигон - по московскому району
//var moscPolygon;


//ФУНКЦИЯ ЗАПИСЫВАЕТ УЛИЦЫ В ТАБЛИЦУ УЛИЦ
/*
function geocode(num){
//	obj = ymaps.geocode("Рязань, 1-е Бутырки улица", {results: 1});
//	obj.then(function(res){
//		myMap.geoObjects.add(res.geoObjects.get(0));
//	});
	var text = "Рязань, " + arr[num];
	obj = ymaps.geocode(text, {results: 1});
	obj.then(function(res){
		var firstGeoObject = res.geoObjects.get(0),
		//координаты
		coords = firstGeoObject.geometry.getCoordinates();

		//myMap.geoObjects.add(firstGeoObject);
		//alert(moscPolygon.geometry.contains(coords));
		//alert(arr.length);
		if(moscPolygon.geometry.contains(coords)){
			var data = new FormData();
			data.append('ajax_pass', '688b76242871b8b69ec2175f65eb8c43');
			data.append('next', arr[num]);
			data.append('nex', num);

			var url = "/mcdonalds/delivery_form_handler";
			var req = new XMLHttpRequest();
			req.open("POST", url, true);
			req.send(data);
		}
	});
}
*/


function init_ymap(){
	//геокодируем
	//geocode();

	street_input = document.getElementById("suggest_street");

	//подключаем поисковые подсказки
	var suggestView = new ymaps.SuggestView('suggest_street', {provider: provider, results: 5});


		//Создаем карту
	myMap = new ymaps.Map("map", {
            center: [54.63, 39.74],
            zoom: 11,
			controls: ['zoomControl', 'fullscreenControl']
        });

	//обрабатываем событие выбора поисковой подсказки
	suggestView.events.add('select', function(event){
		street_input.style.border = "1px solid green";
		str_input = street_input.value;
		street_input_norm = true;
	});

	street_input.oninput = function(){
		if(street_input.value != str_input){
			street_input.style.border = "1px solid red";
			street_input_norm = false;
			err_mes.style.display = "none";
		}
	};


		//Многоугольник московского района
/*		moscPolygon = new ymaps.Polygon([
			[[54.63717, 39.715519], [54.660065, 39.68153],
			 [54.683664, 39.6615], [54.674264, 39.612148],
			 [54.651919, 39.641258], [54.633888, 39.636453],
			 [54.626319, 39.636453], [54.623331, 39.65894],
			 [54.627912, 39.697221]], []], {}, {
			// Задаем опции геообъекта.
			// Цвет заливки.
			fillColor: '#00FF0088',
			opacity: 0.9,
			// Ширина обводки.
			strokeWidth: 3,
			strokeColor: '#008800CC'
		});
*/

		 // Создаем многоугольник, используя вспомогательный класс Polygon.
		myPolygon = new ymaps.Polygon([
			// Указываем координаты вершин многоугольника.
			// Координаты вершин внешнего контура.
			[[54.583819024828344, 39.749239041116546],
			 [54.583811760509356, 39.78122731106164],
			 [54.58031830670132, 39.79717853069176],
			 [54.580713526218545, 39.82102617366116],
			 [54.595162000948335, 39.84434554406952],
			 [54.61022808277366, 39.83861122642971],
			 [54.62284822012943, 39.80137704611857],
			 [54.63211701324001, 39.78603481055351],
			 [54.64098542970245, 39.7689759612187],
			 [54.64000222034526, 39.7369181990727],
			 [54.648389846765944, 39.69958184958508],
			 [54.6814753825741, 39.65306161643118],
			 [54.68386246409271, 39.64928506613862],
			 [54.68048072365977, 39.64379190207595],
			 [54.67869027592293, 39.64756845236859],
			 [54.67550706187833, 39.64001535178203],
			 [54.669736847435715, 39.64799760581007],
			 [54.66197560528215, 39.630058991919334],
			 [54.655058884453986, 39.63915704489752],
			 [54.64903689110616, 39.647740113745],
			 [54.6420577942956, 39.64247176477229],
			 [54.635929097081686, 39.63915094477763],
			 [54.62979947187859, 39.63994999783054],
			 [54.59939178752313, 39.66529052766695],
			 [54.58605804053417, 39.733858455645844],
			 [54.583819024828344, 39.749239041116546]],
			// Координаты вершин внутреннего контура.
			[]
			
		], 
		{
			// Описываем свойства геообъекта.
			// Содержимое балуна.
			//hintContent: "Многоугольник"
		}, 
		{
			// Задаем опции геообъекта.
			// Цвет заливки.
			fillColor: '#00FF0088',
			opacity: 0.1,
			// Ширина обводки.
			strokeWidth: 3,
			strokeColor: '#008800CC'
			
		});
		

		/*for(var i = 0; i < arr.length; i++){
			geocode(i);
		}*/

            
		//Добавляем карте событие - клик
		myPolygon.events.add('click', function(e){
			//Удаляем уже установленную метку
			myMap.geoObjects.remove(myPlacemark);
				
			//Получаем координаты щелчка
			var coords = e.get('coords');
				
			//Добавляем на карту метку в месте щелчка
			myPlacemark = new ymaps.Placemark(coords, {iconContent: 'Доставить сюда'}, {
				preset: 'islands#orangeStretchyIcon',
				draggable: true
			});
			myMap.geoObjects.add(myPlacemark);
				
			//Добавляем событие опускания при перетаскивании метки
			myPlacemark.events.add("dragend", function (e) {
				//Получем координаты места куда перетащили метку
				coords = this.geometry.getCoordinates();
				myReverseGeocoder = ymaps.geocode(coords);
				myReverseGeocoder.then(
				function(res){
					var address = res.geoObjects.get(0).properties.get('name');
					//alert(address);
					addressToForm(address);
				}
			);
			}, myPlacemark);



			//Получаем адрес по координатам метки
			var myReverseGeocoder = ymaps.geocode(coords);
			myReverseGeocoder.then(
				function(res){
					var address = res.geoObjects.get(0).properties.get('name');
					//alert(address);
					addressToForm(address);
				}
			);
		});
		// Добавляем многоугольник на карту.
		myMap.geoObjects.add(myPolygon);
}
	
//Функция для вывода ввода адреса в поля формы
function addressToForm(address){
		address = address.split(', ');
		var homeToCorpus = address[1].split('к');
		var homeToBuild = address[1].split('с');
		var street = document.getElementsByName('street');
		var home = document.getElementsByName('home');
		var corpus = document.getElementsByName('corpus');
		var build = document.getElementsByName('build');
		
		street[0].value = address[0];
		//Если начинается со слова "улица", то удалим это слово
		if(street[0].value.substr(0, 5) == "улица")
			street[0].value = street[0].value.replace("улица ", "");
		home[0].value = homeToCorpus[0];
		if(homeToCorpus[1] != undefined)
			corpus[0].value = homeToCorpus[1];
		else
			corpus[0].value = '';
		if(homeToBuild[1] != undefined){
			home[0].value = homeToBuild[0];
			build[0].value = homeToBuild[1];
		}
		else
			build[0].value = '';
}
