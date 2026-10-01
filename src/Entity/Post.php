<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'post')]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Groups(['get'])]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['get'])]
    private string $content;

    #[ORM\Column(type: "datetime_immutable")]
    #[Groups(['get'])]
    private \DateTimeInterface $publishedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['get'])]
    private User $author;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'post_likes')]
    #[Groups(['get'])]
    private Collection $likedBy;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
        $this->likedBy = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getPublishedAt(): \DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getLikedBy(): Collection
    {
        return $this->likedBy;
    }

    public function likeBy(User $user): void
    {
        if ($this->likedBy->contains($user)) {
            return;
        }
        $this->likedBy->add($user);
    }

    public function dislikeBy(User $user): void
    {
        if (!$this->likedBy->contains($user)) {
            return;
        }
        $this->likedBy->removeElement($user);
    }

    /**
     * factory method pour créer et instancié directement un post
     *
     * @param string $content
     * @param User $author
     * @return self
     */
    public static function create(string $content, User $author): self
    {
        $post = new self();
        $post->content = $content;
        $post->author = $author;
        return $post;
    }
}