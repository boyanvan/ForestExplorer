<?php

/* MySQL

*/
$server = "localhost";
$username = "ANybOdY";
$password = "";
$dbname = "cartography";

try {
	$conn = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
	// set the PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}

function query(string $query) {
	global $conn;
	$req = $conn->prepare($query);
	$req->execute();
	$req->setFetchMode(PDO::FETCH_ASSOC);
	$result = $req->fetchAll();

	return $result;
}

function query_p(string $query, array $params) {
	global $conn;
	$req = $conn->prepare($query);
	foreach ($params as $key => $value) {
		$req->bindValue($key, $value);
	}
	$req->execute();
	$req->setFetchMode(PDO::FETCH_ASSOC);
	$result = $req->fetchAll();

	return $result;
}

function query2json(string $query) {
	return json_encode(query($query));
}
?>