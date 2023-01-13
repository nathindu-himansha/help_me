<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once("application/entities/User.php");
require_once("application/libraries/RestControllerLibrary.php");

use dto\Response;
use entities\User;
use Libraries\RestControllerLibrary as RestController;

class UserManagementController extends RestController
{
	public function index()
	{
		parent::__construct();
	}

	public function update_user_details_post()
	{
		log_message(INFO_STATUS, "UserManagementController - update_user_details_put(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					//capturing the request body data
					$jsonArray = json_decode(file_get_contents('php://input'),true);
					$firstName= $jsonArray['first_name'];
					$lastName= $jsonArray['last_name'];
					$email=$jsonArray['email'];

					// validating required fields and passing into users model
					if(!($firstName=="" or$lastName=="" or $email=="")) {

						$userProfileData = new User($firstName, $lastName, $email,"");

						// user profile update
						$this->load->model('UserModel');
						$response = $this->UserModel->updateUserDetails($headerToken,$userProfileData);

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
			log_message(ERROR_STATUS, "UserManagementController - update_user_details_put(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function retrieve_user_profile_get()
	{
		log_message(INFO_STATUS, "UserManagementController - retrieve_user_profile_get(): function called ");
		try {
			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					// user details by id
					$this->load->model('UserModel');
					$response = $this->UserModel->getUserProfileById($headerToken);

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
			log_message(ERROR_STATUS, "UserManagementController - retrieve_user_profile_get(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


}
