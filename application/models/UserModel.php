<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("application/entities/User.php");
require_once("application/dto/Response.php");
require_once("application/dto/UserData.php");
require_once("application/dto/UserProfileData.php");
require_once("application/libraries/JwtLibrary.php");

use entities\User;
use dto\Response;
use dto\UserData;
use dto\UserProfileData;
use Libraries\jwtLibrary as jwt;


class UserModel extends CI_Model
{

	// loads the database while initiating the class
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}


	/**
	 * @throws Exception
	 */
	// function for register the user by processing user data and save into the database
	public function registerUser(User $user): Response
	{
		try {
			log_message(INFO_STATUS, "UserModel - registerUser(): function called ");

			if ($this->checkEmailIsExistsInUserTable($user->getEmail())) {
				log_message(ERROR_STATUS, "UserModel - registerUser(): already exits entered email");
				return new Response(ERROR_STATUS, "ENTERED EMAIL ALREADY EXISTS ", null);
			} else {
				// passing to the database
				$userData = array("first_name" => $user->getFirstName(), "last_name" => $user->getLastName(),
					"email" => $user->getEmail(), "password" => password_hash($user->getPassword(), PASSWORD_BCRYPT));
				$this->db->insert('user', $userData);

				log_message(INFO_STATUS, "User: " . $user->getEmail() . " successfully added to the database");
				$userInfo = new UserData($user->getFirstName(), $user->getEmail(), null);
				return new Response(SUCCESS_STATUS, "USER REGISTERED SUCCESSFULLY", $userInfo->toString());
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "UserModel - registerUser() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "USER REGISTRATION UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);

		}
	}

	/**
	 * @throws Exception
	 */
	// function for login the user by processing user data
	public function loginUser(string $userEmail, string $userPassword): Response
	{
		try {
			log_message(INFO_STATUS, "UserModel - loginUser(): function called ");

			$retrievedUser = $this->getUserByEmailFromUserTable($userEmail);
			if ($retrievedUser->num_rows() == 1) {

				// password verification
				if (password_verify($userPassword, $retrievedUser->row()->password)) {
					log_message(INFO_STATUS, "User: " . $userEmail . " authenticated successfully");

					$generateToken = (new jwt)->generateToken($retrievedUser->row()->id,
						$retrievedUser->row()->first_name, $retrievedUser->row()->email);

					// saving the token into database with user for token validity checking(after logout)
					$this->load->model("UserTokenModel");
					$this->UserTokenModel->saveTokenWithUserToUserTokenTable($retrievedUser->row()->id, $generateToken);

					$userInfo = new UserData($retrievedUser->row()->first_name, $retrievedUser->row()->email, $generateToken);
					return new Response(SUCCESS_STATUS, "USER AUTHENTICATED", $userInfo->toString());
				} else {
					log_message(ERROR_STATUS, "User: " . $userEmail . " password authentication unsuccessful");
					return new Response(ERROR_STATUS, "USER PASSWORD IS INCORRECT", null);
				}
			} else {
				log_message(ERROR_STATUS, "User: " . $userEmail . " email authentication unsuccessful");
				return new Response(ERROR_STATUS, "USER EMAIL IS NOT EXISTS", null);
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "UserModel - loginUser() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "USER LOGIN UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	// function for logout user
	public function logoutUser(string $headerToken): Response
	{
		try {
			log_message(INFO_STATUS, "UserModel - logoutUser(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);
			$userId = $userInToken->getId();

			if (!$userId == "") {
				$retrievedUser = $this->getUserByIdFromUserTable($userId);
				if ($retrievedUser->num_rows() == 1) {
					$this->load->model("UserTokenModel");
					$this->UserTokenModel->saveTokenWithUserToUserTokenTable($userId, "");

					return new Response(SUCCESS_STATUS, "USER LOGOUT SUCCESSFULLY", null);

				} else {
					return new Response(ERROR_STATUS, "USER NOT FOUND", null);
				}

			} else {
				return new Response(ERROR_STATUS, "USER ID NOT FOUND IN TOKEN", null);
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "UserModel - loginUser() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "USER LOGOUT UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	// function for get user from the database by email
	private function getUserByEmailFromUserTable($email): CI_DB_result
	{
		log_message(INFO_STATUS, "UserModel - getUserByEmailFromUserTable(): function called ");

		$this->db->where('email', $email);
		return $this->db->get('user');
	}

	// function for get user from the database by id
	public function getUserByIdFromUserTable($id): CI_DB_result
	{
		log_message(INFO_STATUS, "UserModel - getUserByIdFromUserTable(): function called ");

		$this->db->where('id', $id);
		return $this->db->get('user');
	}


	/**
	 * @throws Exception
	 */
	// function for check whether entered password is exits or not
	private function checkEmailIsExistsInUserTable($email): bool
	{
		log_message(INFO_STATUS, "UserModel - checkEmailIsExistsInUserTable(): function called ");

		$this->db->where('email', $email);
		return $this->db->get('user')->num_rows() == 1;
	}


	// function for encrypt password using password_hash method
	private function encryptPassword(string $password): string
	{
		log_message(INFO_STATUS, "UserModel - encryptPassword(): function called ");

		return password_hash($password, PASSWORD_DEFAULT);
	}


	// function for decrypt password using password_verify method and compares the user entered and registered password
	private function checkPasswordValidity(string $userPassword, string $retrievedUserPassword): bool
	{
		log_message(INFO_STATUS, "UserModel - checkPasswordValidity(): function called ");

		return password_verify($userPassword, $retrievedUserPassword);
	}


	/**
	 * @throws Exception
	 */
	// function to retrieve user data with submitted questions and answers
	public function getUserProfileById(string $headerToken): Response
	{
		try {
			log_message(INFO_STATUS, "UserModel - getUserProfileById(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);
			$userId = $userInToken->getId();

			if (!$userId == "") {
				$retrievedUser = $this->getUserByIdFromUserTable($userId);
				if ($retrievedUser->num_rows() == 1) {

					$this->load->model("AnswerModel");
					$retrievedAnswersList = $this->AnswerModel->getAnswersByUser($userId)->getData();

					$this->load->model("QuestionModel");
					$retrievedQuestionsList = $this->QuestionModel->getQuestionsByUserId($userId)->getData();

					$userProfileData = new UserProfileData($retrievedUser->row()->id, $retrievedUser->row()->first_name,
						$retrievedUser->row()->last_name, $retrievedUser->row()->email, $retrievedQuestionsList, $retrievedAnswersList);

					return new Response(SUCCESS_STATUS, "USER PROFILE DETAILS FOUND SUCCESSFULLY",
						$userProfileData->toString());

				} else {
					return new Response(ERROR_STATUS, "USER NOT FOUND", null);
				}

			} else {
				return new Response(ERROR_STATUS, "USER ID NOT FOUND IN TOKEN", null);
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "UserModel - loginUser() getUserProfileById: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "USER PROFILE RETRIEVAL UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}

	/**
	 * @throws Exception
	 */
	// function to retrieve user data with submitted questions and answers
	public function updateUserDetails(string $headerToken, User $user): Response
	{
		try {
			log_message(INFO_STATUS, "UserModel - updateUserDetails(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);
			$userId = $userInToken->getId();

			if (!$userId == "") {
				$retrievedUser = $this->getUserByIdFromUserTable($userId);
				if ($retrievedUser->num_rows() == 1) {

					// updates the existing record
					$data = ['first_name' => $user->getFirstName(), 'last_name' => $user->getLastName()];
					$this->db->where('id', $retrievedUser->row()->id);
					$this->db->update('user', $data);

					$retrievedUserAfterUpdate = $this->getUserByIdFromUserTable($userId);
					$userProfileData = new User($retrievedUserAfterUpdate->row()->first_name, $retrievedUserAfterUpdate->row()->last_name,
						$retrievedUserAfterUpdate->row()->email, "");

					return new Response(SUCCESS_STATUS, "USER DETAILS UPDATED SUCCESSFULLY",
						$userProfileData->toString());


				} else {
					return new Response(ERROR_STATUS, "USER NOT FOUND", null);
				}

			} else {
				return new Response(ERROR_STATUS, "USER ID NOT FOUND IN TOKEN", null);
			}


		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "UserModel - updateUserDetails() getUserProfileById: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "USER PROFILE RETRIEVAL UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}
}
