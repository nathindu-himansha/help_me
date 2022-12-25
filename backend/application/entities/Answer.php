<?php

namespace entities;
class Answer
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private int $id;
	private string $answer;
	private string $fkUserQuestionId;
	private int $fkUserId;


	public function __construct(int $id, string $answer, string $fkUserQuestionId, int $fkUserId)
	{
		$this->id = $id;
		$this->answer = $answer;
		$this->fkUserQuestionId = $fkUserQuestionId;
		$this->fkUserId = $fkUserId;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getAnswer(): string
	{
		return $this->answer;
	}

	public function setAnswer(string $answer): void
	{
		$this->answer = $answer;
	}

	public function getFkUserQuestionId(): string
	{
		return $this->fkUserQuestionId;
	}

	public function setFkUserQuestionId(string $fkUserQuestionId): void
	{
		$this->fkUserQuestionId = $fkUserQuestionId;
	}

	public function getFkUserId(): int
	{
		return $this->fkUserId;
	}

	public function setFkUserId(int $fkUserId): void
	{
		$this->fkUserId = $fkUserId;
	}

	public function toString(): array
	{
		return array("id" => $this->getId(), "answer" => $this->getAnswer(), "questionId" => $this->getFkUserQuestionId(),
			 "userId" => $this->getFkUserId());
	}

}
