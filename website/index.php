<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home Page</title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/index_style.css">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Jim+Nightshade&display=swap" rel="stylesheet">

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
	<?php include 'header.php'; ?>

	<section class="hero">
		<div class="hero-text">
			<h1>Autonomous forest mapping</h1>
			<p>We make previously uncharted trails accessible worldwide on an easy to use map with basic features.</p>
		</div>
		<div class="img-box">
			<img src="assets/images/forward_trail.png">
		</div>
	</section>

	<section class="stats">
		<img src="assets/images/trail-scheming.png">
		<div class="content">
			<h1>Our goal is to automate the mapping of forest trails and save time and effort!</h1>
			<ul>
				<li><span class="num">0</span><span class="desc">Trails mapped</span></li>
				<li><span class="num">0</span><span class="desc">Robots produced</span></li>
				<li><span class="num">0</span><span class="desc">Active users</span></li>
			</ul>
		</div>
	</section>

	<section class="contacts">
		<div id="contactsForm" class="contacts-inner">
			<h2>CONTACT US</h2>

			<div class="row">
				<div class="combo">
					<label for="name">Name*</label>
					<input type="text" name="name" required>
				</div>
				<div class="combo">
					<label for="email">Email*</label>
					<input type="email" name="email" required>
				</div>
			</div>
			<div class="row">
				<div class="combo">
					<label for="phone">Phone Number*</label>
					<input type="tel" name="phone" required>
				</div>
				<div class="combo">
					<label for="subject">Subject</label>
					<input type="text" name="subject">
				</div>
			</div>
			<div class="row">
				<div class="combo full">
					<label for="message">Message*</label>
					<textarea name="message"></textarea>
				</div>
			</div>
			<div class="row">
				<button id="sendButton" class="btn-submit">SEND</button>
			</div>
		</div>
	</section>

  	<?php include 'footer.php'; ?>

  	<script type="text/javascript">
  		$(window).on('scroll', () => {
  			let y = window.pageYOffset;
  			console.log(y);
  			if (y == 0) {
  				$('#header').removeClass('scroll');
  			}
  			else {
  				$('#header').addClass('scroll');
  			}
  		});
  		$('#sendButton').click(() => {
  			let data = '';
  			$('#contactsForm input, #contactsForm textarea').each(function(i) {
  				let name = $(this).attr('name');
  				let value = $(this).val();
  				data += (i != 0 ? '&' : '') + encodeURIComponent(name) + '=' + encodeURIComponent(value);
  			});
  			$.ajax({
				url: '/api/post-form.php',
				type: "POST",
				data: data,
				success: function(data) {
					/*  empty  */
					alert(data['msg']);
				},
				error: function(data) {
					alert(data['msg']);
				}
			});
  		});
  	</script>
</body>
</html>