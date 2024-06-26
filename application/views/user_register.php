<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Help Me | Register</title>

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

	<style>
		h5 {
			font-size: 1.2vw;
		}
	</style>
</head>

<body>

<div id="user-register-view" class="mt-2 ml-5 mr-5 ">
	<div class=" mt-5 container ">

		<div class="row mb-4 ">
			<div class="col-4"></div>
			<div class="col-4 bg-light text-dark pt-4">

				<div class="row justify-content-center mb-5">
					<img src="./assets/logo.png" alt="user-img" width="50%"
						 height="80%"/>
				</div>

				<div class="row justify-content-center mb-5 text-muted">
					<h5>Sign up into your account with</h5>
				</div>

				<!-- FName input -->
				<div class="form-outline mb-4">
					<label class="form-label" for="fName_input">First Name</label>
					<input type="text" id="fName_input" class="form-control" placeholder="enter your first name"/>
				</div>

				<!-- LName input -->
				<div class="form-outline mb-4">
					<label class="form-label" for="lName_input">Last Name</label>
					<input type="text" id="lName_input" class="form-control" placeholder="enter your last name"/>
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


				<!-- Submit button -->
				<button type=submit id="register-submit" class="btn btn-warning btn-block mb-4 text-light">Sign Up
				</button>

				<!-- login buttons -->
				<div class="text-center">
					<p>Already have an account? <a href="user_login.php">Login</a></p>
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

		const UserRegisterModel = Backbone.Model.extend({
			url: "/api/register"
		});
		const userRegisterModel = new UserRegisterModel();

		const UserRegisterView = Backbone.View.extend({
			el: '#user-register-view',
			events: {
				"click #register-submit": "userRegister"
			},
			model: userRegisterModel,
			initialize: function () {
			},
			render: function () {
			},
			userRegister: function () {

				document.getElementById('error-alert-section').innerHTML = "";

				const firstName = $('#fName_input').val();
				const lastName = $('#lName_input').val();
				const email = $('#email_input').val().toLowerCase();
				const password = $('#pwd_input').val();

				const validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

				// validations
				if (firstName === "" || lastName === "" || email === "" || password === "") {

					const element = document.getElementById('error-alert-section');
					let html = "<div class='alert alert-danger'>PLEASE FILL ALL THE FIELDS</div>";
					element.insertAdjacentHTML('beforeend', html);

				} else if (!(email.match(validRegex))) {
					const element = document.getElementById('error-alert-section');
					let html = "<div class='alert alert-danger'>EMAIL IS NOT VALID. PLEASE ENTER YOUR IIT EMAIL</div>";
					element.insertAdjacentHTML('beforeend', html);

				} else if (!(email.includes("@iit.ac.lk"))) {
					const element = document.getElementById('error-alert-section');
					let html = "<div class='alert alert-danger'>EMAIL IS NOT VALID. PLEASE ENTER YOUR IIT EMAIL</div>";
					element.insertAdjacentHTML('beforeend', html);

				} else if (password.length < 8) {
					const element = document.getElementById('error-alert-section');
					let html = "<div class='alert alert-danger'>PASSWORD SHOULD BE WITH MINIMUM 8 CHARACTERS</div>";
					element.insertAdjacentHTML('beforeend', html);

				} else {

					const registerData = {
						"firstName": firstName,
						"lastName": lastName,
						"email": email,
						"password": password
					};

					const self = this;
					userRegisterModel.save(registerData, {
						async: false,
						contentType: 'application/json',
						success: function (users, response) {
							const element = document.getElementById('error-alert-section');
							let html = "<div class='alert alert-success'> REGISTRATION SUCCESSFUL" + "<a href='user_login.php'> REDIRECT TO LOGIN</a>" + "</div>";
							element.insertAdjacentHTML('beforeend', html);
						},
						error: function (model, response) {

							let errorMsg = "";

							switch (response.status) {
								case 422:
									errorMsg = "PLEASE FILL ALL THE FIELDS";
									break;
								case 400:
									const responseData = JSON.parse(response.responseText);
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
						}
					})
				}
			}
		});
		const userRegisterView = new UserRegisterView();

	</script>
</body>
</html>
