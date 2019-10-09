<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . 'change_images.inc');
	$change_images = change_images('brand');

//подключаем к коду css и js для change_images
	$html_code = '
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf8" />
	<link rel="stylesheet" href="change_images.css" />
	<script src="change_images.js"></script>
</head>
<body>';

$html_code .=
$change_images . '
</body>
</html>';

echo $html_code;	
?>