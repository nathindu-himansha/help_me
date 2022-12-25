<?php

namespace dto;
class JwtToken
{

	private int $userId;
	private string $userName;
	private string $userEmail;
	private string $issuedAt;
	private string $expiry;

	public function __construct(int $userId, string $userName, string $userEmail, string $issuedAt, string $expiry)
	{
		$this->userId = $userId;
		$this->userName = $userName;
		$this->userEmail = $userEmail;
		$this->issuedAt = $issuedAt;
		$this->expiry = $expiry;
	}

	public function getUserId(): int
	{
		return $this->userId;
	}

	public function setUserId(int $userId): void
	{
		$this->userId = $userId;
	}

	public function getUserName(): string
	{
		return $this->userName;
	}

	public function setUserName(string $userName): void
	{
		$this->userName = $userName;
	}

	public function getUserEmail(): string
	{
		return $this->userEmail;
	}

	public function setUserEmail(string $userEmail): void
	{
		$this->userEmail = $userEmail;
	}

	public function getIssuedAt(): string
	{
		return $this->issuedAt;
	}

	public function setIssuedAt(string $issuedAt): void
	{
		$this->issuedAt = $issuedAt;
	}

	public function getExpiry(): string
	{
		return $this->expiry;
	}

	public function setExpiry(string $expiry): void
	{
		$this->expiry = $expiry;
	}


}
