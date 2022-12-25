<?php

namespace entities;
class Question
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private int $id;
	private string $questionTitle;
	private string $question;
	private int $votes;
	private int $fkUserId;


	public function __construct(int $id, string $questionTitle, string $question, int $votes, int $fkUserId)
	{
		$this->id = $id;
		$this->questionTitle = $questionTitle;
		$this->question = $question;
		$this->votes = $votes;
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


	public function getQuestionTitle(): string
	{
		return $this->questionTitle;
	}


	public function setQuestionTitle(string $questionTitle): void
	{
		$this->questionTitle = $questionTitle;
	}


	public function getQuestion(): string
	{
		return $this->question;
	}

	public function setQuestion(string $question): void
	{
		$this->question = $question;
	}


	public function getVotes(): int
	{
		return $this->votes;
	}


	public function setVotes(int $votes): void
	{
		$this->votes = $votes;
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
		return array("id" => $this->getId(), "title" => $this->getQuestionTitle(), "question" => $this->getQuestion(),
			"votes" => $this->getVotes(), "user" => $this->getFkUserId());
	}


}
