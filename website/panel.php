<?php
require 'auth.php';
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/panel_style.css">

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
	<?php include 'header.php'; ?>

	<section class="main">
		<section class="robot">
			<div class="box">
				<h2>Robots</h2>
				<div id="robot-list" class="content list">
				</div>
			</div>
		</section>
		<section class="support">
			<div class="box">
				<h2>Customer messages</h2>
				<div id="support-list" class="content list">
				</div>
			</div>
		</section>
	</section>

	<script type="text/javascript">
		const elementDataMap = new WeakMap();

		const setElementData = (el, data) => {
		  elementDataMap.set(el, data);
		}

		const getElementData = (el) => {
		  return elementDataMap.get(el);
		}


		$('#logout').click(function () {
			window.location.href = "/api/logout.php";
		});
		$.ajax({
			url: '/api/get-robots.php',
			type: "GET",
			async: false,
			success: function(list) {
				let html = list.map(item => {
					return `<div class="item">
								<img class="icon">
								<p class="name">${item['name']}</p>
								<button class="btn-submit btn-small"><div class="dots">...</div></button>
							</div>`;
				}).join('\n');
				$('#robot-list').html(html);
				for (var i = 0; i < list.length; i++) {
					let el = document.querySelector(`#robot-list > div:nth-child(${i+1})`);
					setElementData(el, list[i]);
					el.querySelector('button').addEventListener('click', (e) => {
						console.log( getElementData(e.srcElement.closest('.robot-item')) );

					});
				}
			}
		});
		$.ajax({
			url: '/api/get-forms.php',
			type: "GET",
			async: false,
			success: function(list) {
				let html = list.map(item => {
					return `<div class="item">
								<p class="subject">Subject: ${item['subject']}</p>
								<p class="subject">Date: ${item['date']}</p>
								<button class="btn-submit btn-small"><div class="dots">...</div></button>
							</div>`;
				}).join('\n');
				$('#support-list').html(html);
				for (var i = 0; i < list.length; i++) {
					let el = document.querySelector(`#support-list > div:nth-child(${i+1})`);
					setElementData(el, list[i]);
					el.querySelector('button').addEventListener('click', (e) => {
						console.log( getElementData(e.srcElement.closest('.support-item')) );

					});
				}
			}
		});
	</script>
</body>
</html>