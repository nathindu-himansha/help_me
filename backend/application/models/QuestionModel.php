<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("application/entities/Question.php");
require_once("application/dto/Response.php");

use entities\Question;
use dto\Response;


class QuestionModel extends CI_Model
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
	// function for create question, insert into table and create question tag mappings
	public function createQuestion(string $headerToken, string $title, string $question, array $tags): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - createQuestion(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);

			// marking as transaction query to avoid error inserts (if some db query failed other will not save)
			$this->db->trans_start();

			// inserting data into question table
			$questionData = array("question_title" => strtolower($title), "question" => $question, "fk_user_id" => $userInToken->getId());
			$this->db->insert('user_question', $questionData);
			log_message(INFO_STATUS, "QuestionModel - createQuestion(): Question saved ");

			// retrieving the insert question for further use
			$question_id = $this->db->insert_id();
			$question = $this->getQuestionById(intval($question_id))->getData()[0];

			// inserting data into question tag mapping table
			$tagList = array();
			foreach ($tags as $tagId) {

				// retrieving the tag from the id from database for fk mapping
				$this->load->model('TagModel');
				$retrievedTag = $this->TagModel->getTagById(intval($tagId));
				$tagList[] = $retrievedTag->toString();

				// inserting data into table with rag and question references
				$questionTagMappingData = array("fk_question_id" => $question_id, "fk_tag_id" => $retrievedTag->getId());
				$this->db->insert('question_tag_mapping', $questionTagMappingData);
			}
			log_message(INFO_STATUS, "QuestionModel - createQuestion(): Question-Tag mappings saved ");
			$this->db->trans_complete();

			return new Response(SUCCESS_STATUS, "QUESTION INSERTED SUCCESSFULLY", array("question" => $question, "tags" => $tagList));

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - createQuestion() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "QUESTION INSERTED UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}



	/**
	 * @throws Exception
	 */
	// function for retrieve questions by id
	public function getQuestionById(int $questionId): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - getQuestionById(): function called ");

			$this->db->where('id', $questionId);
			$retrievedQuestion = $this->db->get('user_question');
			$question = new Question($retrievedQuestion->row()->id, $retrievedQuestion->row()->question_title,
				$retrievedQuestion->row()->question, $retrievedQuestion->row()->votes, $retrievedQuestion->row()->fk_user_id);

			return new Response(SUCCESS_STATUS, "QUESTION RETRIEVED SUCCESSFULLY", array($question->toString()));

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - getQuestionById() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "QUESTION RETRIEVED UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}

	/**
	 * @throws Exception
	 */
	// function for retrieve all questions
	public function getAllQuestions(): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - getAllQuestions(): function called ");

			$retrievedQuestions = $this->db->get('user_question');

			$questionList = array();
			foreach ($retrievedQuestions->result() as $question) {
				$questionList[] = $question;
			}

			return new Response(SUCCESS_STATUS, "QUESTION RETRIEVED SUCCESSFULLY", $questionList);

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - getAllQuestions() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "QUESTION RETRIEVED UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}

	/**
	 * @throws Exception
	 */
	// function for retrieve all questions by user id
	public function getQuestionsByUserId(int $userId): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - getQuestionsByUserId(): function called ");

			$this->db->where('fk_user_id', $userId);
			$retrievedQuestionList = $this->db->get('user_question');

			$retrievedQuestionsList = array();
			foreach ($retrievedQuestionList->result() as $retrievedQuestion) {
				$retrievedQuestionsList[] = $retrievedQuestion;
			}

			return new Response(SUCCESS_STATUS, "QUESTION RETRIEVED SUCCESSFULLY", $retrievedQuestionsList);

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - getQuestionsByUserId() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "QUESTION RETRIEVED UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	// function to get most answered questions
	public function getTrendingQuestions(): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - getTrendingQuestions(): function called ");

			// loads the answer model and function to retrieve list
			$this->load->model("AnswerModel");
			$retrievedQuestionListResponse = $this->AnswerModel->getMostAnsweredQuestionList();

			if ($retrievedQuestionListResponse->getStatus() == SUCCESS_STATUS) {
				$retrievedQuestionIdList = $retrievedQuestionListResponse->getData();

				// iterates the received question ids and retrieve the questions
				$retrievedQuestionList = array();
				foreach ($retrievedQuestionIdList as $questionId) {

					$retrievedQuestion = $this->getQuestionById($questionId->fk_user_question_id);
					if ($retrievedQuestion->getStatus() == SUCCESS_STATUS) {
						$retrievedQuestionList[] = $retrievedQuestion->getData()[0];
					}
				}

				return new Response(SUCCESS_STATUS, "QUESTION LIST RETRIEVED SUCCESSFUL", $retrievedQuestionList);

			} else {
				return new Response(ERROR_STATUS, "QUESTION LIST RETRIEVED UNSUCCESSFUL: "
					. $retrievedQuestionListResponse->getMessage, null);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - getTrendingQuestions() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "QUESTION RETRIEVED UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	// function to vote question
	public function voteQuestion(int $questionId, bool $isUpVote): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - voteQuestion(): function called ");

			// retrieving the question from the table by id
			$retrievedQuestionResponse = $this->getQuestionById($questionId);

			// if question is retrieved and not null answer will save into the table
			if ($retrievedQuestionResponse->getStatus() == SUCCESS_STATUS) {

				if ($retrievedQuestionResponse->getData()[0]) {

					// checks the user input and adding/subtracting
					$voteCount = (int)$retrievedQuestionResponse->getData()[0]['votes'];
					if ($isUpVote) {
						$voteCount += 1;
					} else {
						$voteCount -= 1;
					}

					// updates the existing record
					$data = ['votes' => $voteCount,];
					$this->db->where('id', $retrievedQuestionResponse->getData()[0]['id']);
					$this->db->update('user_question', $data);

					$retrievedQuestionAfterUpdate = $this->getQuestionById($questionId);

					return new Response(SUCCESS_STATUS, "VOTE UPDATED SUCCESSFULLY",
						$retrievedQuestionAfterUpdate->getData()[0]);

				} else {
					return new Response(ERROR_STATUS, "RETRIEVED QUESTION IS NULL ", null);
				}

			} else {
				return new Response(ERROR_STATUS, "RETRIEVED QUESTION ERROR", null);
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - voteQuestion() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "VOTE UPDATED UNSUCCESSFUL : EXCEPTION - "
				. $exception->getMessage(), null);
		}
	}
}
