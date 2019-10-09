addEventListener('load', maintance_initial);

var all_sec;
var timerId;

function maintance_initial()
{
	all_sec = document.getElementById('time').innerHTML;
	var arr = all_sec.split('.');
	for(var i = 0; i < (arr.length - 1); i++)
		arr[i] = parseInt(arr[i]);
	
	if(arr.length == 5){
		arr[0] *= 86400;
		arr[1] *= 3600;
		arr[2] *= 60;
	}
	else if(arr.length == 4){
		arr[0] *= 3600;
		arr[1] *= 60;
	}
	else if(arr.length == 3)
		arr[0] *= 60;
	
	all_sec = 0;
	for(var i = 0; i < (arr.length - 1); i++)
		all_sec += arr[i];
	
	//наконец получили общее число оставшихся секунд
	timerId = setInterval(time_func, 1000);
}

function time_func()
{
	var time_str = '';
	var secs = all_sec--;
	var days = parseInt(secs / 86400);
	secs -= (days * 86400);
	var hours = parseInt(secs/ 3600);
	secs -= (hours * 3600);
	var minutes = parseInt(secs / 60);
	secs -= (minutes * 60);
	var seconds = secs;
	
	if(days > 0)
		time_str += days + ' дн. ';
	if(hours > 0)
		time_str += hours + ' ч. ';
	if(minutes > 0)
		time_str += minutes + ' мин. ';
	if(seconds > 0)
		time_str += seconds + ' сек. ';
	
	//alert(time_str);
	document.getElementById('time').innerHTML = time_str;
	
	if(all_sec <= 0)
		clearInterval(timerId);
}