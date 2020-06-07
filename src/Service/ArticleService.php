<?php
/*
 * Article Service.
 */

namespace App\Service;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * ArticleService constructor.
     *
     * @param ArticleRepository $articleRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(ArticleRepository $articleRepository, PaginatorInterface $paginator, FileUploader $fileUploader)
    {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param int $page
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articleRepository->queryAll(),
            $page,
            ArticleRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save Article.
     *
     * @param Article $article
     * @param UserInterface $user
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Article $article, UserInterface $user = null, UploadedFile $image = null): void
    {
        if($user instanceof UserInterface){
            $article->setAuthor($user);
        }

        if($image){
            $this->fileUploader->upload($image, $article);
        }
        $this->articleRepository->save($article);
    }

    /**
     * Delete article.
     *
     * @param Article $article
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Article $article): void
    {
        $this->articleRepository->delete($article);
    }
}