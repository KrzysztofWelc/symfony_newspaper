<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param App\Entity\Category Category entity
     *
     * @return Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route("/{name}", name="category_show")
     */
    public function show(Category $category): Response
    {
        return $this->render(
            'category/articlesList.html.twig',
            ['category' => $category]
        );
    }
}
