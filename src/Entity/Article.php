<?php
/**
 * Article entity.
 */

namespace App\Entity;

use App\Repository\ArticleRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Article.
 *
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\Table(name="articles")
 *
 * @UniqueEntity(fields={"title"})
 */
class Article
{
    /**
     * Primary key.
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="3",
     *     max="255",
     * )
     */
    private $title;

    /**
     * Code.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     length=255
     * )
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *     min="3",
     *     max="255",
     * )
     *
     * @Gedmo\Slug(fields={"title"})
     */
    private $code;

    /**
     * Body.
     *
     * @ORM\Column(type="text", length=65535)
     *
     * @Assert\Type("string")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="3",
     *     max="65535",
     * )
     */
    private $body;

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
     * Updated at.
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\DateTime
     *
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Type(type="App\Entity\Category")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="article", orphanRemoval=true)
     *
     * @Assert\All({
     *   @Assert\Type(type="App\Entity\Comment")
     * })
     */
    private $comments;

    /**
     * Tags.
     *
     * @var array
     *
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="articles", orphanRemoval=true)
     *
     * @ORM\JoinTable(name="articles_tags")
     *
     * @Assert\All({
     *   @Assert\Type(type="App\Entity\Tag")
     * })
     */
    private $tags;

    /**
     * Author.
     *
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Type(type="App\Entity\User")
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type("boolean")
     */
    private $isPublished;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $fileName;

    /**
     * Article constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * Getter fot id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter fot title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
     * Getter for category.
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * Getter for comments.
     *
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Setter for comments.
     */
    public function addComment(Comment $comment): void
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }
    }

    /**
     * Remove Comment.
     */
    public function removeComment(Comment $comment): void
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }
    }

    /**
     * Getter for updatedAt.
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Getter for code.
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Setter for code.
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * Getter for tags.
     *
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Setter for tags.
     */
    public function addTag(Tag $tag): void
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * Setter for tags.
     */
    public function removeTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }
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

    /**
     * Getter for isPublished.
     */
    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * Setter for isPublished.
     */
    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }
}
