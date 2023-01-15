
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Help Me | Add Answer</title>

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
		.triangle-up {
			width: 0;
			height: 0;
			border-left: 15px solid transparent;
			border-right: 15px solid transparent;
			border-bottom: 30px solid #ffc107;
		}

		.triangle-up:hover {
			border-bottom: 30px solid #d9a60f;
		}

		.triangle-down {
			width: 0;
			height: 0;
			border-left: 15px solid transparent;
			border-right: 15px solid transparent;
			border-top: 30px solid #ffc107;
		}

		.triangle-down:hover {
			border-top: 30px solid #d9a60f;
		}
	</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="mt-5" id="answer-add-section">
	<script type="text/template" id="add-answer-template">
		<div class="container">

			<div class="row">
				<div class="mr-4">

					<!--vote-->
					<i class="bi bi-triangle"></i>

					<div data-bs-toggle="tooltip" title="This answer is useful" id="vote-up"
						 class="triangle-up mb-1"></div>
					<div class="text-center"><h2><%= data ? data.question.votes: 0 %></h2></div>

					<div data-bs-toggle="tooltip" title="This answer is not useful" id="vote-down"
						 class="triangle-down"></div>
				</div>

				<!--question-->
				<div class=" col-11 mb-5">
					<div class=" mb-5">
						<h3 class="text-justify"> <%= data ? data.question.title: '' %></h3>
						<span class="text-muted"> asked on: <%= data ? data.question.datetime: 0 %> &nbsp by  <%= data ? data.question.user_fName: "" %></span>
						<hr class="section-title-hr"/>
					</div>

					<div class=" mb-4">
						<p class="text-justify"><%= data ? data.question.question: 'no data to show' %></p>
					</div>

					<!--tags-->
					<div class=" mb-4">
						<% _.each(data.tags, function(tag) { %>
						<a href="#" class="badge badge-warning pt-1 py-1 px-2 text-white"><%=tag.tag%></a>
						<% }); %>
						<hr class="section-title-hr"/>
					</div>
				</div>
			</div>


			<!-- answers section -->
			<div class="row ">
				<div class="col-12">
					<h6 class="mb-3"><b>SUBMITTED ANSWERS</b></h6><br>

					<% _.each(data.answers, function(answer) { %>

					<div class="card mb-2 border">

						<div class="card-body">
							<h6 class="card-title"><%= answer.answer %></br></h6>
						</div>

						<div class="card-footer bg-white mt-1 text-muted">
							<div class="row">
								<div class="col-12 text-right">
									<span class="text-muted"> &nbsp;&nbsp answered on: <%= answer.timestamp %> &nbsp by  <%= answer.fkUserFirstName%></span>
								</div>
							</div>
						</div>
					</div>
					<% }); %>
				</div>
			</div>

			<!-- answers submit section -->
			<div class=" m5-5 px-5 py-4 mt-5 bg-light text-dark">
				<div class="row ">
					<div class="col-12">
						<h6 class="mb-3"><b>SUBMIT YOUR ANSWER</b></h6><br>


						<div class="form-outline">
							<label for="answer-submit-area"></label>
							<textarea class="form-control" id="answer-submit-area" rows="5"></textarea>
						</div>


						<div class="text-right my-3">
							<button id="add-answer" class=" text-white text-center btn btn-warning btn-m type= submit">
								Submit
							</button>
						</div>

						<div id="error-alert-section" class="mt-4">

						</div>

					</div>
				</div>
			</div>
			<br><br>


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


	const site_url = new URL(window.location.href);
	const questionId = site_url.searchParams.get("question_id");
	const QuestionAnswerModel = Backbone.Model.extend({
		url: "/api/get/question?id=" + questionId
	});
	const questionAnswerModel = new QuestionAnswerModel();

	const QuestionVoteModel = Backbone.Model.extend({
		url: "/api/question/vote"
	});
	const questionVoteModel = new QuestionVoteModel();

	const AnswerSubmitModel = Backbone.Model.extend({
		url: "/api/answer/question"
	});
	const answerSubmitModel = new AnswerSubmitModel();


	const QuestionAnswerView = Backbone.View.extend({
		el: '#answer-add-section',
		events: {
			"click #vote-up": "voteUpQuestion",
			"click #vote-down": "voteDownQuestion",
			"click #add-answer": "submitAnswer",
		},
		initialize: function () {
			this.retrieveAnswerData()
		},
		render: function () {
			this.retrieveAnswerData()
		},
		validateToken: function () {

		},
		submitAnswer: function () {
			document.getElementById('error-alert-section').innerHTML = "";

			const user_answer = $('#answer-submit-area').val();

			if (user_answer === "") {
				const element = document.getElementById('error-alert-section');
				let html = "<div class='alert alert-danger'> empty answer cannot submit </div>";
				element.insertAdjacentHTML('beforeend', html);
			} else {
				const self = this;
				const token = window.localStorage.getItem('token');

				const $answerData = {
					"questionId": questionId,
					"answer": user_answer
				}

				answerSubmitModel.save($answerData, {
					headers: {'Authorization': 'Bearer ' + token},
					async: false,
					contentType: 'application/json',
					success: function (users, response) {
						window.location.reload();
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
						document.getElementById('answer-add-section').innerHTML = "";
						const element = document.getElementById('answer-add-section');
						let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='index.php'> redirect me to home</a>" + "</div>";
						element.insertAdjacentHTML('beforeend', html);
					}
				});
			}
		},
		retrieveAnswerData: function () {
			const self = this;
			const token = window.localStorage.getItem('token');
			questionAnswerModel.fetch({
				headers: {'Authorization': 'Bearer ' + token},
				async: false,
				contentType: 'application/json',
				success: function (users, response) {
					const template = _.template($('#add-answer-template').html(), {data: response.data});
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
					document.getElementById('answer-add-section').innerHTML = "";
					const element = document.getElementById('answer-add-section');
					let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='index.php'> REDIRECT ME TO HOME</a>" + "</div>";
					element.insertAdjacentHTML('beforeend', html);
				}
			});

		},

		voteUpQuestion: function () {
			const self = this;
			const token = window.localStorage.getItem('token');
			const voteData = {
				"questionId": parseInt(questionId),
				"isUpVote": "true",
			}
			questionVoteModel.save(voteData, {
				headers: {'Authorization': 'Bearer ' + token},
				async: false,
				contentType: 'application/json',
				success: function (users, response) {
					window.location.reload();
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
					document.getElementById('answer-add-section').innerHTML = "";
					const element = document.getElementById('answer-add-section');
					let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='index.php'> REDIRECT ME TO HOME</a>" + "</div>";
					element.insertAdjacentHTML('beforeend', html);
				}
			});
		},
		voteDownQuestion: function () {
			const self = this;
			const token = window.localStorage.getItem('token');
			const voteData = {
				"questionId": parseInt(questionId),
				"isUpVote": "false",
			}
			questionVoteModel.save(voteData, {
				headers: {'Authorization': 'Bearer ' + token},
				async: false,
				contentType: 'application/json',
				success: function (users, response) {
					window.location.reload();
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
					document.getElementById('answer-add-section').innerHTML = "";
					const element = document.getElementById('answer-add-section');
					let html = "<div class='alert alert-danger'>" + errorMsg + "<a href='index.php'> REDIRECT ME TO HOME</a>" + "</div>";
					element.insertAdjacentHTML('beforeend', html);
				}
			});
		},
	});
	const questionAnswerView = new QuestionAnswerView();
</script>
<?php include 'footer.php'; ?>
</body>
</html>

