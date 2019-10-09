<?php
	//сервисная функция - устанавливает соединение с БД
	function connectionDB(){
		$db = mysqli_connect("localhost", "akav", "21031990Aa", "mbk_delivery");
		mysqli_query($db, 'SET NAMES utf8');	
		return $db;
	}
?>