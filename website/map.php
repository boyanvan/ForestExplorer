<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Map Page</title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/map_style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/mapbox_style.css">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Jim+Nightshade&display=swap" rel="stylesheet">


	<script src='https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.js'></script>
	<link href='https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.css' rel='stylesheet' />
	<script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
</head>
<body>
	<?php include 'header.php'; ?>

	<div id='map' class="map"></div>
	<div class="toolbar" style="display:none">
		<div class="combo">
			<label for="editMode">Edit Mode</label>
			<input id='editModeCheckbox' type="checkbox" name="editMode">
		</div>
		<button id='saveLineBtn' class="btn-submit">Save line</button>
	</div>

	<?php include 'footer.php'; ?>

	<script>
		mapboxgl.accessToken = 'pk.eyJ1IjoiaG9nb3RvIiwiYSI6ImNsM3B2ZWkyMjA2YXUzam1zcHZtazlpbXkifQ.O37qtLHrUTSjH91IveGMOg';
		const map = new mapboxgl.Map({
			container: 'map', // container ID
			style: "mapbox://styles/mapbox/outdoors-v12", // style URL
			center: [27.8603366, 43.2049365], // starting position [lng, lat]
			zoom: 12, // starting zoom
		});
	</script>
	<script>    
		let collection = {
				'type': 'FeatureCollection',
				'features': []
		};
		$.ajax({
			url: '/api/get-trails.php',
			type: "GET",
			async: false,
			success: function(list) {
				list.forEach(item => {
					let feature = {"type":"Feature","properties":{"name":item["name"],"description":item["description"]},"geometry":{"type":"LineString","coordinates":JSON.parse(item["geoPoints"])}};
					collection.features.push(feature);
				});
			}
		});
		let featureIndex = collection.features.length;
		
		map.on('load', () => {
				map.addSource('trails', {
						'type': 'geojson',
						'data': collection
				});
				map.addLayer({
						'id': 'trailsLayer',
						'type': 'line',
						'source': 'trails',
						'paint': {
								'line-width': 4,
								'line-color': '#964b00'
						}
				});
		});

		$.ajax({
			url: '/api/get-robots.php',
			type: "GET",
			async: false,
			success: function(list) {
				list.forEach(item => {
					let feature = {"type":"Feature","properties":{"name":item["name"], "traveled":item["traveled"],"description":item["description"]},"geometry":{"type":"Point","coordinates":item["location"]}};

					// create a HTML element for each feature
					const el = document.createElement('div');
					el.className = 'marker';

					var marker = new mapboxgl.Marker(el)
						.setLngLat(feature.geometry.coordinates)
						.setPopup(
						new mapboxgl.Popup({ offset: 25 }) // add popups
								.setHTML(
									`
									<h3>${feature.properties.name}</h3>
									<p>Traveled distance: ${feature.properties.traveled} km</p>
									<p><em>${feature.properties.description}</em></p>
									`
								)
							)
						.addTo(map);
				});
			}
		});

		map.on('click', (e) => {
			if ($('#editModeCheckbox').is(":checked") === false) return;
			
			let coords = [e.lngLat.lng, e.lngLat.lat];
			console.log('Point: ' + coords);
			if (typeof collection.features[featureIndex] === 'undefined') {
				console.log('Adding new feature');
				collection.features.push({
															'type': 'Feature',
															'properties': {},
															'geometry': {
																	'type': 'LineString',
																	'coordinates': [
																		coords
																	]
															}
													});
			}
			else {
				collection.features[featureIndex].geometry.coordinates.push(coords);
			}
			map.getSource('trails').setcollection(collection);
		});
		map.on('click', 'trailsLayer', (e) => {
			const name = e.features[0].properties.name;
			const description = e.features[0].properties.description;
			const dist = turf.length(e.features[0]);
			
			const html = `<h3>${name}</h3><p>Distance: ${dist.toFixed(2)} km</p><p>Description: ${description}</p>`;

			new mapboxgl.Popup()
			.setLngLat(e.lngLat)
			.setHTML(html)
			.addTo(map);
		});
		map.on('mouseenter', 'trailsLayer', () => {
				map.getCanvas().style.cursor = 'pointer';
		});
		map.on('mouseleave', 'trailsLayer', () => {
				map.getCanvas().style.cursor = '';
		});

		$('#saveLineBtn').click(async function() {
			if (typeof collection.features[featureIndex] === 'undefined' || collection.features[featureIndex].geometry.coordinates.length === 0) {
				alert('Please create a trail first');
			}
			else {
				collection.features[featureIndex].properties.color = '#964b00'; // brown
				map.getSource('trails').setcollection(collection);
				let json = JSON.stringify(collection.features[featureIndex]);
				let count = findKeys('trail|').length;
				let name = 'trail|' + count; // await sha256(json)
				setKey(name, json);
				featureIndex++;
			}
		});
	</script>
</body>
</html>