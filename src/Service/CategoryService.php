<?php
/*
 * Category controller.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService
{
    /**
     * Paginator interface.
     * @var Knp\Component\Pager\PaginatorInterface
     */
    private $paginator;

    /**
     * Category Repository.
     *
     * @var App\Repository\CategoryRepository;
     */
    private $categoryRepository;

    /**
     * CategoryService constructor.
     *
     * @param CategoryRepository $categoryRepository Category repository
     * @param PaginatorInterface $paginator          Paginator interface
     */
    public function __construct(CategoryRepository $categoryRepository, PaginatorInterface $paginator)
    {
        $this->categoryRepository = $categoryRepository;
        $this->paginator = $paginator;
    }

    /**
     * Create list of categories.
     * @return array List of categories
     */
    public function createList(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * Create paginated list.
     *
     * @param Category $category Category entity
     * @param int      $page     page index
     *
     * @return PaginationInterface
     */
    public function createPaginatedListOfArticles(Category $category, int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $category->getArticles(),
            $page,
            3
        );
    }

    /**
     * Save action.
     *
     * @param Category $category Category Entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    /**
     * Delete action.
     * @param Category $category Category entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }
}
