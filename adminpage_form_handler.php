<?php
	//пришел запрос на выход из системы
	if(isset($_POST['exit'])){
		unset($_POST['exit']);
		
		Header('WWW-Authenticate: Basic realm="Admin Page"');
		Header("HTTP/1.0 401 Unauthorized");
	}
?>