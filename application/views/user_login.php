
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Help Me | User Profile</title>

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

<div id="user-login-view" class="mt-2 ml-5 mr-5 ">
	<div class=" mt-5 container ">

		<div class="row mb-4 ">
			<div class="col-4"></div>
			<div class="col-4 bg-light text-dark pt-4">

				<div class="row justify-content-center mb-5">
					<img src="./assets/user-profile.png" alt="user-img" width="100vw"
						 height="80vh"/>
				</div>

				<div class="row justify-content-center mb-5 text-muted">
					<h5>Sign in into your account with</h5>
				</div>

				<!-- Email input -->
				<div class="form-outline mb-4">
					<label class="form-label" for="email_input">Email address</label>
					<input type="email" id="email_input" class="form-control" placeholder="enter your email address"/>
				</div>

				<!-- Password input -->
				<div class="form-outline mb-4">
					<label class="form-label" for="pwd_input">Password</label>
					<input type="password" id="pwd_input" class="form-control" placeholder="enter your password"/>
				</div>

				<!-- 2 column grid layout for inline styling -->
				<div class="row mb-4">

					<div class="col ">
						<!-- Simple link -->
						<a href="#!">Forgot password?</a>
					</div>
				</div>

				<!-- Submit button -->
				<button type=submit id="login-submit" class="btn btn-warning btn-block mb-4 text-light">Sign in</button>

				<!-- Register buttons -->
				<div class="text-center">
					<p>Not a member? <a href="user_register.php">Register</a></p>
				</div>

				<div id="error-alert-section">

				</div>

			</div>
		</div>
	</div>
	<div class="col-4"></div>

	<script lang="Javascript">


		$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
			options.url = 'http://localhost/help_me/index.php' + options.url;
		});

		const UserLoginModel = Backbone.Model.extend({
			url: "/api/login"
		});
		const userLoginModel = new UserLoginModel();

		const UserLoginView = Backbone.View.extend({
			el: '#user-login-view',
			events: {
				"click #login-submit": "userLogin"
			},
			model: userLoginModel,
			initialize: function () {
				//this.retrieveProfile()
			},
			render: function () {

				//this.retrieveProfile()
			},
			userLogin: function () {

				document.getElementById('error-alert-section').innerHTML = "";

				console.log('booooom');

				const email = $('#email_input').val();
				const password = $('#pwd_input').val();

				const loginData = {
					"email": email,
					"password": password
				};

				const self = this;
				userLoginModel.save(loginData, {
					async: false,
					contentType: 'application/json',
					success: function (users, response) {
						// window.location.reload();

						console.log("SUCCESS - userLoginModel-save()");
						console.log(response);
						console.log("token", response.data.token);
						window.localStorage.setItem("token", response.data.token);
						window.localStorage.setItem("name", response.data.firstName);
						// window.localStorage.setItem("id", response.data.token);
						window.location.href="http://localhost/help_me/application/views/user_profile.php";
					},
					error: function (model, response) {
						// const obj = JSON.parse(response)
						// console.log("weee"+obj)


						const responseData = JSON.parse(response.responseText);
						let errorMsg = "";

						switch (response.status) {
							case 422:
								errorMsg = "PLEASE FILL ALL THE FIELDS";
								break;
							case 400:
								errorMsg = responseData.message;
								break;
							case 500:
								errorMsg = "SYSTEM ERROR. PLEASE CONTACT SYSTEM ADMINISTRATION";
								break;
							default:
								errorMsg = "SOMETHING WENT WRONG";
								break;
						}
						const element = document.getElementById('error-alert-section');
						let html = "<div class='alert alert-danger'>" + errorMsg + "</div>";
						element.insertAdjacentHTML('beforeend', html);

						//document.getElementById('error-alert-section').append(<section id="error-alert-section" class="alert alert-danger" role="alert">responseData.message</section>);
						console.log("ERROR - userLoginModel save() CODE: " + response.status + " STATUS: " + response.statusText);
					}
				})
			}
		});
		const userLoginView = new UserLoginView();

	</script>
</body>
</html>
