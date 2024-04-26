<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<link rel="stylesheet" type="text/css" href="assets/css/login_style.css">

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="assets/js/jsSHA/sha.js"></script>
</head>
<body>
	<?php include 'header.php'; ?>

	<section class="login">
		<div id="login-box" class="login-inner">
			<input type="text" name="username" placeholder="Username">
			<input type="password" name="password" placeholder="Password">

			<p class="switch-box">
				<span class="text">Login</span>
				<label class="switch">
					<input id="action_checkbox" type="checkbox">
					<span class="slider"></span>
				</label>
				<span class="text">Register</span>
			</p>

			<button id="login-btn">Login</button>
		</div>
	</section>

	<script type="text/javascript">
		function login() {
			let user = $('input[name=username]').val();
			let pass = $('input[name=password]').val();

			const shaObj = new jsSHA("SHA-384", "TEXT", { encoding: "UTF8" });
			shaObj.update(pass);
			const hash = shaObj.getHash("HEX");

			const url = $("#action_checkbox").is(":checked") ? '/api/register.php' : '/api/login.php';

			let data = {'username': user, 'password': hash}
			$.ajax({
				url: url,
				type: "POST",
				data: data,
				success: function(data) {
					const urlParams = new URLSearchParams(window.location.search);
					const myParam = urlParams.get('continue');
					if (myParam && myParam != '') {
						window.location.href = myParam;
					}
					else {
						// defaults to panel.php
						window.location.href = "/panel.php";
					}
				},
				error: function(data) {
					alert(data.responseJSON['msg']);
				}
			});
		}

		$('#login-box input').on('keypress',function(e) {
			if(e.which == 13) {
				login();
			}
		});
		$('#login-btn').click(login);
	</script>
</body>
</html>