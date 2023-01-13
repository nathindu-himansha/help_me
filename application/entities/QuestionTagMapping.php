<?php

namespace entities;
class QuestionTagMapping
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private int $id;
	private int $fk_question_id;
	private int $fk_tag_id;


	public function __construct(int $id, int $fk_question_id, int $fk_tag_id)
	{
		$this->id = $id;
		$this->fk_question_id = $fk_question_id;
		$this->fk_tag_id = $fk_tag_id;
	}


	public function getId(): int
	{
		return $this->id;
	}


	public function setId(int $id): void
	{
		$this->id = $id;
	}


	public function getFkQuestionId(): int
	{
		return $this->fk_question_id;
	}


	public function setFkQuestionId(int $fk_question_id): void
	{
		$this->fk_question_id = $fk_question_id;
	}


	public function getFkTagId(): int
	{
		return $this->fk_tag_id;
	}


	public function setFkTagId(int $fk_tag_id): void
	{
		$this->fk_tag_id = $fk_tag_id;
	}

	public function toString(): array
	{
		return array("id" => $this->getId(), "questionId" => $this->getFkQuestionId(), "tagId" => $this->getFkTagId());
	}


}
