<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("application/entities/Question.php");
require_once("application/dto/Response.php");
require_once("application/dto/QuestionData.php");
require_once("application/dto/AnswerData.php");

use entities\Question;
use dto\Response;
use dto\QuestionData;
use dto\AnswerData;


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
	public function createQuestion(string $headerToken, string $title, string $question, string $tags): Response
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
			$question = $this->getQuestionDataById(intval($question_id))->getData();

			// seperated tags by comma
			$entered_tags = explode(',', $tags);

			// inserting data into question tag mapping table
			$tagList = array();
			foreach ($entered_tags as $tagName) {

				// retrieving the tag from the id from database for fk mapping
				$this->load->model('TagModel');

				if (!($this->TagModel->checkTagIsExists($tagName))) {
					$retrievedTag = $this->TagModel->createTag($tagName)->getData()[0];
				} else {
					$retrievedTag = $this->TagModel->getTagByName($tagName);
				}
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
	// function for update question
	public function updateQuestion(string $headerToken, int $questionId, string $updatedTitle, string $updatedQuestion, string $updatedTags): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - updateQuestion(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);

			// marking as transaction query to avoid error inserts (if some db query failed other will not save)
			$this->db->trans_start();

			$question = $this->getQuestionById($questionId)->getData()[0];
			if ($question) {
				if ($question['user'] == $userInToken->getId()) {

					// updates the existing record (question)
					$data = ['question_title' => $updatedTitle, 'question' => $updatedQuestion];
					$this->db->where('id', $questionId);
					$this->db->update('user_question', $data);

					//tags updating
					$this->load->model('TagModel');
					$this->load->model('QuestionTagMappingModel');

					$questionTagMappings = $this->QuestionTagMappingModel->getQuestionTagMappings($questionId)->getData();
					foreach ($questionTagMappings as $questionTagMapping) {

						// deleting existing tags
						$this->db->where('id', $questionTagMapping->getId());
						$this->db->delete('question_tag_mapping');
					}

					// inserting tags
					$entered_updated_tags = explode(',', $updatedTags);
					foreach ($entered_updated_tags as $tagName) {
						if (!($this->TagModel->checkTagIsExists(strtolower($tagName)))) {
							$retrievedTag = $this->TagModel->createTag(strtolower($tagName))->getData()[0];
						} else {
							$retrievedTag = $this->TagModel->getTagByName(strtolower($tagName));
						}

						// inserting data into table with rag and question references
						$questionTagMappingData = array("fk_question_id" => $questionId, "fk_tag_id" => $retrievedTag->getId());
						$this->db->insert('question_tag_mapping', $questionTagMappingData);
					}

					$this->db->trans_complete();
					return new Response(SUCCESS_STATUS, "QUESTION UPDATED SUCCESSFULLY",
						array("question" => $question, "tags" => $entered_updated_tags));

				} else {
					return new Response(ERROR_STATUS, "NOT ALLOWED TO UPDATE", null);
				}
			} else {
				return new Response(ERROR_STATUS, "QUESTION NOT EXISTS", null);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - updateQuestion() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "QUESTION UPDATE UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	// function for delete question
	public function deleteQuestion(string $headerToken, int $questionId): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - deleteQuestion(): function called ");

			// retrieving the user from the token
			$this->load->model('UserTokenModel');
			$userInToken = $this->UserTokenModel->getUserByTokenPayload($headerToken);

			$question = $this->getQuestionById($questionId)->getData()[0];
			if ($question) {
				if ($question['user'] == $userInToken->getId()) {

					// deleting answers related to question
					$this->db->where('fk_user_question_id', $questionId);
					$this->db->delete('user_answer');

					// deletes tag mappings
					$this->load->model('QuestionTagMappingModel');
					$questionTagMappings = $this->QuestionTagMappingModel->getQuestionTagMappings($questionId)->getData();
					foreach ($questionTagMappings as $questionTagMapping) {

						// deleting existing tags
						$this->db->where('id', $questionTagMapping->getId());
						$this->db->delete('question_tag_mapping');
					}

					// deleting question
					$this->db->where('id', $questionId);
					$this->db->delete('user_question');

					return new Response(SUCCESS_STATUS, "QUESTION DELETED SUCCESSFUL", null);
				} else {
					return new Response(ERROR_STATUS, "NOT ALLOWED TO DELETE QUESTION", null);
				}
			} else {
				return new Response(ERROR_STATUS, "QUESTION NOT EXISTS", null);
			}
		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - deleteQuestion() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "QUESTION DELETE UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	// function for search question
	public function getQuestionsByString(string $string): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - getQuestionsByString(): function called ");

			$this->db->like('question_title', $string);
			$retrievedQuestions = $this->db->get('user_question');

			return new Response(SUCCESS_STATUS, "QUESTIONS RETRIEVED SUCCESSFULLY", $retrievedQuestions->result());

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionModel - getQuestionsByString() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "QUESTION DELETE UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
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
				$retrievedQuestion->row()->question, $retrievedQuestion->row()->votes,
				$retrievedQuestion->row()->fk_user_id, $retrievedQuestion->row()->timestamp);

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
	// function for retrieve questions by id
	public function getQuestionDataById(int $questionId): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionModel - getQuestionById(): function called ");

			$this->db->where('id', $questionId);
			$retrievedQuestion = $this->db->get('user_question');

			$this->load->model('UserModel');
			$questionUser = $this->UserModel->getUserByIdFromUserTable($retrievedQuestion->row()->fk_user_id);

			$question = new QuestionData($retrievedQuestion->row()->id, $retrievedQuestion->row()->question_title,
				$retrievedQuestion->row()->question, $retrievedQuestion->row()->votes,
				$questionUser->row()->first_name, $retrievedQuestion->row()->timestamp);

			$answersDataList = array();
			$tagList = array();
			if ($question->getId() != null) {

				// answers
				$this->load->model('AnswerModel');
				$answersList = $this->AnswerModel->getAnswersByQuestionId($question->getId())->getData();

				foreach ($answersList as $answer) {

					$answeredUser = $this->UserModel->getUserByIdFromUserTable($answer->fk_user_id);
					$answersData = new AnswerData($answer->id, $answer->answer, $answer->fk_user_question_id, $answer->fk_user_id,
						$answeredUser->row()->first_name, $answer->timestamp);
					$answersDataList[] = $answersData->toString();
				}

				//tags
				$this->load->model('QuestionTagMappingModel');
				$tagQuestionMappings = $this->QuestionTagMappingModel->getQuestionTagMappings($question->getId())->getData();

				$this->load->model('TagModel');
				foreach ($tagQuestionMappings as $tagMapping) {
					$tagList[] = $this->TagModel->getTagById($tagMapping->getFkTagId())->toString();
				}
			}

			return new Response(SUCCESS_STATUS, "QUESTION RETRIEVED SUCCESSFULLY",
				array("question" => $question->toString(), "answers" => $answersDataList, "tags" => $tagList));

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

			log_message(INFO_STATUS, "555QuestionModel - voteQuestion(): function called" . $questionId . "||" . $isUpVote);

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
