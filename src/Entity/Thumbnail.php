<?php
/**
 * Thumbnail
 */
namespace App\Entity;

use App\Repository\ThumbnailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Thumbnail.
 *
 * @ORM\Entity(repositoryClass=ThumbnailRepository::class)
 * @ORM\Table(
 *     name="thumbnails",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="UQ_filename_1",
 *              columns={"filename"},
 *          )
 *     }
 * )
 *
 * @UniqueEntity(fields={"filename"})
 */
class Thumbnail
{
    /**
     * Id.
     *
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Article.
     *
     * @var Article::class
     *
     * @ORM\OneToOne(targetEntity=Article::class, inversedBy="thumbnail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Type(type=Article::class)
     */
    private $article;

    /**
     * Filename.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Type(type="string")
     */
    private $filename;

    /**
     * Getter for id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for Article.
     *
     * @return Article|null
     */
    public function getArticle(): ?Article
    {
        return $this->article;
    }

    /**
     * Setter for article.
     *
     * @param Article $article
     */
    public function setArticle(Article $article): void
    {
        $this->article = $article;
    }

    /**
     * Getter for fillename.
     *
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Setter for filename.
     *
     * @param string $filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }
}
