<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryController.
 *
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @var App\Service\CategoryService
     */
    private $categoryService;

    /**
     * @var Symfony\Contracts\Translation\TranslatorInterface translator Interface
     */
    private $translator;

    /**
     * CategoryController constructor.
     *
     * @param CategoryService     $categoryService Category service
     * @param TranslatorInterface $translator
     */
    public function __construct(CategoryService $categoryService, TranslatorInterface $translator)
    {
        $this->categoryService = $categoryService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @return Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route("/", name="category_index")
     */
    public function index(): Response
    {
        return $this->render(
            'category/index.html.twig',
            ['categories' => $this->categoryService->createList()]
        );
    }

    /**
     * Show action.
     *
     * @Route("/show/{name}", name="category_show")
     *
     * @param Request  $request
     * @param Category $category
     *
     * @return Response
     */
    public function show(Request $request, Category $category): Response
    {
        $pagination = $this->categoryService->createPaginatedListOfArticles(
            $category,
            $request->query->getInt('page', 1)
        );

        return $this->render(
            'category/articlesList.html.twig',
            [
                'pagination' => $pagination,
                'category' => $category,
            ]
        );
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
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
     *
     * @isGranted("ROLE_ADMIN")
     */
    public function create(Request $request): Response
    {
        if (!$this->getUser()->getCanPublish()) {
            $this->addFlash('danger', $this->translator->trans('banned_msg'));

            return $this->redirectToRoute('article_index');
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);
            $this->addFlash('success', $this->translator->trans('category_created_msg'));

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request  HTTP request
     * @param \App\Entity\Category                      $category Category entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/delete/{id}",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="category_delete",
     * )
     *
     * @isGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Category $category): Response
    {
        $form = $this->createForm(FormType::class, $category, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);
            $this->addFlash('success', $this->translator->trans('category_deleted_msg'));

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request  HTTP request
     * @param \App\Entity\Category                      $category Article entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/edit/{id}",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="category_edit",
     * )
     *
     * @isGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);
            $this->addFlash('success', $this->translator->trans('category_updated_msg'));

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }
}
