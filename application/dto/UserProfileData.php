<?php

namespace dto;
class UserProfileData
{

	private int $id;
	private string $firstName;
	private string $lastName;
	private string $email;
	private array $questions;
	private array $answers;


	public function __construct(int $id, string $firstName,string $lastName, string $email, array $questions, array $answers)
	{
		$this->id = $id;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->questions = $questions;
		$this->answers = $answers;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getLastName(): string
	{
		return $this->lastName;
	}

	public function setLastName(string $lastName): void
	{
		$this->lastName = $lastName;
	}

	public function getFirstName(): string
	{
		return $this->firstName;
	}

	public function setFirstName(string $firstName): void
	{
		$this->firstName = $firstName;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function getQuestions(): array
	{
		return $this->questions;
	}

	public function setQuestions(array $questions): void
	{
		$this->questions = $questions;
	}

	public function getAnswers(): array
	{
		return $this->answers;
	}

	public function setAnswers(array $answers): void
	{
		$this->answers = $answers;
	}

	public function toString(): array
	{
		return array("id" => $this->getId(), "firstName" => $this->getFirstName(),"lastName" => $this->getLastName(),
			"email" => $this->getEmail(), "questions" => $this->getQuestions(), "answers" => $this->getAnswers());

	}
}
