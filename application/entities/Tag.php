<?php

namespace entities;
class Tag
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private int $id;
	private string $tag;

	public function __construct(int $id, string $tag)
	{
		$this->id = $id;
		$this->tag = $tag;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getTag(): string
	{
		return $this->tag;
	}

	public function setTag(string $tag): void
	{
		$this->tag = $tag;
	}

	public function toString(): array
	{
		return array("id" => $this->getId(), "tag" => $this->getTag());
	}

}
