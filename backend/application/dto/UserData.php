<?php

namespace dto;
class UserData
{

	private string $firstName;
	private string $email;
	public ?string $token;

	public function __construct(string $firstName, string $email, ?string $token)
	{
		$this->firstName = $firstName;
		$this->email = $email;
		$this->token = $token;
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

	public function getToken(): ?string
	{
		return $this->token;
	}


	public function setToken(?string $token): void
	{
		$this->token = $token;
	}


	public function toString(): array
	{
		return array("email" => $this->getEmail(), "firstName" => $this->getFirstName(),"token"=>$this->getToken());

	}


}
