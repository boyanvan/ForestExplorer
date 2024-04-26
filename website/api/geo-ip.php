<?php
require '../connect.php';

if (isset($_GET['ip'])) {
	$ip = $_GET['ip'];
	$result = query2json("SELECT lat, lng FROM ipData WHERE ip = INET_ATON('$ip')");
	if ($result == '[]') {
		$geoipAPIKey = 'fe57865d34ee495895db238c1c9a6420';
		$session = curl_init("https://api.ipgeolocation.io/ipgeo?apiKey=$geoipAPIKey&ip=$ip");
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($session);
		curl_close($session);

		$data = json_decode($data);		
		query2json("INSERT INTO ipData (ip, lat, lng) VALUES (INET_ATON('$ip'), '$data->latitude', '$data->longitude')");
		$result = "[{\"lat\":\"$data->latitude\",\"lng\":\"$data->longitude\"}]";
	}
	echo $result;
}
?>