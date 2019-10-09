<?php

$access_token = "49c20c945d27d21c-d6031053d74e7f9b-2595497b68bcfa83";

$request = file_get_contents("php://input");
$input = json_decode($request, true);

if($input['event'] == 'webhook'){
	$webhook_response['status'] = 0;
	$webhook_response['status_message'] = "ok";
	$webhook_response['event_types'] = "delivered";
	echo json_encode($webhook_response);
	die;
}
else if($input['event']=="message"){
	$text_received = $input['message']['text'];
	$sender_id = $input['sender']['id'];
	$sender_name = $input['sender']['name'];

	//возможные вопросы боту
	if($text_received == "Как заказать" || $text_received == "Как сделать заказ")
		$message_to_reply = "Перейдите на страницу https://mbk-delivery.ru/mcdonalds/info/kak-sdelat-zakaz";
	else if($text_received == "П")
		$message_to_reply = "Привет";
	else if($text_received == "Номер")
		$message_to_reply = $sender_id;

	$data['auth_token'] = $access_token;
	$data['receiver'] = $sender_id;
	$data['type'] = 'text';
	$data['text'] = $message_to_reply;

	sendMessage($data);
}



function sendMessage($data){
	$url = "https://chatapi.viber.com/pa/send_message";
	$jsonData = json_encode($data);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch);
	return $result;
}
