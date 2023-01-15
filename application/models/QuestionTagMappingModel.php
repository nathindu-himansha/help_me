<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("application/entities/QuestionTagMapping.php");
require_once("application/dto/Response.php");

use entities\QuestionTagMapping;
use dto\Response;


class QuestionTagMappingModel extends CI_Model
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
	// get for create question-tag mappings
	public function getQuestionTagMappings(int $questionId): Response
	{
		try {
			log_message(INFO_STATUS, "QuestionTagMappingModel - getQuestionTagMappings(): function called ");

			$this->db->where('fk_question_id', $questionId);
			$this->db->distinct();
			$retrievedTagMappings = $this->db->get('question_tag_mapping');

			$mappedTags = array();
			foreach ($retrievedTagMappings->result() as $retrievedTagMapping) {
				$mappedTags[] = new QuestionTagMapping($retrievedTagMapping->id, $retrievedTagMapping->fk_question_id
					, $retrievedTagMapping->fk_tag_id);
			}
			return new Response(SUCCESS_STATUS, "TAG MAPPINGS RETRIEVED SUCCESSFULLY", $mappedTags);


		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "QuestionTagMappingModel - getQuestionTagMappings() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "TAG MAPPINGS RETRIEVED UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}
}
