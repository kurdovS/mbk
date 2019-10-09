<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . 'products_catalog.inc');
	$products_catalog = products_catalog('mcdonalds');

//подключаем к коду css для products_catalog
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
	<link rel="stylesheet" href="product_block.css" />
	<script src="products_with_volume.js"></script>
</head>
<body>';

$html_code .=
$products_catalog
 . '
</body>
</html>';

echo $html_code;
?>