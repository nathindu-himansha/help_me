
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Help Me | Add Question</title>

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
<div class="mt-5" id="question-add-section">
	<div class="container">
		<div class="mb-5 ">
			<h3>Ask a Question</h3>
			<hr class="section-title-hr"/>
		</div>

		<!-- question title input -->
		<div class="row mt-5">
			<div class="col-md-2">
				<span>Question Title </span>
			</div>

			<div class="col-md-10">
				<div class="form-outline mb-4">
					<input type="text" id="question-title" class="form-control"/>
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
					<textarea class="form-control" id="question-body" rows="4"></textarea>
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
					<input type="text" id="question-tags" class="form-control"/>
					<label class="form-label text-secondary" for="question-tags">separate tags by comma (ex:
						python,java)</label>
				</div>
			</div>
		</div>


		<div class="text-right">
			<button id="add-question-btn" class="text-center btn btn-warning btn-m text-white" type="submit">
				Submit
			</button>
		</div>

		<div id="error-alert-section" class="mt-4">

		</div>

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

		const QuestionSubmitModel = Backbone.Model.extend({
			url: "/api/create/question"
		});
		const questionSubmitModel = new QuestionSubmitModel()

		const QuestionSubmitView = Backbone.View.extend({
			el: '#question-add-section',
			model: questionSubmitModel,
			initialize: function () {
				//this.retrieveProfile()
			},
			render: function () {
				//this.retrieveProfile()
			},
			events: {
				"click #add-question-btn": "addQuestion"
			},
			addQuestion: function () {

				const question_title = $('#question-title').val();
				const question_body = $('#question-body').val();
				const question_tags = $('#question-tags').val();

				const questionDetails = {
					"title": question_title,
					"question": question_body,
					"tags": question_tags,
				};
				if (question_title === "" || question_body === "" || question_tags === "") {
					const element = document.getElementById('error-alert-section');
					let html = "<div class='alert alert-danger'> empty question cannot submit. please fill all the fields </div>";
					element.insertAdjacentHTML('beforeend', html);
				} else {
					const self = this;
					const token = window.localStorage.getItem('token');
					questionSubmitModel.save(questionDetails, {
						headers: {'Authorization': 'Bearer ' + token},
						async: false,
						contentType: 'application/json',
						success: function (users, response) {
							window.location.href = "add_answer.php?question_id=" + response.data.question.question.id;
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
							document.getElementById('question-add-section').innerHTML = "";
							const element = document.getElementById('question-add-section');
							let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='index.php'> REDIRECT ME TO HOME</a>" + "</div>";
							element.insertAdjacentHTML('beforeend', html);
						}
					});
				}
			}
		});
		const questionSubmitView = new QuestionSubmitView();

	</script>
	<?php include 'footer.php'; ?>co
</body>
</html>
