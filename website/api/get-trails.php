<?php
require '../connect.php';

header('Content-Type: application/json; charset=utf-8');

echo query2json("SELECT name, description, geoPoints FROM trails");
?>