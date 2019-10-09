<?php

function email($brand)
{
	//подключение к БД
	require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
	$db = connectionDB();
	
	$email = '
<div id="email_block" class="anim_eb email_block_zero">
	<div id="email_wrap">
		<h2>Узнавай первым о наших акциях</h2>
	</div>
	<div id="bottom_email">
		<span>подпишитесь на еженедельную e-mail рассылку!</span>
		<input type="email" id="email_input" />
		<div id="email_button">ПОДПИСАТЬСЯ</div>
		<div class="empty"></div>
	</div>
</div>';

	return $email;
}
?>