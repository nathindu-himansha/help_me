<?php

namespace dto;
class QuestionData
{

	private int $id;
	private string $questionTitle;
	private string $question;
	private int $votes;
	private string $fkUserFirstName;
	private string $dateTime;


	public function __construct(int $id, string $questionTitle, string $question, int $votes, string $fkUserFirstName,string $dateTime)
	{
		$this->id = $id;
		$this->questionTitle = $questionTitle;
		$this->question = $question;
		$this->votes = $votes;
		$this->fkUserFirstName = $fkUserFirstName;
		$this->dateTime=$dateTime;
	}


	public function getDateTime(): string
	{
		return $this->dateTime;
	}


	public function setDateTime(string $dateTime): void
	{
		$this->dateTime = $dateTime;
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


	public function getFkUserFirstName(): string
	{
		return $this->fkUserFirstName;
	}


	public function setFkUserFirstName(string $fkUserFirstName): void
	{
		$this->fkUserFirstName = $fkUserFirstName;
	}


	public function toString(): array
	{
		return array("id" => $this->getId(), "title" => $this->getQuestionTitle(), "question" => $this->getQuestion(),
			"votes" => $this->getVotes(), "user_fName" => $this->getFkUserFirstName(),"datetime"=>$this->dateTime);
	}


}
