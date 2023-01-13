<?php

namespace Libraries;
require_once("application/dto/JwtToken.php");

use dto\JwtToken;
use Exception;

defined('BASEPATH') or exit('No direct script access allowed');

class JwtLibrary
{
	const JWT_SECRET_KEY = "AsswdCwHelpMeSecret";
	const JWT_TOKEN_VALIDITY = 60*60*60; // todo reduce this time at the end
	const JWT_ALGORITHM_TYPE = "HS256";

	/**
	 * @throws Exception
	 */
	// generates a JWT token on request by binding user details
	public function generateToken($userId, $userFirstName, $userEmail): string
	{
		log_message('info', "jwtLibrary - generateToken(): function called ");
		try {
			$tokenIssuedAt = time();
			$tokenExpirationTime = $tokenIssuedAt + self::JWT_TOKEN_VALIDITY;

			$encodedHeaders = $this->encodeToBase64Url(json_encode(array('alg' => self::JWT_ALGORITHM_TYPE, 'typ' => 'JWT')));
			$encodedPayload = $this->encodeToBase64Url(json_encode(array("user" => array("id" => $userId, "fName" => $userFirstName,
				"email" => $userEmail), 'iat' => $tokenIssuedAt, 'exp' => $tokenExpirationTime)));

			$encodedSignature = $this->createEncodedTokenSignature($encodedHeaders, $encodedPayload);

			return $encodedHeaders . "." . $encodedPayload . "." . $encodedSignature;

		} catch (Exception $exception) {
			log_message('error', "jwtLibrary - generateToken() Exception: " . $exception->getMessage());
			throw new Exception("JWT TOKEN GENERATION EXCEPTION " . $exception->getMessage());
		}
	}


	/**
	 * @throws Exception
	 */
	// validates the received token by checking expiry time and signature
	public function validateJsonToken($headerToken): bool
	{
		log_message('info', "jwtLibrary - validateJsonToken(): function called ");

		try {
			// splits the JWT token by the . symbol to segment header,payload and signature
			$tokenUnits = explode('.', $headerToken);
			$tokenHeader = base64_decode($tokenUnits[0]);
			$tokenPayload = base64_decode($tokenUnits[1]);
			$tokenSignature = $tokenUnits[2];

			// checks the token validity by the expiration time
			$definedExpirationTime = json_decode($tokenPayload)->exp;
			$is_token_expired = ($definedExpirationTime - time()) < 0;

			// rebuilding the method signature by adding the secret key to check weather the received signature is valid
			$encodedHeaders = $this->encodeToBase64Url($tokenHeader);
			$encodedPayload = $this->encodeToBase64Url($tokenPayload);
			$encodedSignature = $this->createEncodedTokenSignature($encodedHeaders, $encodedPayload);

			// verify weather the received token signature and inbuilt signatures are valid or not
			$is_signature_valid = ($encodedSignature == $tokenSignature);

			log_message('info', "jwtLibrary - validateJsonToken(): token status user:"
				. json_decode($tokenPayload)->user->id . " expired: " . ($is_token_expired ? "true" : "false") . " valid: "
				. ($is_signature_valid ? "true" : "false"));
			if (!$is_token_expired && $is_signature_valid) {
				return true;
			} else {
				return false;
			}

		} catch (Exception $exception) {
			log_message('error', "jwtLibrary - validateJsonToken() Exception: " . $exception->getMessage());
			throw new Exception("JWT TOKEN VALIDATION EXCEPTION " . $exception->getMessage());
		}
	}


	/**
	 * @throws Exception
	 */
	public function extractDataFromTokenPayload($headerToken): JwtToken
	{
		log_message('info', "jwtLibrary - extractDataFromTokenPayload(): function called ");
		try {
			// splits the JWT token by the . symbol to segment header,payload and signature
			$tokenUnits = explode('.', $headerToken);
			$tokenPayload = base64_decode($tokenUnits[1]);

			return new JwtToken(json_decode($tokenPayload)->user->id, json_decode($tokenPayload)->user->fName,
				json_decode($tokenPayload)->user->email, json_decode($tokenPayload)->iat, json_decode($tokenPayload)->exp);

		} catch (Exception $exception) {
			log_message('error', "jwtLibrary - extractDataFromTokenPayload() Exception: " . $exception->getMessage());
			throw new Exception("JWT TOKEN DATA EXTRACTION EXCEPTION " . $exception->getMessage());
		}
	}


	// function to create encoded token signature by binding user data and token data
	private function createEncodedTokenSignature($encodedHeaders, $encodedPayload): string
	{
		log_message('info', "jwtLibrary - createEncodedTokenSignature(): function called ");

		$tokenSignature = hash_hmac('SHA256', $encodedHeaders . "." . $encodedPayload, self::JWT_SECRET_KEY, true);
		return $this->encodeToBase64Url($tokenSignature);
	}


	// function to convert string into base64 based string
	private function encodeToBase64Url($string): string
	{
		log_message('info', "jwtLibrary - encodeToBase64Url(): function called ");

		return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
	}


}
