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
