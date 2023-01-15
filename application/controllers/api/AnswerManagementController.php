<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once("application/libraries/RestControllerLibrary.php");

use Libraries\RestControllerLibrary as RestController;

class AnswerManagementController extends RestController
{

	public function index()
	{
		parent::__construct();
	}

	public function answer_question_post()
	{
		log_message(INFO_STATUS, "AnswerManagementController - answer_question_post(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					//capturing the request body data
					$jsonArray = json_decode(file_get_contents('php://input'), true);
					$questionId = $jsonArray['questionId'];
					$answer = $jsonArray['answer'];

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
			log_message(ERROR_STATUS, "AnswerManagementController - answer_question_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function answer_update_post()
	{
		log_message(INFO_STATUS, "AnswerManagementController - answer_update_post(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					//capturing the request body data
					$jsonArray = json_decode(file_get_contents('php://input'), true);
					$answerId = $jsonArray['answerId'];
					$answer = $jsonArray['answer'];

					// validating required fields and passing into users model
					if (!($answerId == "" or $answer == "")) {
						$this->load->model('AnswerModel');

						$response = $this->AnswerModel->updateAnswer($headerToken, intval($answerId), $answer);
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
			log_message(ERROR_STATUS, "AnswerManagementController - answer_update_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function delete_answer_delete($id)
	{
		log_message(INFO_STATUS, "AnswerManagementController - answer_delete_post(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					//capturing the request body data
					$answerId = $id;

					// validating required fields and passing into users model
					if (!($answerId == "")) {
						$this->load->model('AnswerModel');

						$response = $this->AnswerModel->deleteAnswer($headerToken, intval($answerId));
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
			log_message(ERROR_STATUS, "AnswerManagementController - answer_delete_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function retrieve_answer_get()
	{
		log_message(INFO_STATUS, "AnswerManagementController - answer_get(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					//capturing the request  data
					$answerId = $this->input->get('id');;

					// validating required fields and passing into users model
					if (!($answerId == "")) {
						$this->load->model('AnswerModel');

						$response = $this->AnswerModel->getAnswerWithQuestionById(intval($answerId));
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
			log_message(ERROR_STATUS, "AnswerManagementController - answer_get(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

}
