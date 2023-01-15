
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Help Me | Edit Answer</title>

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

<div class="mt-5" id="answer-update-section">
	<script type="text/template" id="edit-answer-template">
		<div class="container">
			<div class="mb-5 ">
				<h3>Edit Answer</h3>
				<hr class="section-title-hr"/>
			</div>

			<!-- question title input -->
			<div class="row mt-5">
				<div class="col-md-2">
					<span>Question Title </span>
				</div>

				<div class="col-md-10">
					<div class="form-outline mb-4">
						<input  disabled type="text" id="question-title" class="form-control"
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
						<textarea disabled class="form-control" id="question-body" rows="4"> <%= data ? data.question.question:'' %></textarea>
					</div>
				</div>
			</div>

			<!-- answer  input -->
			<div class="row">
				<div class="col-md-2">
					<span>Answer </span>
				</div>

				<div class="col-md-10">
					<div class="form-outline mb-4">
						<input type="text" id="question-answer" class="form-control" value="<%= data ? data.answer.answer:'' %>"/>
					</div>
				</div>
			</div>


			<div class="text-right">
				<button id="update-answer-btn" class="text-center btn btn-warning btn-m type= submit">
					Update
				</button>
				<button id="delete-answer-btn" class="text-center btn btn-danger btn-m type= submit">
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
		const answerId = site_url.searchParams.get("answer_id");
		const questionId ="" // updates when page loads


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

		const AnswerUpdateModel = Backbone.Model.extend({
			url: "/api/update/answer"
		});
		const answerUpdateModel = new AnswerUpdateModel();

		const AnswerViewModel = Backbone.Model.extend({
			url: "/api/get/answer?id="+answerId
		});
		const answerViewModel = new AnswerViewModel();

		const AnswerDeleteModel = Backbone.Model.extend({
			url: "/api/delete/answer/"+answerId
		});
		const answerDeleteModel = new AnswerDeleteModel({id:answerId});


		const AnswerUpdateView = Backbone.View.extend({
				el: '#answer-update-section',
				// model: questionSubmitModel,
				initialize: function () {
					this.getAnswerData()
				},
				render: function () {
					this.getAnswerData()
				},
				events: {
					"click #update-answer-btn": "updateAnswer",
					"click #delete-answer-btn": "deleteAnswer"
				},

				deleteAnswer: function () {
					if (confirm("Are you sure you want to delete")) {
						const self = this;
						const token = window.localStorage.getItem('token');
						answerDeleteModel.destroy({
							headers: {'Authorization': 'Bearer ' + token},
							async: false,
							contentType: 'application/json',
							success: function (users, response) {
								console.log("SUCCESS - QuestionAnswerView - fetch()");
								console.log(response);
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
								document.getElementById('answer-update-section').innerHTML = "";
								const element = document.getElementById('answer-update-section');
								let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='user_profile.php'> REDIRECT ME TO THE PROFILE</a>" + "</div>";
								element.insertAdjacentHTML('beforeend', html);

								console.log(response)
								console.log("ERROR - QuestionAnswerView fetch() CODE: " + response.status + " STATUS: " + response.statusText);
							}
						})
					}
				},
				getAnswerData: function () {
					const self = this;
					const token = window.localStorage.getItem('token');
					answerViewModel.fetch({
						headers: {'Authorization': 'Bearer ' + token},
						async: false,
						contentType: 'application/json',
						success: function (users, response) {
							console.log("SUCCESS - QuestionAnswerView - fetch()");
							console.log(response);

							self.questionId=response.data.question.id

							const template = _.template($('#edit-answer-template').html(), {data: response.data});
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
							document.getElementById('answer-update-section').innerHTML = "";
							const element = document.getElementById('answer-update-section');
							let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='user_profile.php'> REDIRECT ME TO THE PROFILE</a>" + "</div>";
							element.insertAdjacentHTML('beforeend', html);
							console.log("ERROR - QuestionAnswerView fetch() CODE: " + response.status + " STATUS: " + response.statusText);
						}
					})
				},
				updateAnswer: function () {
					document.getElementById('error-alert-section').innerHTML = "";
					const updated_answer = $('#question-answer').val();

					if (updated_answer === "" ) {
						const element = document.getElementById('error-alert-section');
						let html = "<div class='alert alert-danger'> empty answer cannot submit </div>";
						element.insertAdjacentHTML('beforeend', html);
					} else {
						if (confirm("Are you sure you want to update")) {

							const answerDetails = {
								"answerId": answerId,
								"answer": updated_answer
							};

							const self = this;
							const token = window.localStorage.getItem('token');
							answerUpdateModel.save(answerDetails, {
								headers: {'Authorization': 'Bearer ' + token},
								async: false,
								contentType: 'application/json',
								success: function (users, response) {

									const element = document.getElementById('error-alert-section');
									let html = "<div class='alert alert-success'>" +"ANSWER UPDATED SUCCESSFULLY" + "<a href='user_profile.php'> REDIRECT ME TO THE PROFILE</a>" + "</div>";
									element.insertAdjacentHTML('beforeend', html);

									// after sucess forward into question-answer view(after page complted)

									console.log("SUCCESS - questionUpdateModel-save()");
									console.log(response);
									//window.location.reload();
									//window.location.href = "add_answer.php?question_id="+self.questionId;

									//http://localhost/help_me/application/views/add_answer.php?question_id=79
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
											const responseData = JSON.parse(response.responseText);
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
									document.getElementById('answer-update-section').innerHTML = "";
									const element = document.getElementById('answer-update-section');
									let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='user_profile.php'> REDIRECT ME TO THE PROFILE</a>" + "</div>";
									element.insertAdjacentHTML('beforeend', html);
									console.log("ERROR - questionUpdateModel save() CODE: " + response.status + " STATUS: " + response.statusText);
								}
							});
						}
					}
				}
				,
			})
		;
		const answerUpdateView = new AnswerUpdateView();


	</script>
	<?php include 'footer.php'; ?>
</body>
</html>
