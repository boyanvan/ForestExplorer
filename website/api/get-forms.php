<?php
require '../connect.php';

header('Content-Type: application/json; charset=utf-8');

$trails = query("SELECT name, description, geoPoints FROM trails");

$arr0 = json_decode($trails[0]['geoPoints'], true);
$arr1 = json_decode($trails[1]['geoPoints'], true);

echo json_encode(query("SELECT * FROM contactforminfo"));
?>