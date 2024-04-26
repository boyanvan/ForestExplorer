<?php
if (!empty($_COOKIE['token'])) {
	require '../connect.php';
	query_p("DELETE FROM sessions WHERE id = :token",
		[':token' => $_COOKIE['token']]);
	unset($_COOKIE['token']);
	setcookie('token', '', -1, '/');
	header('Location: /login.php');
}
?>