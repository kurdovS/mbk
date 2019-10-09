<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . 'header.inc');
	$head = head('mcdonalds');

//подключаем к коду css для header
	$html_code = '
<!DOCTYPE html>
<html lang="ru">
<head>
	<style>
	* {
		margin: 0;
		padding: 0;
		font-family: Helvetica;
		font-size: 14px;
	}
	</style>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta charset="utf8" />
	<link rel="stylesheet" href="header.css" />
	<script src="header.js"></script>
</head>
<body>';

$html_code .=
$head
 . '
</body>
</html>';

echo $html_code;	
?>