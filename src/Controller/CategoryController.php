<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController.
 *
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @param \App\Repository\CategoryRepository Category repository
     *
     * @return Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route("/", name="category_index")
     */
    public function index(CategoryRepository $repository): Response
    {
        return $this->render(
            'category/index.html.twig',
            ['categories' => $repository->findAll()]
        );
    }

    /**
     * @param Knp\Component\Pager\PaginatorInterface $paginator Paginator interface
     * @param App\Entity\Category Category entity
     * @param Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route("/{name}", name="category_show")
     */
    public function show(Request $request, Category $category, PaginatorInterface $paginator): Response
    {
//        dump($category->getArticles()->getTitle);

        $pagination = $paginator->paginate(
            $category->getArticles(),
            $request->query->getInt('page', 1),
            3
        );

        return $this->render(
            'category/articlesList.html.twig',
            ['pagination' => $pagination]
        );
    }
}
