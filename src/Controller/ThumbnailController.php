<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ThumbnailType;
use App\Service\ArticleService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ThumbnailController.
 *
 * @Route("/thumbnail")
 */
class ThumbnailController extends AbstractController
{
    /**
     * @var App\Service\ArticleService
     */
    private $articleService;

    /**
     * @var Symfony\Contracts\Translation\TranslatorInterface Translator Interface.
     */
    private $translator;

    /**
     * ThumbnailController constructor.
     */
    public function __construct(ArticleService $articleService, TranslatorInterface $translator)
    {
        $this->articleService = $articleService;
        $this->translator = $translator;
    }

    /**
     * show action.
     *
     * @Route("/show/{title}", name="thumbnail_show")
     */
    public function show(Article $article)
    {
        return $this->render('thumbnail/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * add action.
     *
     * @Route("/add/{id}", name="thumbnail_add")
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Request $request, Article $article): Response
    {
        $form = $this->createForm(ThumbnailType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('file')->getData();
            $this->articleService->setThumbnail($article, $image);
            $this->addFlash('success', $this->translator->trans('thumbnail_added_msg'));


            return $this->redirectToRoute('article_index');
        }

        return $this->render('thumbnail/add.html.twig', [
                'form' => $form->createView(),
                'id' => $article->getId(),
            ]
        );
    }

    /**
     * edit action.
     *
     * @Route("/edit/{id}", name="thumbnail_edit")
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ThumbnailType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('file')->getData();
            $imagesDirectory = $this->getParameter('avatars_directory');
            $this->articleService->setThumbnail($article, $image, $imagesDirectory);
            $this->addFlash('success', $this->translator->trans('thumbnail_edited_msg'));


            return $this->redirectToRoute('article_index');
        }

        return $this->render('thumbnail/edit.html.twig', [
                'form' => $form->createView(),
                'id' => $article->getId(),
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
     *     name="thumbnail_delete",
     * )
     *
     */
    public function delete(Request $request, Article $article): Response
    {
        $form = $this->createForm(FormType::class, null, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $imagesDirectory = $this->getParameter('avatars_directory');
            $this->articleService->deleteThumbnail($article, $imagesDirectory);
            $this->addFlash('success', $this->translator->trans('thumbnail_deleted_msg'));


            return $this->redirectToRoute('article_index');
        }

        return $this->render(
            'thumbnail/delete.html.twig',
            [
                'form' => $form->createView(),
                'article' => $article,
            ]
        );
    }
}
