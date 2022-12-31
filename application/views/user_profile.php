
<?php include 'header.php';?>

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

<div class="mt-5" id="profile-data-section">
	<script type="text/template" id="user-profile-template">

		<!-- welcome section -->
		<div class="container">
			<div class="row mb-4">
				<div class="col-12">
					<h3>Welcome <%= data ? data.firstName: '' %></h3>
					<hr/>
				</div>
			</div>
		</div>

		<!--user profile section-->
		<div class="container mt-5 px-5 py-4 mt-5 bg-light text-dark">
			<div class="row ">
				<div class="col-md-3 text-center">
					<img src="./assets/user-profile.png" alt="user-img" width="180vw"
						 height="170vh"/>
				</div>
				<div class="col-md-9">
					<div class="row mb-4">
						<div class="col">
							<div class="form-outline">
								<label class="form-label" for="first_name_field">First Name</label>
								<input type="text" id="first_name_field" class="form-control" value="<%= data ? data.firstName:
								'' %>"/>
							</div>
						</div>
						<div class="col">
							<div class="form-outline">
								<label class="form-label" for="last_name_field">Last Name</label>
								<input type="email" id="last_name_field" class="form-control" value="<%= data ? data.lastName:
								'' %> "/>
							</div>
						</div>
					</div>
					<div class="row mb-4">
						<div class="col">
							<div class="form-outline flex-fill">
								<label class="form-label" for="email_field">Email</label>
								<input type="text" id="email_field" class="form-control" value="<%= data ? data.email:
								'' %> "/>
							</div>
						</div>
					</div>

				</div>
			</div>

			<div class="text-right">
				<button id="profile-update" class=" text-center btn btn-warning btn-m type= submit">
					Update Details
				</button>
				<button type="button" class="btn btn-danger" onclick = "logoutUser()">Logout</button>
			</div>

		</div>


		<!-- questions section -->
		<div class="container px-5 py-4 mt-5 bg-light text-dark">
			<div class="row ">
				<div class="col-12">
					<h4><b>YOUR QUESTIONS</b></h4><br>

					<% _.each(data.questions, function(question) { %>

					<div class="card mb-3 border border-secondary">

						<div class="card-body">
							<h5 class="card-title"><%= question.question_title %></br></h5>
							<span class="card-text"><%= question.question %></span>
						</div>

						<div class="card-footer bg-white mt-2 text-muted">
							<div class="row">
								<div class="col-md-3"><b>votes: <%= question.votes %> </b><br>
									asked on: <%= question.timestamp %>
								</div>
								<div class="col-md-9">
									<div class="text-right">
										<button class=" text-center btn btn-warning btn-m type= submit">
											Edit
										</button>
										<button class=" text-center btn btn-danger btn-m type= submit">
											Delete
										</button>
									</div>
								</div>
							</div>
						</div>

					</div>
					<% }); %>
				</div>
			</div>
		</div>

		</br>

		<!-- answers section -->
		<div class="container mt-5 px-5 py-4 mt-5 bg-light text-dark">
			<div class="row ">
				<div class="col-12">
					<h4 class="mb-3"><b>YOUR ANSWERS</b></h4><br>

					<% _.each(data.answers, function(answer) { %>

					<div class="card mb-3 border border-secondary">

						<div class="card-body">
							<h5 class="card-title"><%= answer.answer %></br></h5>
						</div>

						<div class="card-footer bg-white mt-2 text-muted">
							<div class="row">
								<div class="col-md-5">
									answered on: <%= answer.timestamp %>
								</div>
								<div class="col-md-7">
									<div class="text-right">
										<button class=" text-center btn btn-warning btn-m type= submit">
											Edit
										</button>
										<button class=" text-center btn btn-danger btn-m type= submit">
											Delete
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<% }); %>
				</div>
			</div>
		</div>

	</script>
</div>


<script lang="Javascript">

	$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
		options.url = 'http://localhost/help_me/index.php' + options.url;
	});


	const TokenValidationModel = Backbone.Model.extend({
		url: "/api/token_verify"
	});
	const tokenValidationModel = new TokenValidationModel();

	function validateToken() {

		//console.log("uuuuu");
		//console.log(<p hhhhp //echo base_url("news/local/123"); ?>//);

		const token = window.localStorage.getItem('token');
		tokenValidationModel.save({}, {
			headers: {'Authorization': 'Bearer ' + token},
			async: false,
			contentType: 'application/json',
			success: function (users, response) {
			},
			error: function (model, response) {
				if (response.status === 401) {
					localStorage.clear();
					window.location.href = "user_login.php";
				}
			}
		});
		setTimeout(validateToken, 1000000);
	}

	validateToken();

	const LogoutModel = Backbone.Model.extend({
		url: "/api/logout"
	});
	const logoutModel = new LogoutModel();

	function logoutUser() {
		const token = window.localStorage.getItem('token');
		logoutModel.save({},{
			headers: {'Authorization': 'Bearer ' + token},
			async: false,
			contentType: 'application/json',
			success: function (users, response) {
				localStorage.clear();
				window.location.href = "user_login.php";
			},
			error: function (model, response) {

				if (response.status === 401) {
					window.location.href = "user_login.php";
				}
				console.log("ERROR - userProfileModel fetch() CODE: " + response.status + " STATUS: " + response.statusText);
			}
		});
	}



	const UserProfileModel = Backbone.Model.extend({
		url: "/api/user"
	});
	const userProfileModel = new UserProfileModel()

	const UserProfileView = Backbone.View.extend({
		el: '#profile-data-section',
		events: {},
		model: userProfileModel,
		initialize: function () {
			this.retrieveProfile()
		},
		render: function () {
			this.retrieveProfile()
		},
		validateToken: function () {

		},
		retrieveProfile: function () {
			const self = this;
			const token = window.localStorage.getItem('token');
			userProfileModel.fetch({
				headers: {'Authorization': 'Bearer ' + token},
				async: false,
				contentType: 'application/json',
				success: function (users, response) {
					console.log("SUCCESS - userProfileModel-fetch()");
					console.log(response);

					var template = _.template($('#user-profile-template').html(), {data: response.data});
					self.$el.html(template);
				},
				error: function (model, response) {

					if (response.status === 401) {
						window.location.href = "user_login.php";
					}
					console.log("ERROR - userProfileModel fetch() CODE: " + response.status + " STATUS: " + response.statusText);
				}
			});
		}
	});
	const userProfileView = new UserProfileView();


	const UserProfileUpdateModel = Backbone.Model.extend({
		url: "/api/user/update"
	});
	const userProfileUpdateModel = new UserProfileUpdateModel()


	const UserProfileUpdateView = Backbone.View.extend({
		el: '#profile-data-section',
		model: userProfileUpdateModel,
		initialize: function () {
			//this.retrieveProfile()
		},
		render: function () {
			//this.retrieveProfile()
		},
		events: {
			"click #profile-update": "updateProfile"
		},
		updateProfile: function () {

			const first_name = $('#first_name_field').val();
			const last_name = $('#last_name_field').val();
			const email = $('#email_field').val();


			const userUpdatedDetails = {
				"first_name": first_name,
				"last_name": last_name,
				"email": email,
			};

			const self = this;
			const token = window.localStorage.getItem('token');
			userProfileUpdateModel.save(userUpdatedDetails, {
				headers: {'Authorization': 'Bearer ' + token},
				async: false,
				contentType: 'application/json',
				success: function (users, response) {
					window.location.reload();

					console.log("SUCCESS - userProfileUpdateModel-save()");
					console.log(response);
				},
				error: function (model, response) {
					if (response.status === 401) {
						window.location.href = "user_login.php";
					}
					console.log("ERROR - userProfileUpdateModel save() CODE: " + response.status + " STATUS: " + response.statusText);
				}
			});
		}
	});
	const userProfileUpdateView = new UserProfileUpdateView();

	//
	// var Router = Backbone.Router.extend({
	// 	routes: {
	// 		"": "defaultRoute",
	// 		"/login": "firstRoute",
	// 		"2": "secondRoute",
	// 		"3": "thirdRoute",
	// 	},
	// 	defaultRoute: function () {
	// 		this.navigate("#/default");
	// 	},
	// 	firstRoute: function () {
	// 		this.navigate("http://localhost/help_me/application/views/login.php");
	// 	},
	// 	secondRoute: function () {
	// 		this.navigate("#/second");
	// 	},
	// 	thirdRoute: function () {
	// 		this.navigate("#/third");
	// 	},
	// });
	//
	// router = new Router();
	// Backbone.history.start();

</script>

</body>
</html>
