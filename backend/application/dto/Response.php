<?php
// common class for send responses
namespace dto;
class Response
{

	private string $status;
	private string $message;
	private ?array $data;


	public function __construct(string $status, string $message, ?array $data)
	{
		$this->status = $status;
		$this->message = $message;
		$this->data = $data;
	}

	public function getStatus(): string
	{
		return $this->status;
	}


	public function setStatus(string $status): void
	{
		$this->status = $status;
	}


	public function getMessage(): string
	{
		return $this->message;
	}


	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	public function getData(): ?array
	{
		return $this->data;
	}


	public function setData(?array $data): void
	{
		$this->data = $data;
	}

	public function toString(): array
	{
		return array("status" => $this->getStatus(), "message" => $this->getMessage(), "data" => $this->getData());

	}

}
