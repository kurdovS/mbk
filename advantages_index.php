<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . 'advantages.inc');
	$advantages = advantages('mcdonalds');

//подключаем к коду css для advantages
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
	<link rel="stylesheet" href="advantages.css" />
</head>
<body>';

$html_code .=
$advantages
 . '
</body>
</html>';

echo $html_code;	
?>