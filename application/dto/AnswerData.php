<?php

namespace dto;
class AnswerData
{

	private int $id;
	private string $answer;
	private int $fkQuestionId;
	private int $fkUserId;
	private string $fkUserFirstName;
	private string $timestamp;


	public function __construct(int $id, string $answer, int $fkQuestionId, int $fkUserId, string $fkUserFirstName, string $timestamp)
	{
		$this->id = $id;
		$this->answer = $answer;
		$this->fkQuestionId = $fkQuestionId;
		$this->fkUserId = $fkUserId;
		$this->fkUserFirstName = $fkUserFirstName;
		$this->timestamp = $timestamp;
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


	public function getFkQuestionId(): int
	{
		return $this->fkQuestionId;
	}


	public function setFkQuestionId(int $fkQuestionId): void
	{
		$this->fkQuestionId = $fkQuestionId;
	}


	public function getFkUserId(): int
	{
		return $this->fkUserId;
	}


	public function setFkUserId(int $fkUserId): void
	{
		$this->fkUserId = $fkUserId;
	}


	public function getFkUserFirstName(): string
	{
		return $this->fkUserFirstName;
	}


	public function setFkUserFirstName(string $fkUserFirstName): void
	{
		$this->fkUserFirstName = $fkUserFirstName;
	}


	public function getTimestamp(): string
	{
		return $this->timestamp;
	}


	public function setTimestamp(string $timestamp): void
	{
		$this->timestamp = $timestamp;
	}

	public function toString(): array
	{
		return array("id" => $this->getId(), "answer" => $this->getAnswer(),"fkQuestionId" => $this->getFkQuestionId(),
			"fkUserId" => $this->getFkUserId(), "fkUserFirstName" => $this->getFkUserFirstName(), "timestamp" => $this->getTimestamp());

	}
}
