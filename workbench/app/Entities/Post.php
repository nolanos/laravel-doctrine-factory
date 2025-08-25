<?php

namespace Workbench\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Entity, Table(name: 'posts')]
class Post
{
    use HasFactory;

    #[Id, GeneratedValue, Column(type: 'integer')]
    protected $id;

    #[Column(type: 'string')]
    protected $title;

    #[Column(type: 'boolean')]
    protected bool $published = false;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    protected ?User $user = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'secondaryPosts')]
    protected ?User $secondaryAuthor = null;

    #[ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
    #[JoinTable(name: 'post_tags')]
    protected Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getSecondaryAuthor(): ?User
    {
        return $this->secondaryAuthor;
    }

    public function setSecondaryAuthor($user)
    {
        $this->secondaryAuthor = $user;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
    }

    public function removeTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
    }
}
