<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\ArticleService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class ArticleController.
 *
 * @Route("/")
 */
class ArticleController extends AbstractController
{
    /**
     * @var App\Service\ArticleService Article Service
     */
    private $articleService;

    /**
     * @var Symfony\Contracts\Translation\TranslatorInterface Translator Interface.
     */
    private $translator;

    /**
     * ArticleController constructor.
     *
     * @param ArticleService      $articleService
     * @param TranslatorInterface $translator
     */
    public function __construct(ArticleService $articleService, TranslatorInterface $translator)
    {
        $this->articleService = $articleService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request    HTTP request
     * @param \App\Repository\ArticleRepository         $repository task repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route("/", name="article_index")
     */
    public function index(Request $request): Response
    {
        return $this->render(
            'article/index.html.twig',
            ['pagination' => $this->articleService->createPaginatedList($request->query->getInt('page', 1))]
        );
    }

    /**
     * Show action.
     *
     * @param \App\Entity\Article $article Article entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     * @Route(
     *     "/show/{id}",
     *     name="article_show",
     *     methods={"GET", "POST"}
     * )
     */
    public function show(Article $article): Response
    {
        return $this->render(
            'article/show.html.twig',
            [
                'article' => $article,
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
     *     name="article_create",
     * )
     *
     * @Security("is_granted('ROLE_REDACTOR')")
     */
    public function create(Request $request): Response
    {
        if (!$this->getUser()->getCanPublish()) {
            $this->addFlash('danger', $this->translator->trans('banned_msg'));

            return $this->redirectToRoute('article_index');
        }

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->save($article, $this->getUser());
            $this->addFlash('success', $this->translator->trans('article_created_msg'));

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Article                       $article Article entity
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
     *     name="article_edit",
     * )
     *
     * @Security("is_granted('ROLE_ADMIN') or ( is_granted('ROLE_REDACTOR') and is_granted('EDIT', article) )")
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->save($article, null);

            $this->addFlash('success', $this->translator->trans('article_updated_msg'));

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/edit.html.twig',
            [
                'form' => $form->createView(),
                'article' => $article,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Article                       $article Article entity
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
     *     name="article_delete",
     * )
     *
     * @Security("is_granted('ROLE_ADMIN') or ( is_granted('ROLE_REDACTOR') and is_granted('EDIT', article) )")
     */
    public function delete(Request $request, Article $article): Response
    {
        $form = $this->createForm(FormType::class, $article, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleService->delete($article);
            $this->addFlash('success', $this->translator->trans('article_deleted_msg'));

            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'article/delete.html.twig',
            [
                'form' => $form->createView(),
                'article' => $article,
            ]
        );
    }
}
