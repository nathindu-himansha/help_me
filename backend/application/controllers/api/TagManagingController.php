<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once("application/libraries/RestControllerLibrary.php");

use dto\Response;
use Libraries\RestControllerLibrary as RestController;

class TagManagingController extends RestController
{

	public function index()
	{
		parent::__construct();
	}


	// API for the add a question tag
	public function tag_create_post()
	{
		log_message(INFO_STATUS, "TagManagingController - tag_create_post(): function called ");

		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					// catching the received data
					$enteredTag = $this->input->post('tag');

					// validation rules for required fields
					$this->form_validation->set_rules('tag', 'Question Tag', 'required');

					// validating required fields and passing into tag model
					if ($this->form_validation->run()) {

						/**
						 * @var Response $response
						 */
						$this->load->model('TagModel');
						$response = $this->TagModel->createTag($enteredTag);

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
			log_message(ERROR_STATUS, "TagManagingController - tag_create_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	// API for the add a question tag
	public function all_tags_get()
	{
		log_message(INFO_STATUS, "TagManagingController - all_tags_get(): function called ");

		try {

			/**
			 * @var Response $response
			 */
			$this->load->model('TagModel');
			$response = $this->TagModel->getAllTags();

			if ($response->getStatus() == SUCCESS_STATUS) {
				$this->response($response->toString(), self::HTTP_OK);
			} else {
				$this->response($response->toString(), self::HTTP_BAD_REQUEST);
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "TagManagingController - all_tags_get(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
