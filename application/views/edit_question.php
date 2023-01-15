
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Help Me | Edit Question</title>

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
<?php include 'header.php'; ?>

<div class="mt-5" id="question-update-section">
	<script type="text/template" id="edit-question-template">
		<div class="container">
			<div class="mb-5 ">
				<h3>Edit Question</h3>
				<hr class="section-title-hr"/>
			</div>

			<!-- question title input -->
			<div class="row mt-5">
				<div class="col-md-2">
					<span>Question Title </span>
				</div>

				<div class="col-md-10">
					<div class="form-outline mb-4">
						<input type="text" id="question-title" class="form-control"
							   value="<%= data ? data.question.title:'' %>"/>
					</div>
				</div>
			</div>

			<!-- question body input -->
			<div class="row">
				<div class="col-md-2">
					<span>Question Body </span>
				</div>
				<div class="col-md-10">

					<div class="form-outline mb-4">
						<label for="question-body"></label>
						<textarea class="form-control" id="question-body" rows="4"> <%= data ? data.question.question:'' %></textarea>
					</div>
				</div>
			</div>

			<!-- question tags input -->
			<div class="row">
				<div class="col-md-2">
					<span>Tags </span>
				</div>

				<div class="col-md-10">
					<div class="form-outline mb-4">
						<input type="text" id="question-tags" class="form-control" value="<%= tags ? tags:'' %>"/>
						<label class="form-label text-secondary" for="question-tags">separate tags by comma (ex:
							python,java)</label>
					</div>
				</div>
			</div>


			<div class="text-right">
				<button id="update-question-btn" class="text-center btn btn-warning btn-m type= submit">
					Update
				</button>
				<button id="delete-question-btn" class="text-center btn btn-danger btn-m type= submit">
					Delete
				</button>
			</div>

			<div id="error-alert-section" class="my-5">

			</div>

		</div>
	</script>
	<script>
		$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
			options.url = 'http://localhost/help_me/index.php' + options.url;
		});

		const site_url = new URL(window.location.href);
		const questionId = site_url.searchParams.get("question_id");


		const TokenValidationModel = Backbone.Model.extend({
			url: "/api/token_verify"
		});
		const tokenValidationModel = new TokenValidationModel();

		function validateToken() {
			const token = window.localStorage.getItem('token');
			if (token == null) {
				window.location.href = "user_login.php";
			}
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
			setTimeout(validateToken, 300000);
		}

		validateToken();

		const QuestionUpdateModel = Backbone.Model.extend({
			url: "/api/update/question"
		});
		const questionUpdateModel = new QuestionUpdateModel();

		const QuestionRetrieveModel = Backbone.Model.extend({
			url: "/api/get/question?id=" + questionId
		});
		const questionRetrieveModel = new QuestionRetrieveModel();

		const QuestionDeleteModel = Backbone.Model.extend({
			url: "/api/delete/question/" + questionId
		});
		const questionDeleteModel = new QuestionDeleteModel({id: questionId, "question_id": questionId});

		const QuestionUpdateView = Backbone.View.extend({
				el: '#question-update-section',
				// model: questionSubmitModel,
				initialize: function () {
					this.getQuestionData()
				},
				render: function () {
					this.getQuestionData()
				},
				events: {
					"click #update-question-btn": "updateQuestion",
					"click #delete-question-btn": "deleteQuestion"
				},
				deleteQuestion: function () {
					if (confirm("Are you sure you want to delete")) {
						const self = this;
						const token = window.localStorage.getItem('token');
						questionDeleteModel.destroy({
							headers: {'Authorization': 'Bearer ' + token},
							async: false,
							contentType: 'application/json',
							success: function (users, response) {
								window.location.href = "user_profile.php";
							},
							error: function (model, response) {
								let errorMsg = "";

								switch (response.status) {
									case 401:
										window.location.href = "user_login.php";
										break;
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
								document.getElementById('question-update-section').innerHTML = "";
								const element = document.getElementById('question-update-section');
								let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='user_profile.php'> REDIRECT ME TO THE PROFILE</a>" + "</div>";
								element.insertAdjacentHTML('beforeend', html);
							}
						})
					}
				},
				getQuestionData: function () {
					const self = this;
					const token = window.localStorage.getItem('token');
					questionRetrieveModel.fetch({
						headers: {'Authorization': 'Bearer ' + token},
						async: false,
						contentType: 'application/json',
						success: function (users, response) {

							// binding all obj tags as a single string with comma seperated
							let tags = "";
							for (let i = 0; i < response.data.tags.length; i++) {

								if (i == 0) {
									tags = tags.concat(response.data.tags[i].tag)
								} else {
									tags = tags.concat(",", response.data.tags[i].tag)
								}
							}

							const template = _.template($('#edit-question-template').html(), {
								data: response.data,
								tags: tags
							});
							self.$el.html(template);
						},
						error: function (model, response) {

							let errorMsg = "";
							switch (response.status) {
								case 401:
									window.location.href = "user_login.php";
									break;
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
							document.getElementById('question-update-section').innerHTML = "";
							const element = document.getElementById('question-update-section');
							let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='user_profile.php'> REDIRECT ME TO THE PROFILE</a>" + "</div>";
							element.insertAdjacentHTML('beforeend', html);
						}
					})
				},
				updateQuestion: function () {
					document.getElementById('error-alert-section').innerHTML = "";

					const question_title = $('#question-title').val();
					const question_body = $('#question-body').val();
					const question_tags = $('#question-tags').val();

					if (question_title === "" || question_body === "" || question_tags === "") {
						const element = document.getElementById('error-alert-section');
						let html = "<div class='alert alert-danger'> empty question cannot submit </div>";
						element.insertAdjacentHTML('beforeend', html);
					} else {
						if (confirm("Are you sure you want to update")) {

							const questionDetails = {
								"question_id": questionId,
								"title": question_title,
								"question": question_body,
								"tags": question_tags
							};

							const self = this;
							const token = window.localStorage.getItem('token');
							questionUpdateModel.save(questionDetails, {
								headers: {'Authorization': 'Bearer ' + token},
								async: false,
								contentType: 'application/json',
								success: function (users, response) {

									const element = document.getElementById('error-alert-section');
									let html = "<div class='alert alert-success'>" + "QUESTION UPDATED SUCCESSFULLY" + "<a href='user_profile.php'> REDIRECT ME TO THE PROFILE</a>" + "</div>";
									element.insertAdjacentHTML('beforeend', html);
								},
								error: function (model, response) {

									let errorMsg = "";

									switch (response.status) {
										case 401:
											window.location.href = "user_login.php";
											break;
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
									document.getElementById('question-update-section').innerHTML = "";
									const element = document.getElementById('question-update-section');
									let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='user_profile.php'> REDIRECT ME TO THE PROFILE</a>" + "</div>";
									element.insertAdjacentHTML('beforeend', html);
								}
							});
						}
					}
				}
				,
			})
		;
		const questionUpdateView = new QuestionUpdateView();

	</script>
	<?php include 'footer.php'; ?>c
</body>
</html>
