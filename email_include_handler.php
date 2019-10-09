<?php
	if(isset($_POST['email_to_db'])){
		require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/for_all/db_connection.inc");
		$db = connectionDB();
		
		$query = 'INSERT INTO `subscriptions_emails` (email) VALUES ("' . $_POST['email_to_db'] . '")';
		$res = mysqli_query($db, $query);
		mysqli_close($db);
		unset($_POST['email_to_db']);
	}
?>