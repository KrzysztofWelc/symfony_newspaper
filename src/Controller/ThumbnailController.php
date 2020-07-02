<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ThumbnailType;
use App\Service\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * ThumbnailController constructor.
     *
     * @param ArticleService $articleService
     */
    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
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
     * @param Request $request
     * @param Article $article
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(Request $request, Article $article): Response
    {
        $form = $this->createForm(ThumbnailType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('file')->getData();
            $this->articleService->setThumbnail($article, $image);

            return $this->redirectToRoute('article_index');
        }

        return $this->render('thumbnail/add.html.twig', [
                'form' => $form->createView(),
                'id' => $article->getId()
            ]
        );
    }
}
