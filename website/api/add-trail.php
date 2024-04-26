<?php
require '../connect.php';

if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['geoPoints'])) {
	$name = $_POST['name'];
	$desc = $_POST['description'];
	$geoPoints = $_POST['geoPoints'];
	query2json("INSERT INTO trails (name, description, geoPoints) VALUES ('$name', '$desc', '$geoPoints')");
}
?>