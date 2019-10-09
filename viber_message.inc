<?php
//отправляет сообщение мне в viber
function viber_message($text){
	$access_token = "49c20c945d27d21c-d6031053d74e7f9b-2595497b68bcfa83";
	$my_viber_id = "uagpAn3WGhwfDqBdfqCUZg==";
	
	$data['auth_token'] = $access_token;
	$data['receiver'] = $my_viber_id;
	$data['type'] = 'text';
	$data['text'] = $text;

	$url = "https://chatapi.viber.com/pa/send_message";
	$jsonData = json_encode($data);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch);
	curl_close($ch);
}
?>
