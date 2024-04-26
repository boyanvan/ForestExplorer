<?php
require '../connect.php';

header('Content-Type: application/json; charset=utf-8');

if ( !empty($_POST['name'])
	&& (!empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
	&& (!empty($_POST['phone']) && preg_match('/^(\+|0)([0-9]){6,14}$/', $_POST['phone']))
	&& !empty($_POST['message']) ) {
	$sql = "INSERT INTO contactforminfo (name, email, phone" . (!empty($_POST['subject']) ? ', subject' : '') . ", message, date) VALUES (:name, :email, :phone" . (!empty($_POST['subject']) ? ', :subject' : '') . ", :message, now())";
	$params = [ ':name' => $_POST['name'],
				':email' => $_POST['email'],
				':phone' => $_POST['phone'],
				':message' => $_POST['message'] ];
	if (!empty($_POST['subject'])) {
		$params[':subject'] = $_POST['subject'];
	}

	$res = query_p($sql, $params);

	echo json_encode(['msg' => 'Sent successfully.']);;
}
else {
	http_response_code(400);
}

?>