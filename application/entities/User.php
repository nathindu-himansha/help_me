<?php


namespace entities;
class User
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private int $id;
	private string $firstName;
	private string $lastName;
	private string $email;
	private string $password;

	function __construct($firstName, $lastName, $email, $password)
	{
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->password = $password;
	}


	public function getId(): int
	{
		return $this->id;
	}

	function setId($id)
	{
		$this->id = $id;
	}

	public function getFirstName(): string
	{
		return $this->firstName;
	}


	public function setFirstName($firstName): void
	{
		$this->firstName = $firstName;
	}


	public function getLastName(): string
	{
		return $this->lastName;
	}


	public function setLastName($lastName): void
	{
		$this->lastName = $lastName;
	}


	public function getEmail(): string
	{
		return $this->email;
	}


	public function setEmail($email): void
	{
		$this->email = $email;
	}


	public function getPassword(): string
	{
		return $this->password;
	}


	public function setPassword($password): void
	{
		$this->password = $password;
	}

	public function toString(): array
	{
		return array("firstName"=>$this->getFirstName(),"lastName"=>$this->getLastName(),"email" => $this->getEmail(), "password" => $this->getPassword());
	}

}
