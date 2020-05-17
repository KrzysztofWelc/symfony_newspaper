<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Form\CategoryType;
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
     * @Route("/show/{name}", name="category_show")
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

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request            HTTP request
     * @param \App\Repository\CategoryRepository        $categoryRepository Category repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="category_create",
     * )
     */
    public function create(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category);

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }
}
