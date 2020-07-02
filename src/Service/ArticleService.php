<?php
/*
 * Article Service.
 */

namespace App\Service;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ArticleService.
 */
class ArticleService
{
    /**
     * @var App\Repository\ArticleRepository
     */
    private $articleRepository;

    /**
     * @var Knp\Component\Pager\PaginatorInterface
     */
    private $paginator;

    /**
     * @var App\Service\FileUploader
     */
    private $fileUploader;

    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;

    /**
     * ArticleService constructor.
     */
    public function __construct(ArticleRepository $articleRepository, PaginatorInterface $paginator, FileUploader $fileUploader, Filesystem $fileSystem)
    {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
        $this->fileUploader = $fileUploader;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articleRepository->getPublishedArticles(),
            $page,
            ArticleRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save Article.
     *
     * @param UserInterface $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Article $article, UserInterface $user = null): void
    {
        if ($user instanceof UserInterface) {
            $article->setAuthor($user);
        }

        $this->articleRepository->save($article);
    }

    /**
     * Delete article.
     *
     * @param Article $article
     * @param string  $directory
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Article $article, string $directory): void
    {
        $this->fileSystem->remove(
            $directory.'/'.$article->getFileName()
        );
        $this->articleRepository->delete($article);
    }

    /**
     * set thumbnail.
     *
     * @param Article $article
     * @param $image
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setThumbnail(Article $article, $image, $directory = null): void
    {
        if($article->getFileName() && $directory){
            $this->fileSystem->remove(
                $directory.'/'.$article->getFileName()
            );
        }
        $article->setFileName($this->fileUploader->upload($image));
        $this->articleRepository->save($article);
    }

    /**
     * delete thumbnail.
     *
     * @param Article $article
     * @param string  $directory
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteThumbnail(Article $article, string $directory): void
    {
        $this->fileSystem->remove(
            $directory.'/'.$article->getFileName()
        );
        $article->setFileName(null);
        $this->articleRepository->save($article);
    }
}
