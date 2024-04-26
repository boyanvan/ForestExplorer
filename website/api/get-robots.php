<?php
require '../connect.php';

header('Content-Type: application/json; charset=utf-8');

$trails = query("SELECT name, description, geoPoints FROM trails");

$arr0 = json_decode($trails[0]['geoPoints'], true);
$arr1 = json_decode($trails[1]['geoPoints'], true);

echo json_encode([
	['name' => 'Explorer 01', 'traveled' => mt_rand(100, 1000) / 100, 'description' => 'Description ...', 'location' => $arr0[random_int(0, count($arr0) - 1)]],
	['name' => 'Steve', 'traveled' => mt_rand(100, 1000) / 100, 'description' => 'Description ...', 'location' => $arr1[random_int(0, count($arr1) - 1)]]
]);
?>