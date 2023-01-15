<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once("application/entities/User.php");
require_once("application/libraries/RestControllerLibrary.php");

Header('Access-Control-Allow-Origin: *'); //for allow any domain, insecure
Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

use dto\Response;
use entities\User;
use Libraries\RestControllerLibrary as RestController;

class AuthenticationController extends RestController
{

	public function index()
	{
		parent::__construct();
		$this->load->view('user_profile');
	}

	// API for the user registration
	public function user_register_post()
	{
		log_message(INFO_STATUS, "AuthenticationControllerLibrary - user_register_post(): function called ");

		try {
			// catching the received data
			$jsonArray = json_decode(file_get_contents('php://input'), true);
			$firstName = $jsonArray['firstName'];
			$lastName = $jsonArray['lastName'];
			$email = $jsonArray['email'];
			$password = $jsonArray['password'];

			// validating required fields and passing into users model
			if (!($firstName == "" or $lastName == "" or $email == "" or $password == "")) {

				/**
				 * @var Response $response
				 */
				$this->load->model('userModel');
				$response = $this->userModel->registerUser(new User($firstName, $lastName, $email, $password));

				if ($response->getStatus() == SUCCESS_STATUS) {
					$this->response($response->toString(), self::HTTP_OK);
				} else {
					$this->response($response->toString(), self::HTTP_BAD_REQUEST);
				}
			} else {
				$this->response("REQUIRED FIELDS ARE NOT FILLED", self::HTTP_UNPROCESSABLE_ENTITY);
			}

		} catch (Exception $exception) {
			log_message(ERROR_STATUS, "AuthenticationControllerLibrary - user_register_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function user_login_post()
	{
		log_message(INFO_STATUS, "AuthenticationControllerLibrary - user_login_post(): function called ");
		try {

			//capturing the request body data
			$jsonArray = json_decode(file_get_contents('php://input'), true);
			$email = $jsonArray['email'];
			$password = $jsonArray['password'];

			// validating required fields and passing into users model
			if (!($email == "" or $password == "")) {

				/**
				 * @var Response $response
				 */
				$this->load->model('userModel');
				$response = $this->userModel->loginUser($email, $password);

				if ($response->getStatus() == SUCCESS_STATUS) {
					$this->response($response->toString(), self::HTTP_OK);
				} else {
					$this->response($response->toString(), self::HTTP_BAD_REQUEST);
				}
			} else {
				$this->response("REQUIRED FIELDS ARE NOT FILLED", self::HTTP_UNPROCESSABLE_ENTITY);
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AuthenticationControllerLibrary - user_login_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	public function user_logout_post()
	{
		log_message(INFO_STATUS, "AuthenticationControllerLibrary - user_logout_post(): function called ");
		try {

			$headerToken = $this->input->get_request_header('Authorization');
			if ($headerToken != "") {

				// validating token
				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {

					$this->load->model('userModel');
					$response = $this->userModel->logoutUser($headerToken);

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
			log_message(ERROR_STATUS, "AuthenticationControllerLibrary - user_logout_post(): " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	public function validate_user_token_post()
	{
		log_message(INFO_STATUS, "AuthenticationControllerLibrary - validate_user_token_post(): function called ");
		try {

			// catching the entered token
			$headerToken = $this->input->get_request_header('Authorization');

			// validating required fields
			if ($headerToken != "") {

				$this->load->model('UserTokenModel');
				if ($this->UserTokenModel->validateRetrievedToken($headerToken)) {
					$this->response("VALID TOKEN", self::HTTP_OK);
				} else {
					$this->response("INVALID TOKEN", self::HTTP_UNAUTHORIZED);
				}
			} else {
				$this->response("TOKEN NOT FOUND", self::HTTP_UNPROCESSABLE_ENTITY);
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AuthenticationControllerLibrary - validate_user_token_post: " . $exception->getMessage());
			$this->response("EXCEPTION CAUGHT: " . $exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}
