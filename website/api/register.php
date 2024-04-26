<?php
header('Content-Type: application/json; charset=utf-8');

function validateSHA384(string $hash) {
	return strlen($hash) == 96 && ctype_xdigit($hash);
}

if (!empty($_POST['username']) && !empty($_POST['password'])) {
	require '../connect.php';

	$user = $_POST['username'];
	$pass = $_POST['password'];
	$res = query_p("SELECT username FROM accounts WHERE username = :user",
		[':user' => $user]);
	$userExists = count( $res );
	if ($userExists) {
		http_response_code(400);
		echo json_encode(['msg' => 'Username already taken.']);
	}
	else {
		if (!validateSHA384($pass)) {
			http_response_code(400);
			echo json_encode(['msg' => 'Invalid password encoding.']);
		}
		else {
			// Gotta test this
			query_p("INSERT INTO accounts (username, password) VALUES (:user, :pass)",
				[':user' => $user, ':pass' => $pass]);
			echo json_encode(['msg' => 'Success.']);
		}
	}
}
?>