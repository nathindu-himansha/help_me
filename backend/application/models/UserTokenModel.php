<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once("application/libraries/JwtLibrary.php");
require_once("application/dto/TokenUser.php");

use Libraries\jwtLibrary as jwt;
use dto\TokenUser;

class UserTokenModel extends CI_Model
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
	public function validateRetrievedToken($headerToken): bool
	{
		log_message(INFO_STATUS, "UserTokenModel - validateRetrievedToken(): function called ");
		try {
			// splits the 'Bearer' string and token by the space(Bearer token_digits)
			$segmentedJwtToken = explode(" ", $headerToken)[1];
			$jwtTokenPayload = (new jwt)->extractDataFromTokenPayload($segmentedJwtToken);

			//checks the expiry and signature
			$isTokenValid = (new jwt)->validateJsonToken($segmentedJwtToken);

			// checks the token mapped with user
			$isTokenMappedWithUser = $this->checkUserTokenIsMapped($jwtTokenPayload->getUserId(), $segmentedJwtToken);

			log_message(INFO_STATUS, "UserTokenModel - validateRetrievedToken(): token status- isTokenValid: "
				. ($isTokenValid ? "true" : "false") . " isTokenMappedWithUser: " . ($isTokenMappedWithUser ? "true" : "false"));

			if ($isTokenValid && $isTokenMappedWithUser) {
				return true;
			} else {
				return false;
			}

		} catch (Exception $exception) {
			log_message(ERROR_STATUS, "UserTokenModel - validateRetrievedToken() Exception: " . $exception->getMessage());
			throw new Exception("UserTokenModel - validateRetrievedToken() Exception: " . $exception->getMessage());
		}
	}



	/**
	 * @throws Exception
	 */
	// function for check the status of user token(token exists or not)
	public function checkUserTokenIsMapped($userId, $receivedToken): bool
	{
		log_message(INFO_STATUS, "UserTokenModel - validateUserToken(): function called ");
		try {
			$retrievedUserMappedToken = $this->getUserByIdFromUserTokenTable($userId);

			if ($retrievedUserMappedToken->num_rows() == 1) {
				log_message(INFO_STATUS, "token fetched from the user token table: " . $retrievedUserMappedToken->row()->token);

				// compares user entered token and database saved(mapping) token
				if ($retrievedUserMappedToken->row()->token == $receivedToken) {
					return true;
				}
			}
			return false;

		} catch (Exception $exception) {
			log_message(ERROR_STATUS, "UserTokenModel - validateUserToken() Exception: " . $exception->getMessage());
			throw new Exception("UserTokenModel - validateUserToken() Exception: " . $exception->getMessage());
		}
	}


	/**
	 * @throws Exception
	 */
	// function for save generated token with user conditionals
	public function saveTokenWithUserToUserTokenTable($userId, $generatedToken)
	{
		log_message(INFO_STATUS, "UserTokenModel - saveTokenWithUserToUserTokenTable(): function called ");
		try {
			$retrievedUserTokenData = $this->getUserByIdFromUserTokenTable($userId);

			if ($retrievedUserTokenData->num_rows() == 1) {
				$userData = array("id" => $retrievedUserTokenData->row()->id, "fk_userId" => $userId, "token" => $generatedToken);
				$this->db->replace('user_token', $userData);
			} else {
				$userData = array("fk_userId" => $userId, "token" => $generatedToken);
				$this->db->insert('user_token', $userData);
			}
		} catch (Exception $exception) {
			log_message(ERROR_STATUS, "UserTokenModel - saveTokenWithUserToUserTokenTable() Exception: "
				. $exception->getMessage());
			throw new Exception("UserTokenModel - saveTokenWithUserToUserTokenTable() Exception: "
				. $exception->getMessage());
		}
	}


	/**
	 * @throws Exception
	 */
	// function for get user from the database by email
	public function getUserByIdFromUserTokenTable($userId): CI_DB_result
	{
		log_message(INFO_STATUS, "UserTokenModel - getUserByIdFromUserTokenTable(): function called ");
		try {
			$this->db->where("fk_userId", $userId);
			return $this->db->get('user_token');

		} catch (Exception $exception) {
			log_message(ERROR_STATUS, "UserTokenModel - getUserByIdFromUserTokenTable() Exception: "
				. $exception->getMessage());
			throw new Exception("UserTokenModel - getUserByIdFromUserTokenTable() Exception: "
				. $exception->getMessage());
		}
	}


	/**
	 * @throws Exception
	 */
	// function for get user from the token payload
	public function getUserByTokenPayload($token): TokenUser
	{
		log_message(INFO_STATUS, "UserTokenModel - getUserByTokenPayload(): function called ");
		try {
			// splits the 'Bearer' string and token by the space(Bearer token_digits)
			$segmentedJwtToken = explode(" ", $token)[1];
			$jwtTokenPayload = (new jwt)->extractDataFromTokenPayload($segmentedJwtToken);
			return new TokenUser($jwtTokenPayload->getUserId(), $jwtTokenPayload->getUserName(), $jwtTokenPayload->getUserEmail());

		} catch (Exception $exception) {
			log_message(ERROR_STATUS, "UserTokenModel - getUserByTokenPayload() Exception: " . $exception->getMessage());
			throw new Exception("UserTokenModel - getUserByTokenPayload() Exception: " . $exception->getMessage());
		}
	}
}
