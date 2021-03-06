<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $ruName;

	/**
	 * @ORM\ManyToMany(targetEntity=Post::class, mappedBy="tags")
	 */
	private $posts;

	public function __construct()
	{
		$this->posts = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getRuName(): ?string
	{
		return $this->ruName;
	}

	public function setRuName(?string $ruName): self
	{
		$this->ruName = $ruName;

		return $this;
	}

	/**
	 * @return Collection|Post[]
	 */
	public function getPosts(): Collection
	{
		return $this->posts;
	}

	/**
	 * @return Post[]
	 */
	public function getRealizedPosts()
	{
		$time = time();
		$posts = [];
		/** @var Post $post */
		foreach ($this->posts as $post) {
			if ($post->getPublished() <= $time) $posts[] = $post->export();
		}
		return $posts;
	}

	public function addPost(Post $post): self
	{
		if (!$this->posts->contains($post)) {
			$this->posts[] = $post;
			$post->addTag($this);
		}

		return $this;
	}

	public function removePost(Post $post): self
	{
		if ($this->posts->contains($post)) {
			$this->posts->removeElement($post);
			$post->removeTag($this);
		}

		return $this;
	}

	public function export(): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'ru_name' => $this->ruName,
		];
	}
}
