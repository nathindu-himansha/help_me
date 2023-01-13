<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once("application/libraries/RestControllerLibrary.php");

use Libraries\RestControllerLibrary as RestController;

class QuestionManagingController extends RestController
{

	public function index()
	{
		parent::__construct();
	}


	public function create_question_post()
	{
		log_message(INFO_STATUS, "QuestionManagingController - create_question_post(): function called ");
		try {

			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

//					// capturing the request body data
//					$enteredTitle = $this->input->post('title');
//					$enteredQuestion = $this->input->post('question');
//					$enteredTags = $this->input->post('tags');

					//capturing the request body data
					$jsonArray = json_decode(file_get_contents('php://input'), true);
					$enteredTitle = $jsonArray['title'];
					$enteredQuestion = $jsonArray['question'];
					$enteredTags = $jsonArray['tags'];


//					// validation rules for required fields
//					$this->form_validation->set_rules('title', 'Title', 'required');
//					$this->form_validation->set_rules('question', 'Question', 'required');

					// validating required fields and passing into users model
					if (!($enteredTitle == "" or $enteredQuestion == "" or $enteredTags == "")) {

						// passing the data into create the questions and tag mappings
						$this->load->model('QuestionModel');
						$response = $this->QuestionModel->createQuestion($headerToken, $enteredTitle, $enteredQuestion, $enteredTags);

						if ($response->getStatus() == SUCCESS_STATUS) {
							$this->response($response->toString(), self::HTTP_OK);
						} else {
							$this->response($response->toString(), self::HTTP_BAD_REQUEST);
						}

					} else {
						$this->response("REQUIRED FIELDS ARE NOT FILLED", self::HTTP_UNPROCESSABLE_ENTITY);
					}

				} else {
					$this->response("INVALID TOKEN", self::HTTP_UNAUTHORIZED);
				}


			} else {
				$this->response("TOKEN NOT FOUND", self::HTTP_UNPROCESSABLE_ENTITY);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionManagingController - create_question_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function update_question_post()
	{
		log_message(INFO_STATUS, "QuestionManagingController - update_question_put(): function called ");
		try {

			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

//					// capturing the request body data
//					$enteredTitle = $this->input->post('title');
//					$enteredQuestion = $this->input->post('question');
//					$enteredTags = $this->input->post('tags');

					//capturing the request body data
					$jsonArray = json_decode(file_get_contents('php://input'), true);
					$questionId = $jsonArray['question_id'];
					$enteredTitle = $jsonArray['title'];
					$enteredQuestion = $jsonArray['question'];
					$enteredTags = $jsonArray['tags'];


//					// validation rules for required fields
//					$this->form_validation->set_rules('title', 'Title', 'required');
//					$this->form_validation->set_rules('question', 'Question', 'required');

					// validating required fields and passing into users model
					if (!($questionId == "" or $enteredTitle == "" or $enteredQuestion == "" or $enteredTags == "")) {

						// passing the data into create the questions and tag mappings
						$this->load->model('QuestionModel');
						$response = $this->QuestionModel->updateQuestion($headerToken, intval($questionId), $enteredTitle, $enteredQuestion, $enteredTags);

						if ($response->getStatus() == SUCCESS_STATUS) {
							$this->response($response->toString(), self::HTTP_OK);
						} else {
							$this->response($response->toString(), self::HTTP_BAD_REQUEST);
						}

					} else {
						$this->response("REQUIRED FIELDS ARE NOT FILLED", self::HTTP_UNPROCESSABLE_ENTITY);
					}

				} else {
					$this->response("INVALID TOKEN", self::HTTP_UNAUTHORIZED);
				}


			} else {
				$this->response("TOKEN NOT FOUND", self::HTTP_UNPROCESSABLE_ENTITY);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionManagingController - update_question_put(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function delete_question_delete($id)
	{
		log_message(INFO_STATUS, "QuestionManagingController - delete_question_delete(): function called ");
		try {

			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					log_message(INFO_STATUS,"IDDDDDDDDD".strval($id));

					//capturing the request body data
					$jsonArray = json_decode(file_get_contents('php://input'), true);
					$questionId =$id;


					// validating required fields and passing into users model
					if (!($questionId == "")) {

						// passing the data into create the questions and tag mappings
						$this->load->model('QuestionModel');
						$response = $this->QuestionModel->deleteQuestion($headerToken, $questionId);

						if ($response->getStatus() == SUCCESS_STATUS) {
							$this->response($response->toString(), self::HTTP_OK);
						} else {
							$this->response($response->toString(), self::HTTP_BAD_REQUEST);
						}

					} else {
						$this->response("REQUIRED FIELDS ARE NOT FILLED", self::HTTP_UNPROCESSABLE_ENTITY);
					}

				} else {
					$this->response("INVALID TOKEN", self::HTTP_UNAUTHORIZED);
				}


			} else {
				$this->response("TOKEN NOT FOUND", self::HTTP_UNPROCESSABLE_ENTITY);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionManagingController - delete_question_delete(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function retrieve_question_get()
	{
		log_message(INFO_STATUS, "QuestionManagingController - question_get(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					// capturing the request body data
					$questionId = $this->input->get('id');
					$this->load->model('QuestionModel');

					// checking whether id is exits or not
					if (!$questionId == "") {
						// question by id
						$response = $this->QuestionModel->getQuestionDataById(intval($questionId));

					} else {
						// all questions
						$response = $this->QuestionModel->getAllQuestions(intval($questionId));
					}

					if ($response->getStatus() == SUCCESS_STATUS) {
						$this->response($response->toString(), self::HTTP_OK);
					} else {
						$this->response($response->toString(), self::HTTP_BAD_REQUEST);
					}

				} else {
					$this->response("INVALID TOKEN", self::HTTP_UNAUTHORIZED);
				}

			} else {
				$this->response("TOKEN NOT FOUND", self::HTTP_UNPROCESSABLE_ENTITY);

			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionManagingController - question_get(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function answer_question_post()
	{
		log_message(INFO_STATUS, "QuestionManagingController - answer_question_post(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

//					// capturing the request body data
//					$questionId = $this->input->post('questionId');
//					$answer = $this->input->post('answer');

					//capturing the request body data
					$jsonArray = json_decode(file_get_contents('php://input'), true);
					$questionId = $jsonArray['questionId'];
					$answer = $jsonArray['answer'];

//					// validation rules for required fields
//					$this->form_validation->set_rules('questionId', 'Question ID', 'required');
//					$this->form_validation->set_rules('answer', 'Answer', 'required');

					// validating required fields and passing into users model
					if (!($questionId == "" or $answer == "")) {
						$this->load->model('AnswerModel');

						$response = $this->AnswerModel->createAnswer($headerToken, intval($questionId), $answer);
						if ($response->getStatus() == SUCCESS_STATUS) {
							$this->response($response->toString(), self::HTTP_OK);
						} else {
							$this->response($response->toString(), self::HTTP_BAD_REQUEST);
						}

					} else {
						$this->response("REQUIRED FIELDS ARE NOT FILLED", self::HTTP_UNPROCESSABLE_ENTITY);
					}
				} else {
					$this->response("INVALID TOKEN", self::HTTP_UNAUTHORIZED);
				}

			} else {
				$this->response("TOKEN NOT FOUND", self::HTTP_UNPROCESSABLE_ENTITY);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionManagingController - answer_question_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function trending_question_get()
	{
		log_message(INFO_STATUS, "QuestionManagingController - trending_question_get(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {


					$this->load->model('QuestionModel');
					$response = $this->QuestionModel->getTrendingQuestions();

					if ($response->getStatus() == SUCCESS_STATUS) {
						$this->response($response->toString(), self::HTTP_OK);
					} else {
						$this->response($response->toString(), self::HTTP_BAD_REQUEST);
					}

				} else {
					$this->response("INVALID TOKEN", self::HTTP_UNAUTHORIZED);
				}

			} else {
				$this->response("TOKEN NOT FOUND", self::HTTP_UNPROCESSABLE_ENTITY);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionManagingController - trending_question_get(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function vote_question_post()
	{
		log_message(INFO_STATUS, "QuestionManagingController - vote_question_post(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

//					// capturing the request body data
//					$questionId = $this->input->post('questionId');
//					$isUpVote = $this->input->post('isUpVote');

					//capturing the request body data
					$jsonArray = json_decode(file_get_contents('php://input'), true);
					$questionId = $jsonArray['questionId'];
					$isUpVote = $jsonArray['isUpVote'];
					log_message(INFO_STATUS, "HSHSHSHSHSHSHS::::" . $questionId . ";;;;" . $isUpVote);

					// validation rules for required fields
//					$this->form_validation->set_rules('questionId', 'Question ID', 'required');
//					$this->form_validation->set_rules('isUpVote', 'isUpVote', 'required');

					// validating required fields and passing into users model
					if (!($questionId == "" or $isUpVote == "")) {
						$this->load->model('QuestionModel');

						if ($isUpVote === "true") {
							$vote = true;
						} else {
							$vote = false;
						}

						$response = $this->QuestionModel->voteQuestion(intval($questionId), $vote);
						if ($response->getStatus() == SUCCESS_STATUS) {
							$this->response($response->toString(), self::HTTP_OK);
						} else {
							$this->response($response->toString(), self::HTTP_BAD_REQUEST);
						}

					} else {
						$this->response("REQUIRED FIELDS ARE NOT FILLED", self::HTTP_UNPROCESSABLE_ENTITY);
					}
				} else {
					$this->response("INVALID TOKEN", self::HTTP_UNAUTHORIZED);
				}

			} else {
				$this->response("TOKEN NOT FOUND", self::HTTP_UNPROCESSABLE_ENTITY);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionManagingController - vote_question_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


}
