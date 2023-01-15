<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Help Me | Home</title>

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
		#search-result {
			z-index: 10000;
			background-color: white;
			position: absolute;
			margin-top: 40px;
			width: 100%;
		}

		/*this element will append by backbone js*/
		#results-div {
			height: 40vh;
			overflow-y: scroll;
			border-bottom-style: solid;
			border-bottom-width: 1px;
			border-left-style: solid;
			border-left-width: 1px;
			border-right-style: solid;
			border-right-width: 1px;
			border-bottom-color: #E4A11B;
			border-right-color: #E4A11B;
			border-left-color: #E4A11B;
			padding: 10px;
			border-radius: 0 0 5px 5px;
			box-shadow: 0 0 40px rgba(51, 51, 51, .1);
		}

		.card {
			width: 100%;
			height: 14%;
			margin-bottom: 5px !important;
			margin-left: 0 !important;
			padding: 0 !important;
			border-top: none !important;
			border-left: none !important;
			border-right: none !important;
			border-bottom-style: solid;
			border-bottom-width: 1px;
			border-bottom-color: #E4A11B;


		}

		.card:hover {
			background-color: #F5F5F5 !important;
		}

		.card a {
			color: black !important;
			text-decoration: none !important;
			vertical-align: middle;
		}

		#results-close {
			color: red;
			background-color: #FFE9E9 !important;
			height: 2.5em;
			text-align: center;
			border: none !important;
			margin-bottom: 5px;

		}

		#results-close:hover {
			color: red;
			background-color: #FFE1E1 !important;
		}

		#question-card {
			border-style: ridge !important;
			border-color: #F5F4F4 !important;
		}
	</style>
</head>

<body>

<?php include 'header.php'; ?>
<div class="">

	<!--search-->
	<div class="row bg-light py-2 border-bottom border-warning">

		<div class="col-2"></div>
		<div id="search-input-section" class="col-8 my-2">


			<div class="input-group">
				<input id="search-input" type="search" class="form-control rounded" onsearch="OnSearch(this)"
					   placeholder="Enter your question to check whether it is already had someone" aria-label="Search"
				/>
				<button id="search-btn" type="button" class="btn btn-m btn-warning text-white ml-1">SEARCH</button>

				<div id="search-result">
				</div>
			</div>
		</div>
		<div class="col-2"></div>
	</div>

	<!--body-->

	<!--ask question-->
	<div>
		<div class="row mt-3 mx-5 ">
			<div class="col-9 border-right border-warning">
				<div class="text-right">
					<a href="add_question.php">
						<button id="as-question-btn" class=" text-white text-center btn btn-warning btn-m type= submit">
							ASK QUESTION
						</button>
					</a>
				</div>
			</div>
			<div class="col-3"></div>
		</div>

		<!--inner-body-->
		<div class="row mx-5 ">

			<div class="col-9 border-right border-warning">
				<div class="body-header mt-3">
					<h4>ALL QUESTIONS</h4>
					<hr>
				</div>

				<div id="body-data" class="container body-data mt-2">
					<script type="text/template" id="all-questions-template">
						<% _.each(data, function(question) { %>
						<div id="question-card" class="card mb-3 border border-secondary">

							<a style="color:black;text-decoration: none;"
							   href="add_answer.php?question_id=<%=question.id %>">

								<div class="card-body">
									<h5 class="card-title"><%= question.question_title %></h5>
									<h6 class="card-title">votes: <%= question.votes %></h6>
									<div class="text-right">
										<span class="card-title">asked on: &nbsp; <%= question.timestamp %></span>
									</div>
								</div>
							</a>
						</div>
						<% }); %>
					</script>
				</div>

			</div>
			<div class="col-3">

				<div class="body-header mt-3">
					<h6>TRENDING QUESTIONS</h6

				</div>

				<div id="trending-data" class="container body-data mt-4">
					<script type="text/template" id="trending-questions-template">

						<% _.each(data, function(question) { %>
						<div id="question-card" class="card  border border-secondary">

							<a style="color:black;text-decoration: none;"
							   href="add_answer.php?question_id=<%=question.id %>">

								<div class="card-body">
									<h6 class="card-title"><%= question.title %></h6>
								</div>
							</a>
						</div>
						<% }); %>
					</script>
				</div>


			</div>
		</div>
	</div>
</div>
<script lang="Javascript">

	$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
		options.url = 'http://localhost/help_me/index.php' + options.url;
	});

	function OnSearch(input) {
		if (input.value === "") {
			document.getElementById('search-result').innerHTML = "";
		}
	}

	function OnResultsClear() {
		document.getElementById('search-result').innerHTML = "";
	}


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
		setTimeout(validateToken, 1000000);
	}

	validateToken();

	const QuestionSearchModel = Backbone.Model.extend({
		url: "/api/search/question"
	});
	const questionSearchModel = new QuestionSearchModel();


	const AllQuestionsModel = Backbone.Model.extend({
		url: "/api/get/question"
	});
	const allQuestionsModel = new AllQuestionsModel();


	const TrendingQuestionsModel = Backbone.Model.extend({
		url: "/api/get/trending_question"
	});
	const trendingQuestionsModel = new TrendingQuestionsModel();


	const QuestionSearchInputView = Backbone.View.extend({
		el: '#search-input-section',
		model: questionSearchModel,
		initialize: function () {
		},
		render: function () {
		},
		events: {
			"click #search-btn": "retrieveSearchQuestions"
		},
		retrieveSearchQuestions: function () {
			questionSearchResultsView.retrieveSearchQuestions();


		}
	});
	const questionSearchInputView = new QuestionSearchInputView();


	const QuestionSearchResultsView = Backbone.View.extend({
		el: '#search-result',
		model: questionSearchModel,
		initialize: function () {
		},
		render: function () {
		},
		events: {},
		retrieveSearchQuestions: function () {
			document.getElementById('search-result').innerHTML = "";

			const entered_string = $('#search-input').val();
			if (entered_string !== "") {

				const self = this;
				const token = window.localStorage.getItem('token');
				questionSearchModel.save({"string": entered_string}, {
					headers: {'Authorization': 'Bearer ' + token},
					async: false,
					contentType: 'application/json',
					success: function (users, response) {
						const results = document.createElement("div");
						results.setAttribute("id", "results-div");

						// search close
						let close_result_start = document.createElement('div'); // is a node
						close_result_start.innerHTML = "<button id=results-close class=btn-block onclick='OnResultsClear()'> &nbsp; click to close the results</button>"
						results.appendChild(close_result_start);


						//search results
						response.data.forEach(element => {
							let result = document.createElement('span'); // is a node
							result.innerHTML =
								"<span class=card><a href=add_answer.php?question_id=" + element.id + ">" + element.question_title + "</a></span>";
							results.appendChild(result);
						});

						// search close
						if (response.data.length > 8) {
							let close_result_end = document.createElement('div'); // is a node
							close_result_end.innerHTML = "<button id=results-close class=btn-block onclick='OnResultsClear()'> &nbsp; click to close the results</button>"
							results.appendChild(close_result_end);
						}

						//search no results
						if (response.data.length == 0) {
							let close_result_end = document.createElement('div'); // is a node
							close_result_end.innerHTML = "<span  class=btn-block onclick='OnResultsClear()'> &nbsp; no results to display</span>"
							results.appendChild(close_result_end);
						}

						document.getElementById("search-result").appendChild(results);
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
						document.getElementById('search-input-section').innerHTML = "";
						const element = document.getElementById('search-data-section');
						let html = "<div class='alert alert-danger'>" + errorMsg + "</div>";
						element.insertAdjacentHTML('beforeend', html);
					}
				});
			}
		}
	});
	const questionSearchResultsView = new QuestionSearchResultsView();

	const AllQuestionsView = Backbone.View.extend({
		el: '#body-data',
		events: {},
		model: allQuestionsModel,
		initialize: function () {
			this.retrieveQuestionData()
		},
		render: function () {
			this.retrieveQuestionData()
		},
		validateToken: function () {

		},
		retrieveQuestionData: function () {
			const self = this;
			const token = window.localStorage.getItem('token');
			allQuestionsModel.fetch({
				headers: {'Authorization': 'Bearer ' + token},
				async: false,
				contentType: 'application/json',
				success: function (users, response) {
					const template = _.template($('#all-questions-template').html(), {data: response.data});
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
					document.getElementById('body-data').innerHTML = "";
					const element = document.getElementById('body-data');
					let html = "<div class='alert alert-danger'>" + errorMsg + "</div>";
					element.insertAdjacentHTML('beforeend', html);
				}
			});
		}
	});
	const allQuestionsView = new AllQuestionsView();


	const TrendingQuestionsView = Backbone.View.extend({
		el: '#trending-data',
		events: {},
		model: allQuestionsModel,
		initialize: function () {
			this.retrieveTrendingQuestionData()
		},
		render: function () {
			this.retrieveTrendingQuestionData()
		},
		validateToken: function () {

		},
		retrieveTrendingQuestionData: function () {
			const self = this;
			const token = window.localStorage.getItem('token');
			trendingQuestionsModel.fetch({
				headers: {'Authorization': 'Bearer ' + token},
				async: false,
				contentType: 'application/json',
				success: function (users, response) {
					const template = _.template($('#trending-questions-template').html(), {data: response.data});
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
					document.getElementById('body-data').innerHTML = "";
					const element = document.getElementById('body-data');
					let html = "<div class='alert alert-danger'>" + errorMsg + "</div>";
					element.insertAdjacentHTML('beforeend', html);
				}
			});
		}
	});
	const tendingQuestionsView = new TrendingQuestionsView();


</script>

<?php include 'footer.php'; ?>
</body>
</html>
