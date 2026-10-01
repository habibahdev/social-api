<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'comment')]
class Comment
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Groups(['get'])]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['get'])]
    private string $message;

    #[ORM\Column(type: "datetime_immutable")]
    #[Groups(['get'])]
    private \DateTimeInterface $publishedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['get'])]
    private User $author;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    private Post $post;

    public function __construct()
    {
        $this->publishedAt = new \DateTimeImmutable();
    }

    /**
     * factory method pour créer et instancié directement un commentaire
     *
     * @param string $message
     * @param User $author
     * @param Post $post
     * @return self
     */
    public static function create(string $message, User $author, Post $post): self
    {
        $comment = new self();
        $comment->message = $message;
        $comment->author = $author;
        $comment->post = $post;
        return $comment;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
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

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }
}