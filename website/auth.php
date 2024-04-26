<?php
require 'connect.php';

function validate(string $token) {
	$req = json_decode(query2json("SELECT id, accountId, UNIX_TIMESTAMP(startTime) as timestamp FROM sessions WHERE id = '{$_COOKIE['token']}'"));
	if (count($req) > 0) {
		// token expires after 90 days
		if (time() > $req[0]->timestamp + 90 * 86400) {
			return false;
		}
		return true;
	}
	else {
		return false;
	}
}

function logout() {

}

if (empty($_COOKIE['token']) || false == validate($_COOKIE['token'])) {
	if (isset($_COOKIE['token'])) {
		// removes the cookie
		unset($_COOKIE['token']);
		setcookie('token', '', -1, '/'); 
	}
	
	header('Location: /login.php?continue=' . $_SERVER['REQUEST_SCHEME'] .'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	die();
}
?>