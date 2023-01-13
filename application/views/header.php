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
<div class="navbar bg-light border border-warning">
	<div class="container ">
		<div class="col-10">
			<a class="navbar-brand" href="index.php">
				<img src="./assets/logo.png" alt="logo" width="200 em" height="70 em">
			</a>
		</div>
		<div data-bs-toggle="tooltip" title="user profile" id="header-btn-section" class="col-1.5 text-right bg-white rounded border border-warning">

		</div>
	</div>
</div>

<script lang="JavaScript">

	// Initialize tooltips
	const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
	const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	});

	function setHeaderLoginIcon() {
		document.getElementById('header-btn-section').innerHTML = "";
		const token = window.localStorage.getItem('token');
		const fName = window.localStorage.getItem('name');
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
