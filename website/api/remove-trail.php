<?php
require '../connect.php';

if (isset($_POST['name'])) {
	$name = $_POST['name'];
	
	query_p("DELETE FROM trails WHERE name = :name",
		[':name' => $name]);
}
?>