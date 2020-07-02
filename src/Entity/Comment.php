<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Type(type="App\Entity\Article")
     */
    private $article;

    /**
     * Created at.
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\DateTime
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * Getter for id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for body.
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Setter for body.
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * Getter for article.
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Setter for article.
     */
    public function setArticle(?Article $article): void
    {
        $this->article = $article;
    }

    /**
     * Getter for createdAt.
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for createdAt.
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for author.
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }
}
