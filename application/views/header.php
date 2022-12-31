<html lang="en">
<head>
	<meta charset="utf-8">


	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
		  integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
			integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
			crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
			integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
			crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
			integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
			crossorigin="anonymous"></script>

	<script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.2/underscore-min.js"
			type="text/javascript"></script>
	<script src="http://cdnjs.cloudflare.com/ajax/libs/backbone.js/0.9.2/backbone-min.js"></script>
</head>
<body>
<nav class="navbar bg-light">
	<div class="container">
		<div class="col-10">
			<a class="navbar-brand" href="#">
				<img src="./assets/user-profile.png" alt="logo" width="30" height="24">
			</a>
		</div>
		<div id="header-btn-section" class="col-2 text-right">

		</div>
	</div>
</nav>

<script lang="JavaScript">

	// $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
	// 	options.url = 'http://localhost/help_me/index.php' + options.url;
	// });
	//
	// const TokenValidationModel = Backbone.Model.extend({
	// 	url: "/api/token_verify"
	// });
	// const tokenValidationModel = new TokenValidationModel();
	//
	// function validateToken() {
	// 	document.getElementById('header-btn-section').innerHTML = "";
	//
	// 	//console.log("uuuuu");
	// 	//console.log(<p hhhhp //echo base_url("news/local/123"); ?>//);
	//
	// 	const token = window.localStorage.getItem('token');
	// 	tokenValidationModel.save({}, {
	// 		headers: {'Authorization': 'Bearer ' + token},
	// 		async: false,
	// 		contentType: 'application/json',
	// 		success: function (users, response) {
	// 			console.log("nhsbhjsd");
	// 			const element = document.getElementById('header-btn-section');
	// 			let html = "<a href='user_profile.php'><button class='border-0 bg-transparent' <button><img src='./assets/user-profile.png' alt='logo' width='30' height='24'/> </button></a>";
	// 			element.insertAdjacentHTML('beforeend', html);
	// 		},
	// 		error: function (model, response) {
	// 			if (response.status === 401) {
	// 				const element = document.getElementById('header-btn-section');
	// 				let html = "<a href='user_login.php'><button class='border-0 bg-transparent' <button class='btn-warning'>Login/Signup</button></a>";
	// 				element.insertAdjacentHTML('beforeend', html);
	//
	// 				window.location.href = "user_login.php";
	// 			}
	// 		}
	// 	});
	// 	setTimeout(validateToken, 10000000);
	// }

	//validateToken();

	function setHeaderLoginIcon() {
		document.getElementById('header-btn-section').innerHTML = "";
		const token = window.localStorage.getItem('token');
		const fName = window.localStorage.getItem('name');
		console.log("tojeee", token)
		if (token === null) {
			const element = document.getElementById('header-btn-section');
			let html = "<a href='user_login.php'><button class='btn border-0 btn-warning'>Login/Signup</button></a>";
			element.insertAdjacentHTML('beforeend', html);

		} else {
			const element = document.getElementById('header-btn-section');
			let html = "<a href='user_profile.php'><button class='btn border-0 bg-transparent'> <img src='./assets/user-profile.png' alt='logo' width='30' height='25'/> | " + fName+"</button></a>";
			element.insertAdjacentHTML('beforeend', html);

		}
		setTimeout(setHeaderLoginIcon, 1000000);
	}

	setHeaderLoginIcon();

</script>
</body>
</html>
