<?php

namespace dto;
class TokenUser
{

	private int $id;
	private string $firstName;
	private string $email;

	public function __construct(int $id, string $firstName, string $email)
	{
		$this->id = $id;
		$this->firstName = $firstName;
		$this->email = $email;
	}


	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
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


	public function toString(): array
	{
		return array("id" => $this->getId(), "email" => $this->getEmail(), "firstName" => $this->getFirstName());

	}


}
