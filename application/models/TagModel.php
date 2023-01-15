<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("application/entities/Tag.php");
require_once("application/dto/Response.php");

use entities\Tag;
use dto\Response;


class TagModel extends CI_Model
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
	// function for create question related tags
	public function createTag(string $tag): Response
	{
		try {
			log_message(INFO_STATUS, "TagModel - createTag(): function called ");

			if ($this->checkTagIsExists($tag)) {
				log_message(ERROR_STATUS, "TagModel - createTag(): already exits entered tag");
				return new Response(ERROR_STATUS, "ENTERED TAG ALREADY EXISTS ", null);
			} else {
				// passing to the database
				$tagData = array("tag" => strtolower($tag));
				$this->db->insert('tag', $tagData);

				// retrieving the saved tag
				$tag_id = $this->db->insert_id();
				$tag = $this->getTagById(intval($tag_id));

				log_message(INFO_STATUS, "Tag: " . $tag->getTag() . " successfully added to the database");
				return new Response(SUCCESS_STATUS, "TAG INSERTED SUCCESSFULLY", array($tag));
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "TagModel - createTag() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "TAG INSERTED UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}


	/**
	 * @throws Exception
	 */
	public function getTagByName(string $tagName): Tag
	{
		try {
			log_message(INFO_STATUS, "TagModel - getTagByName(): function called ");

			$this->db->where('tag', $tagName);
			$retrievedTag = $this->db->get('tag');

			return new Tag($retrievedTag->row()->id, $retrievedTag->row()->tag);

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "TagModel - getTagByName() Exception: " . $exception->getMessage());
			throw new Exception("TagModel - getTagByName() Exception: " . $exception->getMessage());
		}
	}

	/**
	 * @throws Exception
	 */
	public function getTagById(int $tagId): Tag
	{
		try {
			log_message(INFO_STATUS, "TagModel - getTagById(): function called ");

			$this->db->where('id', $tagId);
			$retrievedTag = $this->db->get('tag');
			return new Tag($retrievedTag->row()->id, $retrievedTag->row()->tag);

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "TagModel - getTagById() Exception: " . $exception->getMessage());
			throw new Exception("TagModel - getTagById() Exception: " . $exception->getMessage());
		}
	}


	/**
	 * @throws Exception
	 */
	public function getAllTags(): Response
	{
		try {
			log_message(INFO_STATUS, "TagModel - getAllTags(): function called ");

			$retrievedTags = $this->db->get('tag');
			if ($retrievedTags->num_rows() > 0) {
				return new Response(SUCCESS_STATUS, "ALL TAGS RETRIEVED SUCCESSFULLY", $retrievedTags->result());
			} else {
				return new Response(ERROR_STATUS, "NO TAGS RETRIEVED SUCCESSFULLY", null);
			}

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "TagModel - getTagByName() Exception: " . $exception->getMessage());
			return new Response(ERROR_STATUS, "TAG RETRIEVED UNSUCCESSFUL : EXCEPTION - " . $exception->getMessage(), null);
		}
	}




	/**
	 * @throws Exception
	 */
	// function for check whether entered tag is exits or not
	public function checkTagIsExists($tag): bool
	{
		try {
			log_message(INFO_STATUS, "TagModel - checkTagIsExists(): function called ");

			$this->db->where('tag', strtolower($tag));
			return $this->db->get('tag')->num_rows() == 1;

		} catch (Throwable $exception) {
			log_message(ERROR_STATUS, "TagModel - checkTagIsExists() Exception: " . $exception->getMessage());
			throw new Exception("TagModel - checkTagIsExists() Exception: " . $exception->getMessage());
		}
	}
}
