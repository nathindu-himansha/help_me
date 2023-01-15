<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("application/entities/Answer.php");
require_once("application/dto/Response.php");

use entities\Answer;
use dto\Response;


class AnswerModel extends CI_Model
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
	// function for create answers to the question, insert into table and create question mappings
	public function createAnswer(string $headerToken, int $questionId, string $answer): Response
	{
		try {
			log_message(INFO_STATUS, "AnswerModel - createAnswer(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);

			// retrieving the question from the table by id
			$this->load->model('QuestionModel');
			$retrievedQuestionResponse = $this->QuestionModel->getQuestionById($questionId);

			// if question is retrieved and not null answer will save into the table
			if ($retrievedQuestionResponse->getStatus() == SUCCESS_STATUS) {
				if ($retrievedQuestionResponse->getData()[0]) {

					// inserting data into answer table
					$answerData = array("answer" => $answer, "fk_user_question_id" => $retrievedQuestionResponse->getData()[0]['id'],
						"fk_user_id" => $userInToken->getId());
					$this->db->insert('user_answer', $answerData);
					log_message(INFO_STATUS, "AnswerModel - createAnswer(): Answer saved ");

					// retrieving the saved answer
					$answer_id = $this->db->insert_id();
					$answer = $this->getAnswerById(intval($answer_id))->getData()[0];

					return new Response(SUCCESS_STATUS, "ANSWER INSERTED SUCCESSFUL", $answer);
				} else {
					return new Response(ERROR_STATUS, "RETRIEVED QUESTION IS NULL ", null);
				}

			} else {
				return new Response(ERROR_STATUS, "RETRIEVED QUESTION ERROR", null);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AnswerModel - createAnswer() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "ANSWER INSERTED UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}

	/**
	 * @throws Exception
	 */
	// function for retrieve questions by id
	public function updateAnswer(string $headerToken, int $answerId, string $updatedAnswer): Response
	{
		try {
			log_message(INFO_STATUS, "AnswerModel - updateAnswer(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);

			$answer = $this->getAnswerById($answerId)->getData()[0];
			if ($answer) {
				if ($answer['userId'] == $userInToken->getId()) {

					// updates the existing record (question)
					$data = ['answer' => $updatedAnswer];
					$this->db->where('id', $answerId);
					$this->db->update('user_answer', $data);

					$updated_answer = $this->getAnswerById($answerId)->getData()[0];

					return new Response(SUCCESS_STATUS, "ANSWER UPDATED SUCCESSFULLY", $updated_answer);

				} else {
					return new Response(ERROR_STATUS, "NOT ALLOWED TO UPDATE", null);
				}
			} else {
				return new Response(ERROR_STATUS, "ANSWER NOT EXISTS", null);
			}


		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AnswerModel - updateAnswer() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "ANSWER UPDATE UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	// function for retrieve questions by id
	public function deleteAnswer(string $headerToken, int $answerId): Response
	{
		try {
			log_message(INFO_STATUS, "AnswerModel - deleteAnswer(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);

			$answer = $this->getAnswerById($answerId)->getData()[0];
			if ($answer) {
				if ($answer['userId'] == $userInToken->getId()) {

					// deleting answer
					$this->db->where('id', $answerId);
					$this->db->delete('user_answer');

					return new Response(SUCCESS_STATUS, "ANSWER DELETED SUCCESSFULLY", null);

				} else {
					return new Response(ERROR_STATUS, "NOT ALLOWED TO UPDATE", null);
				}
			} else {
				return new Response(ERROR_STATUS, "ANSWER NOT EXISTS", null);
			}


		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AnswerModel - deleteAnswer() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "ANSWER DELETE UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}

	/**
	 * @throws Exception
	 */
	// function for retrieve questions by id
	public function getAnswerWithQuestionById(int $answerId): Response
	{
		try {
			log_message(INFO_STATUS, "AnswerModel - getAnswerWithQuestionById(): function called ");

			$answer = $this->getAnswerById($answerId)->getData()[0];

			$this->load->model('QuestionModel');
			$answeredQuestionData = $this->QuestionModel->getQuestionById($answer['questionId'])->getData()[0];

			return new Response(SUCCESS_STATUS, "ANSWER DATA RETRIEVED SUCCESSFULLY",
				array("answer" => $answer, "question" => $answeredQuestionData));

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AnswerModel - getAnswerWithQuestionById() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "ANSWER RETRIEVED UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}



	/**
	 * @throws Exception
	 */
	// function for retrieve questions by id
	public function getAnswerById(int $answerId): Response
	{
		try {
			log_message(INFO_STATUS, "AnswerModel - getAnswerById(): function called ");

			$this->db->where('id', $answerId);
			$retrievedAnswer = $this->db->get('user_answer');
			$answer = new Answer($retrievedAnswer->row()->id, $retrievedAnswer->row()->answer,
				$retrievedAnswer->row()->fk_user_question_id, $retrievedAnswer->row()->fk_user_id);

			return new Response(SUCCESS_STATUS, "ANSWER RETRIEVED SUCCESSFULLY", array($answer->toString()));

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AnswerModel - getAnswerById() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "ANSWER RETRIEVED UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	// function for retrieve questions by id
	public function getAnswersByUser(int $userId): Response
	{
		try {
			log_message(INFO_STATUS, "AnswerModel - getAnswersByUser(): function called ");

			$this->db->where('fk_user_id', $userId);
			$retrievedAnswers = $this->db->get('user_answer');

			$retrievedAnswersList = array();
			foreach ($retrievedAnswers->result() as $retrievedAnswer) {
				$retrievedAnswersList[] = $retrievedAnswer;
			}

			return new Response(SUCCESS_STATUS, "ANSWERS RETRIEVAL SUCCESSFUL", $retrievedAnswersList);

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AnswerModel - getAnswersByUser() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "ANSWERS RETRIEVAL UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);

		}
	}

	/**
	 * @throws Exception
	 */
	// function for retrieve questions by id
	public function getAnswersByQuestionId(int $questionId): Response
	{
		try {
			log_message(INFO_STATUS, "AnswerModel - getAnswersByQuestionId(): function called ");
			$this->db->where('fk_user_question_id', $questionId);
			$retrievedAnswers = $this->db->get('user_answer');

			$retrievedAnswersList = array();
			foreach ($retrievedAnswers->result() as $retrievedAnswer) {
				$retrievedAnswersList[] = $retrievedAnswer;
			}

			return new Response(SUCCESS_STATUS, "ANSWERS RETRIEVAL SUCCESSFUL", $retrievedAnswersList);

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AnswerModel - getAnswersByQuestionId() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "ANSWERS RETRIEVAL UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);

		}
	}


	/**
	 * @throws Exception
	 */
	// function to get question IDs of most answered
	public function getMostAnsweredQuestionList(): Response
	{
		try {
			log_message(INFO_STATUS, "AnswerModel - getMostAnsweredQuestionList(): function called ");

			$this->db->limit(10); // limits to first 10
			$this->db->select("fk_user_question_id, count(*) AS count", false);
			$this->db->group_by("fk_user_question_id");
			$this->db->order_by("count", "DESC");
			$query = $this->db->get('user_answer');

			return new Response(SUCCESS_STATUS, "MOST ANSWERED ANSWERS RETRIEVAL SUCCESSFUL", $query->result());

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "AnswerModel - getMostAnsweredQuestionList() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "ANSWERS RETRIEVAL UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}
}

